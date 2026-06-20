<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Question;
use App\Models\QuestionCategory;
use Illuminate\Http\Request;
use App\Http\Requests\StoreQuestionRequest;
use App\Http\Requests\UpdateQuestionRequest;
use App\Models\Tag;
class QuestionController extends Controller
{
    public function index(Request $request)
    {
        // Include category and tags for eager loading
        $query = Question::with(['category', 'tags']);

        // Apply search filter if present (searches in both question text and tags)
        if ($request->has('search') && !empty($request->search)) {
            $searchTerm = $request->search;

            $query->where(function ($q) use ($searchTerm) {
                $q->where('text', 'LIKE', '%' . $searchTerm . '%') // Search in question text
                ->orWhereHas('tags', function ($q) use ($searchTerm) {
                    $q->where('name', 'LIKE', '%' . $searchTerm . '%'); // Search in tag names
                });
            });
        }

        // Apply category filter if present
        if ($request->has('category') && !empty($request->category)) {
            $query->where('category_id', $request->category);
        }

        // Apply tag filter if present (filter questions by specific tags)
        if ($request->has('tags') && !empty($request->tags)) {
            $tagNames = $request->tags; // Assume tags are passed as an array of tag names

            $query->whereHas('tags', function ($q) use ($tagNames) {
                $q->whereIn('name', $tagNames); // Filter questions that have any of the provided tag names
            });
        }

        // Apply sorting if present
        if ($request->has('sort') && !empty($request->sort)) {
            $query->orderBy($request->sort);
        }

        // Paginate results
        $questions = $query->paginate(10);

        // Fetch all categories and tags for the dropdowns/filters
        $categories = QuestionCategory::all();
        $tags = Tag::all(); // Get all available tags

        // Return the view with filters applied
        return view('dashboard.admin.questions.index', compact('categories', 'tags', 'questions'));
    }

    public function create()
    {
        $categories = QuestionCategory::all(); // Fetch all categories to be used in the form
        $tags = Tag::all(); // Fetch all existing tags to populate in the form
        return view('dashboard.admin.questions.create', compact('categories', 'tags'));
    }

    public function store(StoreQuestionRequest $request)
    {
        try {
            // Create question with validated data
            $question = Question::create($request->validated());

            // Handle tags: Create new tags if needed and associate them
            if ($request->filled('tags')) {
                $tagIds = collect($request->input('tags'))->map(function ($tagName) {
                    return Tag::firstOrCreate(['name' => $tagName])->id;
                });
                $question->tags()->sync(
                    collect($request->tags)->map(function ($tagName) {
                        return Tag::firstOrCreate(['name' => $tagName])->id;
                    })
                );// Sync tags with the question
            }

            return redirect()->route('admin.questions.index')->with('success', 'Question created successfully with tags.');
        } catch (\Exception $e) {
            \Log::error('Error creating question with tags: ' . $e->getMessage());
            return back()->withErrors(['error' => 'An error occurred while creating the question. Please try again.']);
        }
    }

    public function edit($id)
    {
        $question = Question::findOrFail($id); // Find the question by ID or throw a 404 error
        $categories = QuestionCategory::all(); // Fetch all categories for the dropdown
        $tags = Tag::all();
        return view('dashboard.admin.questions.edit', compact('question', 'categories', 'tags')); // Pass the question and categories to the edit view
    }


    public function update(Request $request, $id)
    {
        $request->validate([
            'text' => 'required|string|max:255',
            'category_id' => 'required|exists:question_categories,id',
            'tags' => 'nullable|array',
            'tags.*' => 'string|exists:tags,name',
            'new_tag' => 'nullable|string|max:255',
        ]);

        $question = Question::findOrFail($id);

        // Update question fields
        $question->update($request->only(['text', 'category_id']));

        // Add new tag if entered
        if ($request->filled('new_tag')) {
            $newTag = Tag::firstOrCreate(['name' => $request->new_tag]);
            $request->tags = array_merge($request->tags ?? [], [$newTag->name]);
        }

        // Update tags
        $tagIds = collect($request->tags)->map(function ($tagName) {
            return Tag::firstOrCreate(['name' => $tagName])->id;
        });
        $question->tags()->sync($tagIds);

        return redirect()->route('admin.questions.index')->with('success', 'Question updated successfully.');
    }

    public function show($id)
    {
        $question = Question::findOrFail($id);
        return response()->json($question);
    }

    public function destroy($id)
    {
        try {
            $question = Question::findOrFail($id); // Retrieve the question or throw a 404 error
            $question->delete(); // Delete the question

            // Redirect back to the previous page with a success message
            return back()->with('success', 'Question deleted successfully.');
        } catch (\Exception $e) {
            \Log::error('Error deleting question: ' . $e->getMessage());
            // Redirect back to the previous page with an error message
            return back()->withErrors(['error' => 'An error occurred while deleting the question. Please try again.']);
        }
    }
}
