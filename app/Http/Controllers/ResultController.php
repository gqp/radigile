<?php

namespace App\Http\Controllers;

use App\Models\Result;
use Illuminate\Http\Request;

class ResultController extends Controller
{
    public function index()
    {
        $results = Result::all();
        return response()->json($results);
    }

    public function store(Request $request)
    {
        $validated = $request->validate([
            'assessment_result_id' => 'required|exists:assessment_results,id',
            'team_member_id' => 'required|exists:team_members,id',
            'score' => 'required|integer|min:0|max:4',
        ]);

        $result = Result::create($validated);
        return response()->json($result, 201);
    }
}
