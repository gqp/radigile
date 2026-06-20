<?php

namespace App\Http\Controllers;

use App\Models\AssessmentQuestion;
use Illuminate\Http\Request;

class AssessmentQuestionController extends Controller
{
    public function index()
    {
        $questions = AssessmentQuestion::all();
        return response()->json($questions);
    }

    public function create()
    {
        // Return view/form to create a question
    }

    public function store(Request $request)
    {
        $validated = $request->validate([
            'question_text' => 'required|string',
            'assessment_id' => 'required|exists:assessments,id',
        ]);

        $question = AssessmentQuestion::create($validated);
        return response()->json($question, 201);
    }

    public function show($id)
    {
        $question = AssessmentQuestion::findOrFail($id);
        return response()->json($question);
    }

    public function edit($id)
    {
        $question = AssessmentQuestion::findOrFail($id);
        return response()->json($question);
    }

    public function update(Request $request, $id)
    {
        $validated = $request->validate([
            'question_text' => 'required|string',
        ]);

        $question = AssessmentQuestion::findOrFail($id);
        $question->update($validated);
        return response()->json($question);
    }

    public function destroy($id)
    {
        $question = AssessmentQuestion::findOrFail($id);
        $question->delete();
        return response()->json(['message' => 'Question deleted successfully']);
    }
}
