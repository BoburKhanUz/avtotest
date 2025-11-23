<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Services\AdaptiveLearningService;
use Illuminate\Http\Request;

class AdaptiveLearningController extends Controller
{
    private $adaptiveLearningService;

    public function __construct(AdaptiveLearningService $adaptiveLearningService)
    {
        $this->adaptiveLearningService = $adaptiveLearningService;
    }

    /**
     * Foydalanuvchi progressini olish
     *
     * @OA\Get(
     *     path="/api/learning/progress",
     *     summary="Foydalanuvchi o'quv progressini olish",
     *     tags={"Adaptive Learning"},
     *     security={{"sanctum": {}}},
     *     @OA\Response(
     *         response=200,
     *         description="O'quv progress ma'lumotlari",
     *         @OA\JsonContent(
     *             @OA\Property(property="overview", type="object"),
     *             @OA\Property(property="category_progress", type="array", @OA\Items(type="object")),
     *             @OA\Property(property="weak_areas", type="array", @OA\Items(type="object")),
     *             @OA\Property(property="strong_areas", type="array", @OA\Items(type="object"))
     *         )
     *     )
     * )
     */
    public function getProgress(Request $request)
    {
        $userId = $request->user()->id;
        $progress = $this->adaptiveLearningService->getUserProgress($userId);

        return response()->json($progress);
    }

    /**
     * Tavsiya etilgan savollarni olish
     *
     * @OA\Get(
     *     path="/api/learning/recommendations",
     *     summary="Tavsiya etilgan savollar ro'yxati",
     *     tags={"Adaptive Learning"},
     *     security={{"sanctum": {}}},
     *     @OA\Parameter(
     *         name="limit",
     *         in="query",
     *         description="Maksimal savollar soni",
     *         required=false,
     *         @OA\Schema(type="integer", example=10)
     *     ),
     *     @OA\Response(
     *         response=200,
     *         description="Tavsiya etilgan savollar",
     *         @OA\JsonContent(type="array", @OA\Items(type="object"))
     *     )
     * )
     */
    public function getRecommendations(Request $request)
    {
        $userId = $request->user()->id;
        $limit = $request->input('limit', 10);

        $recommendations = $this->adaptiveLearningService->getRecommendations($userId, $limit);

        return response()->json($recommendations);
    }

    /**
     * Maxsus test yaratish
     *
     * @OA\Post(
     *     path="/api/learning/personalized-test",
     *     summary="Foydalanuvchi uchun maxsus test yaratish",
     *     tags={"Adaptive Learning"},
     *     security={{"sanctum": {}}},
     *     @OA\RequestBody(
     *         required=false,
     *         @OA\JsonContent(
     *             @OA\Property(property="question_count", type="integer", example=20)
     *         )
     *     ),
     *     @OA\Response(
     *         response=200,
     *         description="Maxsus test savollari",
     *         @OA\JsonContent(type="array", @OA\Items(type="object"))
     *     )
     * )
     */
    public function generatePersonalizedTest(Request $request)
    {
        $userId = $request->user()->id;
        $questionCount = $request->input('question_count', 20);

        $questions = $this->adaptiveLearningService->generatePersonalizedTest($userId, $questionCount);

        return response()->json([
            'questions' => $questions,
            'total_count' => $questions->count(),
            'composition' => [
                'weak_areas' => '50%',
                'similar_questions' => '30%',
                'new_questions' => '20%',
            ]
        ]);
    }

    /**
     * Savol bo'yicha statistika
     *
     * @OA\Get(
     *     path="/api/learning/question-stats/{questionId}",
     *     summary="Savol bo'yicha foydalanuvchi statistikasi",
     *     tags={"Adaptive Learning"},
     *     security={{"sanctum": {}}},
     *     @OA\Parameter(
     *         name="questionId",
     *         in="path",
     *         description="Savol ID",
     *         required=true,
     *         @OA\Schema(type="integer")
     *     ),
     *     @OA\Response(
     *         response=200,
     *         description="Savol statistikasi",
     *         @OA\JsonContent(type="object")
     *     )
     * )
     */
    public function getQuestionStats(Request $request, $questionId)
    {
        $userId = $request->user()->id;

        $stats = \App\Models\UserQuestionAnalytic::where('user_id', $userId)
            ->where('question_id', $questionId)
            ->with('question')
            ->first();

        if (!$stats) {
            return response()->json([
                'message' => 'Bu savolda hali urinish yo\'q',
                'attempted' => false
            ]);
        }

        return response()->json([
            'attempted' => true,
            'correct_count' => $stats->correct_count,
            'incorrect_count' => $stats->incorrect_count,
            'total_attempts' => $stats->total_attempts,
            'success_rate' => $stats->success_rate,
            'mastered' => $stats->mastered,
            'last_attempt_at' => $stats->last_attempt_at,
        ]);
    }
}
