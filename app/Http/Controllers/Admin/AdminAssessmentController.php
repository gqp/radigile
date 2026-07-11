<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Assessment;

class AdminAssessmentController extends Controller
{
    public function index()
    {
        $assessments = Assessment::with(['team', 'creator', 'questions', 'responses', 'evaluators'])
            ->latest()
            ->get();

        return view('dashboard.admin.assessments.index', compact('assessments'));
    }
}
