<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Survey;
use App\Models\User;
use App\Models\Role;
use Illuminate\Http\Request;

class SurveyController extends Controller
{
    public function index()
    {
        $surveys = Survey::with('surveyor')
            ->orderBy('created_at', 'desc')
            ->paginate(15);
            
        return view('admin.surveys.index', compact('surveys'));
    }

    public function show(Survey $survey)
    {
        return view('admin.surveys.show', compact('survey'));
    }

    public function edit(Survey $survey)
    {
        $surveyorRole = Role::where('name', 'surveyor')->first();
        $surveyors = User::where('role_id', $surveyorRole->id)->get();
        
        return view('admin.surveys.edit', compact('survey', 'surveyors'));
    }

    public function update(Request $request, Survey $survey)
    {
        $validated = $request->validate([
            'surveyor_id' => 'nullable|exists:users,id',
            'status' => 'required|in:pending,assigned,in_progress,completed,cancelled',
            'payment_status' => 'required|in:pending,paid,refunded',
            'scheduled_date' => 'nullable|date',
            'admin_notes' => 'nullable|string',
        ]);

        $survey->update($validated);

        return redirect()->route('admin.surveys.show', $survey->id)
            ->with('success', 'Survey updated successfully.');
    }
}





