<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\User;
use App\Models\Interview;
use App\Models\Setting;
use App\Models\ActivityLog;
use Illuminate\Http\Request;

class AdminDashboardController extends Controller
{
    /**
     * Display the Admin Central Console.
     */
    public function index()
    {
        $totalUsers = User::count();
        $totalInterviews = Interview::count();
        $avgScore = Interview::where('status', 'completed')->avg('overall_score') ?? 0.00;
        
        $aiProvider = Setting::getVal('ai_provider', 'gemini');
        $activeModel = Setting::getVal($aiProvider . '_model', 'N/A');

        $recentInterviews = Interview::with(['user', 'category'])
            ->orderBy('created_at', 'desc')
            ->limit(10)
            ->get();

        $activityLogs = ActivityLog::with('user')
            ->orderBy('created_at', 'desc')
            ->limit(10)
            ->get();

        return view('admin.dashboard', compact(
            'totalUsers',
            'totalInterviews',
            'avgScore',
            'aiProvider',
            'activeModel',
            'recentInterviews',
            'activityLogs'
        ));
    }
}
