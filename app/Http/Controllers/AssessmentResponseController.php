<?php

namespace App\Http\Controllers;

use App\Models\AssessmentResponse;
use Illuminate\Http\Request;

class AssessmentResponseController extends Controller
{
    public function store(Request $request)
    {
        $validated = $request->validate([
            'assessment_question_id' => 'required|exists:assessment_questions,id',
            'team_member_id' => 'required|exists:team_members,id',
            'response' => 'required|integer|min:0|max:4',
        ]);

        $response = AssessmentResponse::create($validated);
        return response()->json($response, 201);
    }
}
