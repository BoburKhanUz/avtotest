<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\TestResult;
use App\Models\Test;
use App\Models\User;
use App\Models\Category;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;

class TestResultController extends Controller
{
    public function index(Request $request)
    {
        $query = TestResult::with(['user', 'test']);

        // Search by user
        if ($request->filled('search')) {
            $search = $request->search;
            $query->whereHas('user', function ($q) use ($search) {
                $q->where('name', 'like', "%{$search}%")
                  ->orWhere('email', 'like', "%{$search}%");
            });
        }

        // Filter by test
        if ($request->filled('test_id')) {
            $query->where('test_id', $request->test_id);
        }

        // Filter by score range
        if ($request->filled('min_score')) {
            $query->where('score', '>=', $request->min_score);
        }
        if ($request->filled('max_score')) {
            $query->where('score', '<=', $request->max_score);
        }

        // Filter by passed/failed
        if ($request->filled('result')) {
            if ($request->result === 'passed') {
                $query->whereRaw('(score / total_questions * 100) >= 70');
            } elseif ($request->result === 'failed') {
                $query->whereRaw('(score / total_questions * 100) < 70');
            }
        }

        // Filter by date range
        if ($request->filled('date_from')) {
            $query->whereDate('completed_at', '>=', $request->date_from);
        }
        if ($request->filled('date_to')) {
            $query->whereDate('completed_at', '<=', $request->date_to);
        }

        // Sort
        $sortBy = $request->get('sort', 'completed_at');
        $sortOrder = $request->get('order', 'desc');
        $query->orderBy($sortBy, $sortOrder);

        $results = $query->paginate(20)->withQueryString();
        $tests = Test::orderBy('name_uz')->get();

        // Statistics
        $stats = [
            'total_attempts' => TestResult::count(),
            'average_score' => round(TestResult::avg(DB::raw('score / total_questions * 100')), 2),
            'pass_rate' => TestResult::count() > 0
                ? round(TestResult::whereRaw('(score / total_questions * 100) >= 70')->count() / TestResult::count() * 100, 2)
                : 0,
            'unique_users' => TestResult::distinct('user_id')->count('user_id'),
        ];

        return view('admin.test-results.index', compact('results', 'tests', 'stats'));
    }

    public function show(TestResult $testResult)
    {
        $testResult->load(['user', 'test.questions']);
        return view('admin.test-results.show', compact('testResult'));
    }

