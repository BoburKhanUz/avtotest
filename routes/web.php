<?php

use App\Http\Controllers\Auth\LoginController;
use App\Http\Controllers\Admin\AdminController;
use App\Http\Controllers\Admin\CategoryController;
use App\Http\Controllers\Admin\PlanController;
use App\Http\Controllers\Admin\PromocodeController;
use App\Http\Controllers\Admin\QuestionController;
use App\Http\Controllers\Admin\TestController as AdminTestController;
use App\Http\Controllers\Admin\UserController as AdminUserController;
use App\Http\Controllers\Admin\ProfileController;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Route;

// Auth route‘lari
Route::get('/login', [LoginController::class, 'showLoginForm'])->name('login');
Route::post('/login', [LoginController::class, 'login']);
Route::post('/logout', [LoginController::class, 'logout'])->name('logout');

// Asosiy sahifa uchun route
Route::get('/', function () {
    return redirect()->route('login'); // Asosiy sahifadan login sahifasiga yonaltirish
})->name('home');

// Home route‘ini admin uchun dashboard‘ga redirect qilish
Route::get('/home', function () {
    if (Auth::check() && Auth::user()->is_admin) {
        return redirect()->route('admin.dashboard'); // Admin uchun dashboard‘ga yonaltirish
    }
    return redirect()->route('login'); // Auth bo‘lmagan foydalanuvchilar uchun login sahifasiga yonaltirish
})->name('home');

// Admin route‘lari
Route::middleware(['auth', 'admin'])->prefix('admin')->group(function () {
    Route::get('/dashboard', [AdminController::class, 'dashboard'])->name('admin.dashboard');
    Route::get('/stats', [AdminController::class, 'stats'])->name('admin.stats');
    Route::get('/api-docs', [AdminController::class, 'apiDocs'])->name('admin.api-docs');

    Route::resource('tests', AdminTestController::class)->names([
        'index' => 'admin.tests.index',
        'create' => 'admin.tests.create',
        'store' => 'admin.tests.store',
        'show' => 'admin.tests.show',
        'edit' => 'admin.tests.edit',
        'update' => 'admin.tests.update',
        'destroy' => 'admin.tests.destroy',
    ]);

    Route::resource('users', ProfileController::class)->names([
        'index' => 'admin.users.index',
        'create' => 'admin.users.create',
        'store' => 'admin.users.store',
        'show' => 'admin.users.show',
        'edit' => 'admin.users.edit',
        'update' => 'admin.users.update',
        'destroy' => 'admin.users.destroy',
    ]);

    Route::resource('categories', CategoryController::class)->names([
        'index' => 'admin.categories.index',
        'create' => 'admin.categories.create',
        'store' => 'admin.categories.store',
        'show' => 'admin.categories.show',
        'edit' => 'admin.categories.edit',
        'update' => 'admin.categories.update',
        'destroy' => 'admin.categories.destroy',
    ]);

    Route::resource('plans', PlanController::class)->names([
        'index' => 'admin.plans.index',
        'create' => 'admin.plans.create',
        'store' => 'admin.plans.store',
        'show' => 'admin.plans.show',
        'edit' => 'admin.plans.edit',
        'update' => 'admin.plans.update',
        'destroy' => 'admin.plans.destroy',
    ]);

    Route::resource('promocodes', PromocodeController::class)->names([
        'index' => 'admin.promocodes.index',
        'create' => 'admin.promocodes.create',
        'store' => 'admin.promocodes.store',
        'show' => 'admin.promocodes.show',
        'edit' => 'admin.promocodes.edit',
        'update' => 'admin.promocodes.update',
        'destroy' => 'admin.promocodes.destroy',
    ]);

    Route::resource('tests.questions', QuestionController::class)->except(['show'])->names([
        'index' => 'admin.questions.index',
        'create' => 'admin.questions.create',
        'store' => 'admin.questions.store',
        'edit' => 'admin.questions.edit',
        'update' => 'admin.questions.update',
        'destroy' => 'admin.questions.destroy',
    ]);

    Route::post('tests/{test}/questions/import', [QuestionController::class, 'import'])->name('admin.questions.import');
    Route::get('tests/{test}/questions/export', [QuestionController::class, 'export'])->name('admin.questions.export');

    // Profile uchun qo'shimcha route‘lar
    Route::get('/profile/{user}', [ProfileController::class, 'show'])->name('admin.profile.show');
    Route::get('/profile/{user}/edit', [ProfileController::class, 'edit'])->name('admin.profile.edit');
    Route::put('/profile/{user}', [ProfileController::class, 'update'])->name('admin.profile.update');
    Route::post('/profile/{user}/generate-password', [ProfileController::class, 'generatePassword'])->name('admin.profile.generatePassword');
});