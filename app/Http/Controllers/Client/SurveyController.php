<?php

namespace App\Http\Controllers\Client;

use App\Http\Controllers\Controller;
use App\Models\Survey;
use Illuminate\Http\Request;

class SurveyController extends Controller
{
    public function index()
    {
        // Get surveys by matching email
        $surveys = Survey::where('email_address', auth()->user()->email)
            ->orWhere('inf_field_Email', auth()->user()->email)
            ->with('surveyor')
            ->orderBy('created_at', 'desc')
            ->paginate(15);
            
        return view('client.surveys.index', compact('surveys'));
    }

    public function show(Survey $survey)
    {
        // Ensure client can only view their own surveys
        $clientEmail = auth()->user()->email;
        if ($survey->email_address !== $clientEmail && $survey->inf_field_Email !== $clientEmail) {
            abort(403, 'Unauthorized');
        }
        
        return view('client.surveys.show', compact('survey'));
    }
}









