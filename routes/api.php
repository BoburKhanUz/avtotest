<?php

use App\Http\Controllers\Api\AuthController;
use App\Http\Controllers\Api\TestController;
use App\Http\Controllers\Api\SubscriptionController;
use App\Http\Controllers\Api\ReportController;
use App\Http\Controllers\Api\TestSessionController;
use App\Http\Controllers\Api\PaymeController;
use App\Http\Controllers\Api\ClickController;
use App\Http\Controllers\Api\AdaptiveLearningController;
use L5Swagger\Http\Controllers\SwaggerController;

Route::post('/register', [AuthController::class, 'register']);
Route::post('/login', [AuthController::class, 'login']);
Route::get('documentation', [SwaggerController::class, 'api'])->name('l5-swagger.api');

// Payme webhook (avtorizatsiyasiz)
Route::post('/payme/handle', [PaymeController::class, 'handle']);

// Click webhooks (avtorizatsiyasiz)
Route::post('/click/prepare', [ClickController::class, 'prepare']);
Route::post('/click/complete', [ClickController::class, 'complete']);
Route::middleware('auth:sanctum')->group(function () {
    Route::get('/user', [AuthController::class, 'user']);
    Route::apiResource('tests', TestController::class);
    Route::get('/subscriptions', [SubscriptionController::class, 'index']);
    Route::post('/subscriptions', [SubscriptionController::class, 'store']);
    Route::get('/reports/users', [ReportController::class, 'userReport']);
    Route::get('/reports/subscriptions', [ReportController::class, 'subscriptionReport']);
    Route::get('/reports/test-results', [ReportController::class, 'testResultReport']);
    Route::post('/test-sessions/start', [TestSessionController::class, 'start']);
    Route::post('/test-sessions/{session}/submit', [TestSessionController::class, 'submit']);
    Route::get('/reports/user-stats', [ReportController::class, 'userStats']);
    Route::put('/profile', [AuthController::class, 'updateProfile']);

    // To'lov tizimlari
    Route::post('/payments/payme/create', [PaymeController::class, 'createPayment']);
    Route::post('/payments/click/create', [ClickController::class, 'createPayment']);

    // Adaptive Learning
    Route::prefix('learning')->group(function () {
        Route::get('/progress', [AdaptiveLearningController::class, 'getProgress']);
        Route::get('/recommendations', [AdaptiveLearningController::class, 'getRecommendations']);
        Route::post('/personalized-test', [AdaptiveLearningController::class, 'generatePersonalizedTest']);
        Route::get('/question-stats/{questionId}', [AdaptiveLearningController::class, 'getQuestionStats']);
    });
});