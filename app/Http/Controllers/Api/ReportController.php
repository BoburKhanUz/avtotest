<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Models\Subscription;
use App\Models\TestResult;
use App\Models\User;
use Illuminate\Http\Request;

class ReportController extends Controller
{
    public function userReport(Request $request)
    {
        $totalUsers = User::count();
        $activeUsers = User::whereHas('subscriptions', function ($query) {
            $query->where('is_active', true);
        })->count();

        return response()->json([
            'total_users' => $totalUsers,
            'active_users' => $activeUsers,
        ]);
    }

    public function subscriptionReport(Request $request)
    {
        $subscriptions = Subscription::selectRaw('plans.name as plan_name, COUNT(*) as count')
            ->join('plans', 'subscriptions.plan_id', '=', 'plans.id')
            ->groupBy('plans.name')
            ->get();

        return response()->json($subscriptions);
    }

    public function testResultReport(Request $request)
    {
        $user = $request->user();
        $results = TestResult::where('user_id', $user->id)
            ->selectRaw('test_id, AVG(score) as average_score, COUNT(*) as attempts')
            ->groupBy('test_id')
            ->with('test:id,title,category_id')
            ->get();

        return response()->json($results);
    }

    public function userStats(Request $request)
    {
        $user = $request->user();
        $results = TestResult::where('user_id', $user->id)
            ->join('tests', 'test_results.test_id', '=', 'tests.id')
            ->join('categories', 'tests.category_id', '=', 'categories.id')
            ->selectRaw('categories.name as category_name, AVG(test_results.score) as average_score, COUNT(*) as attempts')
            ->groupBy('categories.name')
            ->orderBy('categories.name')
            ->get();

        $labels = $results->pluck('category_name');
        $data = $results->pluck('average_score');

        return response()->json([
            'labels' => $labels,
            'data' => $data,
            'attempts' => $results->pluck('attempts'),
        ]);
    }
}