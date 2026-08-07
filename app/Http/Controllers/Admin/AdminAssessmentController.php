<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Concerns\Searchable;
use App\Http\Controllers\Concerns\Sortable;
use App\Http\Controllers\Controller;
use App\Models\Assessment;

class AdminAssessmentController extends Controller
{
    use Sortable, Searchable;

    public function index()
    {
        $query = Assessment::with(['team', 'creator', 'questions', 'responses', 'evaluators']);
        $this->applySearch($query, ['title', 'team.name', 'creator.name']);
        $this->applySort($query, ['title', 'status', 'created_at'], 'created_at', 'desc');
        $assessments = $query->paginate(15)->withQueryString();

        if (request()->ajax()) {
            return view('dashboard.admin.assessments._results', compact('assessments'));
        }

        return view('dashboard.admin.assessments.index', compact('assessments'));
    }
}
