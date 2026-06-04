<?php

namespace App\Http\Controllers;

use App\Models\Interview;
use App\Models\Category;
use Illuminate\Http\Request;

class DashboardController extends Controller
{
    /**
     * Render candidate dashboard with all stats and analytics.
     */
    public function index()
    {
        $userId = auth()->id();

        // 1. Core Summary Stats
        $totalInterviews = Interview::where('user_id', $userId)->count();
        
        $completedInterviews = Interview::where('user_id', $userId)
            ->where('status', 'completed')
            ->count();

        $avgScore = Interview::where('user_id', $userId)
            ->where('status', 'completed')
            ->avg('overall_score') ?? 0.0;

        $bestScore = Interview::where('user_id', $userId)
            ->where('status', 'completed')
            ->max('overall_score') ?? 0.0;

        // 2. Recent Interviews List
        $recentInterviews = Interview::with('category')
            ->where('user_id', $userId)
            ->orderBy('created_at', 'desc')
            ->limit(5)
            ->get();

        // 3. Category Wise Progress (Group by Category)
        $categoryProgress = Category::select('categories.name', 'categories.icon_class')
            ->selectRaw('count(interviews.id) as total_attempts')
            ->selectRaw('avg(interviews.overall_score) as avg_score')
            ->leftJoin('interviews', function($join) use ($userId) {
                $join->on('categories.id', '=', 'interviews.category_id')
                     ->where('interviews.user_id', '=', $userId)
                     ->where('interviews.status', '=', 'completed');
            })
            ->groupBy('categories.id', 'categories.name', 'categories.icon_class')
            ->get();

        // 4. Analytics: Last 7 Completed Interview Scores (for Chart.js Trend Line)
        $weeklyPerformance = Interview::where('user_id', $userId)
            ->where('status', 'completed')
            ->orderBy('completed_at', 'asc')
            ->limit(7)
            ->get(['completed_at', 'overall_score']);

        $chartLabels = [];
        $chartData = [];
        foreach ($weeklyPerformance as $index => $int) {
            $chartLabels[] = 'Session ' . ($index + 1) . ' (' . $int->completed_at->format('M d') . ')';
            $chartData[] = round($int->overall_score, 1);
        }

        // 5. Monthly aggregation (Average score per month)
        // Using PHP groupBy to stay DB-agnostic (works on MySQL & PostgreSQL)
        $monthlyRaw = Interview::where('user_id', $userId)
            ->where('status', 'completed')
            ->whereNotNull('completed_at')
            ->orderBy('completed_at', 'asc')
            ->limit(200)
            ->get(['completed_at', 'overall_score']);

        $grouped = $monthlyRaw->groupBy(fn($i) => $i->completed_at->format('Y-m'))
            ->take(6);

        $monthlyLabels = [];
        $monthlyData = [];
        foreach ($grouped as $month => $rows) {
            $monthlyLabels[] = date('F Y', strtotime($month . '-01'));
            $monthlyData[] = round($rows->avg('overall_score'), 1);
        }

        $allCategories = Category::all();

        return view('dashboard', compact(
            'totalInterviews',
            'completedInterviews',
            'avgScore',
            'bestScore',
            'recentInterviews',
            'categoryProgress',
            'chartLabels',
            'chartData',
            'monthlyLabels',
            'monthlyData',
            'allCategories'
        ));
    }
}
