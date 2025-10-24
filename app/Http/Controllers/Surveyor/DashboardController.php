<?php

namespace App\Http\Controllers\Surveyor;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;

class DashboardController extends Controller
{
    /**
     * Show the surveyor dashboard.
     *
     * @return \Illuminate\View\View
     */
    public function index()
    {
        $surveyorId = auth()->id();
        
        // Get surveys assigned to this surveyor
        $totalJobs = \App\Models\Survey::where('surveyor_id', $surveyorId)->count();
        $pendingJobs = \App\Models\Survey::where('surveyor_id', $surveyorId)
            ->where('status', 'pending')->count();
        $inProgressJobs = \App\Models\Survey::where('surveyor_id', $surveyorId)
            ->where('status', 'in_progress')->count();
        $completedJobs = \App\Models\Survey::where('surveyor_id', $surveyorId)
            ->where('status', 'completed')->count();
        
        // Recent jobs - only assigned surveys
        $recentJobs = \App\Models\Survey::where('surveyor_id', $surveyorId)
            ->orderBy('created_at', 'desc')
            ->limit(5)
            ->get();
        
        return view('surveyor.dashboard', compact(
            'totalJobs',
            'pendingJobs',
            'inProgressJobs',
            'completedJobs',
            'recentJobs'
        ));
    }
}

