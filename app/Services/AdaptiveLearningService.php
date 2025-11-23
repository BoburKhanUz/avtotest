<?php

namespace App\Services;

use App\Models\User;
use App\Models\Question;
use App\Models\UserQuestionAnalytic;
use App\Models\UserCategoryAnalytic;
use App\Models\RecommendedQuestion;
use Illuminate\Support\Facades\DB;

class AdaptiveLearningService
{
    /**
     * Foydalanuvchi javobini qayd qilish va tahlil qilish
     */
    public function recordAnswer($userId, $questionId, $isCorrect)
    {
        $analytic = UserQuestionAnalytic::firstOrCreate(
            ['user_id' => $userId, 'question_id' => $questionId],
            [
                'correct_count' => 0,
                'incorrect_count' => 0,
                'total_attempts' => 0,
                'success_rate' => 0,
            ]
        );

        // Statistikani yangilash
        if ($isCorrect) {
            $analytic->correct_count++;
        } else {
            $analytic->incorrect_count++;
        }

        $analytic->total_attempts++;
        $analytic->success_rate = $analytic->calculateSuccessRate();
        $analytic->last_attempt_at = now();
        $analytic->save();

        $analytic->checkMastery();

        // Kategoriya statistikasini yangilash
        $question = Question::with('test.category')->find($questionId);
        if ($question && $question->test && $question->test->category) {
            $this->updateCategoryAnalytics($userId, $question->test->category_id);
        }

        // Agar noto'g'ri javob bo'lsa, o'xshash savollarni tavsiya qilish
        if (!$isCorrect) {
            $this->generateRecommendations($userId, $questionId);
        }

        return $analytic;
    }

    /**
     * Kategoriya statistikasini yangilash
     */
    public function updateCategoryAnalytics($userId, $categoryId)
    {
        $categoryAnalytic = UserCategoryAnalytic::firstOrCreate(
            ['user_id' => $userId, 'category_id' => $categoryId]
        );

        // Kategoriya bo'yicha barcha savollar statistikasini hisoblash
        $stats = UserQuestionAnalytic::where('user_id', $userId)
            ->whereHas('question.test', function ($query) use ($categoryId) {
                $query->where('category_id', $categoryId);
            })
            ->selectRaw('COUNT(*) as total, SUM(CASE WHEN mastered = 1 THEN 1 ELSE 0 END) as mastered, AVG(success_rate) as avg_rate')
            ->first();

        $categoryAnalytic->total_questions = $stats->total ?? 0;
        $categoryAnalytic->mastered_questions = $stats->mastered ?? 0;
        $categoryAnalytic->average_success_rate = $stats->avg_rate ?? 0;

        if ($categoryAnalytic->total_questions > 0) {
            $categoryAnalytic->category_progress = ($categoryAnalytic->mastered_questions / $categoryAnalytic->total_questions) * 100;
        }

        $categoryAnalytic->save();

        return $categoryAnalytic;
    }

    /**
     * O'xshash savollarni tavsiya qilish
     */
    public function generateRecommendations($userId, $questionId)
    {
        $question = Question::with('test')->find($questionId);

        if (!$question) {
            return;
        }

        // 1. Bir xil testdan boshqa savollar
        $similarQuestions = Question::where('test_id', $question->test_id)
            ->where('id', '!=', $questionId)
            ->whereNotIn('id', function ($query) use ($userId) {
                $query->select('question_id')
                    ->from('user_question_analytics')
                    ->where('user_id', $userId)
                    ->where('mastered', true);
            })
            ->limit(5)
            ->get();

        foreach ($similarQuestions as $similarQuestion) {
            RecommendedQuestion::updateOrCreate(
                [
                    'user_id' => $userId,
                    'question_id' => $similarQuestion->id,
                ],
                [
                    'recommendation_type' => 'similar_question',
                    'priority' => 10,
                    'reason' => "Siz '{$question->content_uz}' savoliga noto'g'ri javob berdingiz. Ushbu savol o'xshash mavzuga tegishli.",
                    'completed' => false,
                ]
            );
        }

        // 2. Eng zaif tomonlarni aniqlash
        $weakQuestions = UserQuestionAnalytic::where('user_id', $userId)
            ->where('success_rate', '<', 50)
            ->where('total_attempts', '>=', 2)
            ->orderBy('success_rate', 'asc')
            ->limit(10)
            ->get();

        foreach ($weakQuestions as $weakAnalytic) {
            RecommendedQuestion::updateOrCreate(
                [
                    'user_id' => $userId,
                    'question_id' => $weakAnalytic->question_id,
                ],
                [
                    'recommendation_type' => 'weak_area',
                    'priority' => 15,
                    'reason' => "Bu savolda sizning natijangiz {$weakAnalytic->success_rate}%. Qo'shimcha mashq qilish tavsiya etiladi.",
                    'completed' => false,
                ]
            );
        }
    }

