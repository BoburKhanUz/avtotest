<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Payment;
use App\Models\Subscription;
use App\Models\Test;
use App\Models\TestResult;
use App\Models\User;
use Illuminate\Http\Request;

class AdminController extends Controller
{
    public function dashboard()
    {
        $totalUsers = User::count();
        $totalTests = Test::count();
        $activeSubscriptions = Subscription::where('is_active', true)->count();
        $totalRevenue = Payment::where('status', 'completed')->sum('amount');

        return view('admin.dashboard', compact('totalUsers', 'totalTests', 'activeSubscriptions', 'totalRevenue'));
    }

    public function stats()
    {
        $language = auth()->user()->language ?? 'uz'; // Foydalanuvchi tilini olish (standart: uz)
        $categoryStats = TestResult::join('tests', 'test_results.test_id', '=', 'tests.id')
            ->join('categories', 'tests.category_id', '=', 'categories.id')
            ->selectRaw("categories.name_$language as category_name, AVG(test_results.score) as average_score, COUNT(*) as attempts")
            ->groupBy("categories.name_$language")
            ->orderBy("categories.name_$language")
            ->get();
    
        $labels = $categoryStats->pluck('category_name');
        $scores = $categoryStats->pluck('average_score');
        $attempts = $categoryStats->pluck('attempts');
    
        return view('admin.stats', compact('categoryStats', 'labels', 'scores', 'attempts'));
    }
    
    public function apiDocs()
    {
        return view('admin.api-docs');
    }
}