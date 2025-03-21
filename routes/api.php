<?php

use App\Http\Controllers\Api\AuthController;
use App\Http\Controllers\Api\TestController;
use App\Http\Controllers\Api\SubscriptionController;
use App\Http\Controllers\Api\ReportController;
use App\Http\Controllers\Api\TestSessionController;
use L5Swagger\Http\Controllers\SwaggerController;

Route::post('/register', [AuthController::class, 'register']);
Route::post('/login', [AuthController::class, 'login']);
Route::get('documentation', [SwaggerController::class, 'api'])->name('l5-swagger.api');
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
});