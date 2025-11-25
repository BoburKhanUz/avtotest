<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Http\Responses\ApiResponse;
use App\Models\Test;
use App\Models\TestResult;
use App\Models\TestSession;
use App\Services\AdaptiveLearningService;
use Illuminate\Http\Request;

class TestSessionController extends Controller
{
    public function start(Request $request)
    {
        $validated = $request->validate([
            'test_id' => 'required|exists:tests,id',
        ]);

        $user = $request->user();
        $test = Test::with('questions')->findOrFail($validated['test_id']);

        // Check if user has active subscription (optional)
        // if (!$user->hasActiveSubscription()) {
        //     return ApiResponse::error('Obuna talab qilinadi', null, 403);
        // }

        $session = TestSession::create([
            'user_id' => $user->id,
            'test_id' => $test->id,
            'started_at' => now(),
            'time_limit' => $test->time_limit,
            'status' => 'in_progress',
        ]);

        return ApiResponse::success([
            'session' => [
                'id' => $session->id,
                'started_at' => $session->started_at,
                'time_limit' => $session->time_limit,
                'remaining_time' => $session->getRemainingTime(),
                'status' => $session->status,
            ],
            'test' => [
                'id' => $test->id,
                'name' => $test->name_uz,
                'total_questions' => $test->questions->count(),
                'time_limit' => $test->time_limit,
            ],
        ], 'Test muvaffaqiyatli boshlandi', 201);
    }

    public function submit(Request $request, TestSession $session)
    {
        // Foydalanuvchi faqat o'z sessiyasini topshirishi mumkinligini tekshirish
        if ($session->user_id !== $request->user()->id) {
            return ApiResponse::unauthorized('Bu sessiya sizga tegishli emas');
        }

        $validated = $request->validate([
            'answers' => 'required|array',
        ]);

        if ($session->status !== 'in_progress') {
            return ApiResponse::error('Test allaqachon yakunlangan', null, 400);
        }

        if ($session->isTimeExpired()) {
            $session->update([
                'ended_at' => now(),
                'status' => 'completed',
                'score' => 0,
                'user_answers' => $validated['answers']
            ]);
            return ApiResponse::error('Vaqt tugadi', [
                'session' => $session,
                'score' => 0,
                'percentage' => 0,
            ], 400);
        }

        $test = $session->test;
        $questions = $test->questions;
        $score = 0;
        $correctAnswers = [];
        $incorrectAnswers = [];
        $adaptiveLearning = app(AdaptiveLearningService::class);

        foreach ($questions as $question) {
            $userAnswer = $validated['answers'][$question->id] ?? null;
            $isCorrect = $userAnswer && $userAnswer === $question->correct_option;

            if ($isCorrect) {
                $score++;
                $correctAnswers[] = $question->id;
            } else {
                $incorrectAnswers[] = [
                    'question_id' => $question->id,
                    'user_answer' => $userAnswer,
                    'correct_answer' => $question->correct_option,
                ];
            }

            // Adaptive Learning: Har bir javobni qayd qilish
            $adaptiveLearning->recordAnswer($session->user_id, $question->id, $isCorrect);
        }

        $totalQuestions = $questions->count();
        $percentage = $totalQuestions > 0 ? ($score / $totalQuestions) * 100 : 0;
        $passed = $percentage >= 70; // 70% o'tish balli

        $session->update([
            'user_answers' => $validated['answers'],
            'score' => $score,
            'ended_at' => now(),
            'status' => 'completed',
        ]);

        TestResult::create([
            'user_id' => $session->user_id,
            'test_id' => $session->test_id,
            'score' => $score,
            'total_questions' => $totalQuestions,
            'completed_at' => now(),
        ]);

        return ApiResponse::success([
            'session' => [
                'id' => $session->id,
                'started_at' => $session->started_at,
                'ended_at' => $session->ended_at,
                'duration' => $session->started_at->diffInMinutes($session->ended_at),
                'status' => $session->status,
            ],
            'result' => [
                'score' => $score,
                'total_questions' => $totalQuestions,
                'correct_answers' => count($correctAnswers),
                'incorrect_answers' => count($incorrectAnswers),
                'percentage' => round($percentage, 2),
                'passed' => $passed,
            ],
            'incorrect_details' => $incorrectAnswers,
        ], 'Test muvaffaqiyatli yakunlandi');
    }

    /**
     * Get session status (for real-time timer)
     */
    public function status($sessionId)
    {
        $session = TestSession::findOrFail($sessionId);

        if ($session->user_id !== auth()->id()) {
            return ApiResponse::unauthorized();
        }

        return ApiResponse::success([
            'session' => [
                'id' => $session->id,
                'status' => $session->status,
                'started_at' => $session->started_at,
                'time_limit' => $session->time_limit,
                'remaining_time' => $session->getRemainingTime(),
                'is_expired' => $session->isTimeExpired(),
            ],
        ]);
    }
}