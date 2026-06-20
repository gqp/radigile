<?php

namespace App\Http\Controllers;

use App\Models\Assessment;
use App\Models\Team;
use App\Models\AssessmentQuestion;
use App\Models\AssessmentResult;
use Illuminate\Http\Request;

class AssessmentController extends Controller
{
    public function index()
    {
        $assessments = Assessment::all();
        return response()->json($assessments);
    }

    public function create()
    {
        // Returns a view or form for creating an assessment
    }

    public function store(Request $request)
    {
        $validated = $request->validate([
            'team_id' => 'required|exists:teams,id',
            'name' => 'required|string',
        ]);

        $assessment = Assessment::create($validated);
        return response()->json($assessment, 201);
    }

    public function show($id)
    {
        $assessment = Assessment::findOrFail($id);
        return response()->json($assessment);
    }

    public function edit($id)
    {
        $assessment = Assessment::findOrFail($id);
        return response()->json($assessment);
    }

    public function update(Request $request, $id)
    {
        $validated = $request->validate([
            'name' => 'required|string',
        ]);

        $assessment = Assessment::findOrFail($id);
        $assessment->update($validated);
        return response()->json($assessment);
    }

    public function destroy($id)
    {
        $assessment = Assessment::findOrFail($id);
        $assessment->delete();
        return response()->json(['message' => 'Assessment deleted successfully']);
    }
}
