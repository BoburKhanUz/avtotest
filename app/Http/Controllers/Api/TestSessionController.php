<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
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
        $test = Test::findOrFail($validated['test_id']);

        $session = TestSession::create([
            'user_id' => $user->id,
            'test_id' => $test->id,
            'started_at' => now(),
            'time_limit' => $test->time_limit,
            'status' => 'in_progress',
        ]);

        return response()->json($session, 201);
    }

    public function submit(Request $request, TestSession $session)
    {
        // Foydalanuvchi faqat o'z sessiyasini topshirishi mumkinligini tekshirish
        if ($session->user_id !== $request->user()->id) {
            return response()->json(['error' => 'Unauthorized'], 403);
        }

        $validated = $request->validate([
            'answers' => 'required|array',
        ]);

        if ($session->status !== 'in_progress') {
            return response()->json(['error' => 'Session is not active'], 400);
        }

        if ($session->isTimeExpired()) {
            $session->update([
                'ended_at' => now(),
                'status' => 'completed',
                'score' => 0,
                'user_answers' => $validated['answers']
            ]);
            return response()->json(['error' => 'Vaqt tugadi', 'session' => $session], 400);
        }

        $test = $session->test;
        $questions = $test->questions;
        $score = 0;
        $adaptiveLearning = app(AdaptiveLearningService::class);

        foreach ($questions as $question) {
            $userAnswer = $validated['answers'][$question->id] ?? null;
            $isCorrect = $userAnswer && $userAnswer === $question->correct_option;

            if ($isCorrect) {
                $score++;
            }

            // Adaptive Learning: Har bir javobni qayd qilish
            $adaptiveLearning->recordAnswer($session->user_id, $question->id, $isCorrect);
        }

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
            'total_questions' => $test->question_count,
            'completed_at' => now(),
        ]);

        return response()->json(['session' => $session, 'score' => $score]);
    }
}