    /**
     * Foydalanuvchi uchun maxsus test yaratish
     */
    public function generatePersonalizedTest($userId, $questionCount = 20)
    {
        // 1. Zaif tomonlar (50%)
        $weakQuestions = RecommendedQuestion::where('user_id', $userId)
            ->where('completed', false)
            ->where('recommendation_type', 'weak_area')
            ->orderBy('priority', 'desc')
            ->take(intval($questionCount * 0.5))
            ->pluck('question_id');

        // 2. O'xshash savollar (30%)
        $similarQuestions = RecommendedQuestion::where('user_id', $userId)
            ->where('completed', false)
            ->where('recommendation_type', 'similar_question')
            ->orderBy('priority', 'desc')
            ->take(intval($questionCount * 0.3))
            ->pluck('question_id');

        // 3. Yangi savollar (20%)
        $attemptedQuestionIds = UserQuestionAnalytic::where('user_id', $userId)
            ->pluck('question_id');

        $newQuestions = Question::whereNotIn('id', $attemptedQuestionIds)
            ->inRandomOrder()
            ->take(intval($questionCount * 0.2))
            ->pluck('id');

        // Barcha savollarni birlashtirish
        $allQuestionIds = $weakQuestions
            ->concat($similarQuestions)
            ->concat($newQuestions)
            ->unique()
            ->shuffle();

        return Question::whereIn('id', $allQuestionIds)->get();
    }

    /**
     * Foydalanuvchi progressini olish
     */
    public function getUserProgress($userId)
    {
        // Umumiy statistika
        $totalAttempts = UserQuestionAnalytic::where('user_id', $userId)->sum('total_attempts');
        $totalMastered = UserQuestionAnalytic::where('user_id', $userId)->where('mastered', true)->count();
        $totalQuestions = UserQuestionAnalytic::where('user_id', $userId)->count();
        $averageSuccessRate = UserQuestionAnalytic::where('user_id', $userId)->avg('success_rate');

        // Kategoriya bo'yicha progress
        $categoryProgress = UserCategoryAnalytic::where('user_id', $userId)
            ->with('category')
            ->get();

        // Zaif tomonlar
        $weakAreas = UserQuestionAnalytic::where('user_id', $userId)
            ->where('success_rate', '<', 60)
            ->with('question.test.category')
            ->orderBy('success_rate', 'asc')
            ->limit(10)
            ->get();

        // Kuchli tomonlar
        $strongAreas = UserQuestionAnalytic::where('user_id', $userId)
            ->where('mastered', true)
            ->with('question.test.category')
            ->orderBy('success_rate', 'desc')
            ->limit(10)
            ->get();

        return [
            'overview' => [
                'total_attempts' => $totalAttempts,
                'total_mastered' => $totalMastered,
                'total_questions' => $totalQuestions,
                'average_success_rate' => round($averageSuccessRate, 2),
                'mastery_percentage' => $totalQuestions > 0 ? round(($totalMastered / $totalQuestions) * 100, 2) : 0,
            ],
            'category_progress' => $categoryProgress,
            'weak_areas' => $weakAreas,
            'strong_areas' => $strongAreas,
        ];
    }

    /**
     * Tavsiya etilgan savollarni olish
     */
    public function getRecommendations($userId, $limit = 10)
    {
        return RecommendedQuestion::where('user_id', $userId)
            ->where('completed', false)
            ->with('question')
            ->orderBy('priority', 'desc')
            ->limit($limit)
            ->get();
    }
}
