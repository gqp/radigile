<?php

namespace App\Http\Controllers;

use App\Models\AssessmentResult;
use Illuminate\Http\Request;

class AssessmentResultController extends Controller
{
    public function index()
    {
        $results = AssessmentResult::all();
        return response()->json($results);
    }

    public function show($id)
    {
        $result = AssessmentResult::findOrFail($id);
        return response()->json($result);
    }

    public function store(Request $request)
    {
        $validated = $request->validate([
            'assessment_id' => 'required|exists:assessments,id',
            'team_id' => 'required|exists:teams,id',
            'result_data' => 'required|array',
        ]);

        $result = AssessmentResult::create($validated);
        return response()->json($result, 201);
    }
}