    /**
     * Statistics dashboard
     */
    public function statistics(Request $request)
    {
        $language = auth()->user()->language ?? 'uz';
        $allowedLanguages = ['uz', 'ru', 'en'];
        $language = in_array($language, $allowedLanguages) ? $language : 'uz';

        // Overall statistics
        $overallStats = [
            'total_tests' => Test::count(),
            'total_attempts' => TestResult::count(),
            'total_users' => User::count(),
            'active_users' => TestResult::distinct('user_id')->count('user_id'),
            'average_score' => round(TestResult::avg(DB::raw('score / total_questions * 100')), 2),
            'pass_rate' => TestResult::count() > 0
                ? round(TestResult::whereRaw('(score / total_questions * 100) >= 70')->count() / TestResult::count() * 100, 2)
                : 0,
        ];

        // Category statistics
        $categoryStats = TestResult::join('tests', 'test_results.test_id', '=', 'tests.id')
            ->join('categories', 'tests.category_id', '=', 'categories.id')
            ->selectRaw("categories.name_{$language} as category_name,
                         AVG(test_results.score / test_results.total_questions * 100) as average_score,
                         COUNT(*) as attempts,
                         SUM(CASE WHEN test_results.score / test_results.total_questions * 100 >= 70 THEN 1 ELSE 0 END) as passed")
            ->groupBy("categories.name_{$language}")
            ->orderBy("categories.name_{$language}")
            ->get();

        // Top performing users
        $topUsers = User::select('users.id', 'users.name', 'users.email')
            ->join('test_results', 'users.id', '=', 'test_results.user_id')
            ->selectRaw('users.id, users.name, users.email,
                         AVG(test_results.score / test_results.total_questions * 100) as avg_score,
                         COUNT(*) as total_attempts')
            ->groupBy('users.id', 'users.name', 'users.email')
            ->orderByDesc('avg_score')
            ->limit(10)
            ->get();

        // Most popular tests
        $popularTests = Test::select("tests.id", "tests.name_{$language} as name")
            ->join('test_results', 'tests.id', '=', 'test_results.test_id')
            ->selectRaw("tests.id, tests.name_{$language} as name, COUNT(*) as attempts")
            ->groupBy('tests.id', "tests.name_{$language}")
            ->orderByDesc('attempts')
            ->limit(10)
            ->get();

        // Monthly statistics for chart
        $monthlyStats = TestResult::selectRaw('
                YEAR(completed_at) as year,
                MONTH(completed_at) as month,
                COUNT(*) as attempts,
                AVG(score / total_questions * 100) as avg_score,
                SUM(CASE WHEN score / total_questions * 100 >= 70 THEN 1 ELSE 0 END) as passed
            ')
            ->whereYear('completed_at', '>=', now()->subYear()->year)
            ->groupBy('year', 'month')
            ->orderBy('year')
            ->orderBy('month')
            ->get();

        // Daily activity for last 30 days
        $dailyActivity = TestResult::selectRaw('
                DATE(completed_at) as date,
                COUNT(*) as attempts,
                COUNT(DISTINCT user_id) as unique_users
            ')
            ->where('completed_at', '>=', now()->subDays(30))
            ->groupBy('date')
            ->orderBy('date')
            ->get();

        return view('admin.test-results.statistics', compact(
            'overallStats',
            'categoryStats',
            'topUsers',
            'popularTests',
            'monthlyStats',
            'dailyActivity'
        ));
    }

    /**
     * Delete test result
     */
    public function destroy(TestResult $testResult)
    {
        $testResult->delete();
        return redirect()->route('admin.test-results.index')->with('success', 'Test natijasi o\'chirildi');
    }

    /**
     * Export test results to CSV
     */
    public function export(Request $request)
    {
        $query = TestResult::with(['user', 'test']);

        // Apply same filters as index
        if ($request->filled('test_id')) {
            $query->where('test_id', $request->test_id);
        }
        if ($request->filled('date_from')) {
            $query->whereDate('completed_at', '>=', $request->date_from);
        }
        if ($request->filled('date_to')) {
            $query->whereDate('completed_at', '<=', $request->date_to);
        }

        $results = $query->orderBy('completed_at', 'desc')->get();

        $filename = 'test_results_' . now()->format('Y-m-d_His') . '.csv';

        $headers = [
            'Content-Type' => 'text/csv; charset=utf-8',
            'Content-Disposition' => "attachment; filename=\"{$filename}\"",
        ];

        $callback = function () use ($results) {
            $file = fopen('php://output', 'w');

            // Add BOM for UTF-8
            fprintf($file, chr(0xEF).chr(0xBB).chr(0xBF));

            // Header row
            fputcsv($file, ['ID', 'Foydalanuvchi', 'Test', 'Ball', 'Jami savollar', 'Foiz', 'Natija', 'Sana']);

            foreach ($results as $result) {
                $percentage = $result->total_questions > 0
                    ? round(($result->score / $result->total_questions) * 100, 2)
                    : 0;
                $passed = $percentage >= 70 ? 'O\'tdi' : 'O\'tmadi';

                fputcsv($file, [
                    $result->id,
                    $result->user ? $result->user->name : 'N/A',
                    $result->test ? $result->test->name_uz : 'N/A',
                    $result->score,
                    $result->total_questions,
                    $percentage . '%',
                    $passed,
                    $result->completed_at->format('Y-m-d H:i:s'),
                ]);
            }

            fclose($file);
        };

        return response()->stream($callback, 200, $headers);
    }
}
