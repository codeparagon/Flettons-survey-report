<?php

namespace App\Http\Controllers\Client;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;

class DashboardController extends Controller
{
    /**
     * Show the client dashboard.
     *
     * @return \Illuminate\View\View
     */
    public function index()
    {
        $clientId = auth()->id();
        
        $clientEmail = auth()->user()->email;
        
        $totalSurveys = \App\Models\Survey::where('email_address', $clientEmail)
            ->orWhere('inf_field_Email', $clientEmail)->count();
        $pendingSurveys = \App\Models\Survey::where(function($q) use ($clientEmail) {
                $q->where('email_address', $clientEmail)->orWhere('inf_field_Email', $clientEmail);
            })->where('status', 'pending')->count();
        $completedSurveys = \App\Models\Survey::where(function($q) use ($clientEmail) {
                $q->where('email_address', $clientEmail)->orWhere('inf_field_Email', $clientEmail);
            })->where('status', 'completed')->count();
        
        // Reports available = completed surveys (they can generate PDF reports)
        $reportsAvailable = $completedSurveys;
        
        // Recent surveys
        $recentSurveys = \App\Models\Survey::where('email_address', $clientEmail)
            ->orWhere('inf_field_Email', $clientEmail)
            ->with('surveyor')
            ->orderBy('created_at', 'desc')
            ->limit(5)
            ->get();
        
        // Calculate completion rate
        $completionRate = $totalSurveys > 0 ? round(($completedSurveys / $totalSurveys) * 100, 1) : 0;
        
        return view('client.dashboard', compact(
            'totalSurveys',
            'pendingSurveys',
            'completedSurveys',
            'reportsAvailable',
            'recentSurveys',
            'completionRate'
        ));
    }
}

