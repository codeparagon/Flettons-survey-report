<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\User;
use Illuminate\Http\Request;

class DashboardController extends Controller
{
    /**
     * Show the admin dashboard.
     *
     * @return \Illuminate\View\View
     */
    public function index()
    {
        $totalUsers = User::count();
        $activeUsers = User::where('status', 'active')->count();
        
        // Survey statistics
        $totalSurveys = \App\Models\Survey::count();
        $pendingSurveys = \App\Models\Survey::where('status', 'pending')->count();
        $inProgressSurveys = \App\Models\Survey::where('status', 'in_progress')->count();
        $completedSurveys = \App\Models\Survey::where('status', 'completed')->count();
        
        // Survey level statistics
        $totalLevels = \App\Models\SurveyLevel::count();
        $activeLevels = \App\Models\SurveyLevel::where('is_active', true)->count();
        
        // Recent surveys
        $recentSurveys = \App\Models\Survey::with('surveyor')
            ->orderBy('created_at', 'desc')
            ->limit(5)
            ->get();
        
        return view('admin.dashboard', compact(
            'totalUsers', 
            'activeUsers',
            'totalSurveys',
            'pendingSurveys',
            'inProgressSurveys',
            'completedSurveys',
            'totalLevels',
            'activeLevels',
            'recentSurveys'
        ));
    }
}

