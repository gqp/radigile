<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Question;
use App\Models\QuestionCategory;
use Illuminate\Http\Request;
use App\Http\Requests\StoreQuestionRequest;
use App\Http\Requests\UpdateQuestionRequest;
use App\Models\Tag;
use App\Services\AiQuestionGenerator;
use Illuminate\Support\Facades\DB;
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
        $categories = QuestionCategory::cached();
        $tags = Tag::cached(); // Get all available tags

        // Return the view with filters applied
        return view('dashboard.admin.questions.index', compact('categories', 'tags', 'questions'));
    }

    public function create()
    {
        $categories = QuestionCategory::cached(); // Fetch all categories to be used in the form
        $tags = Tag::cached(); // Fetch all existing tags to populate in the form
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
        $categories = QuestionCategory::cached(); // Fetch all categories for the dropdown
        $tags = Tag::cached();
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

    public function generateWithAI(Request $request, AiQuestionGenerator $generator)
    {
        $request->validate([
            'description' => 'required|string|min:10|max:500',
            'category'    => 'nullable|string|max:100',
            'framework'   => 'nullable|string|max:100',
        ]);

        $result = $generator->generate($request->description, $request->category, $request->framework);

        if (isset($result['error'])) {
            return response()->json(['error' => $result['error']], $result['status']);
        }

        return response()->json($result);
    }

    public function export()
    {
        $questions = Question::with(['category', 'tags'])->orderBy('id')->get();

        $payload = [
            'questions' => $questions->map(fn (Question $question) => [
                'text'      => $question->text,
                'category'  => $question->category->name ?? null,
                'tags'      => $question->tags->pluck('name')->values(),
                'tip_0'     => $question->tip_0,
                'tip_1'     => $question->tip_1,
                'tip_2'     => $question->tip_2,
                'tip_3'     => $question->tip_3,
                'tip_4'     => $question->tip_4,
                'is_active' => $question->is_active,
            ])->values(),
        ];

        $filename = 'questions-export-' . now()->format('Y-m-d-His') . '.json';

        return response()->streamDownload(function () use ($payload) {
            echo json_encode($payload, JSON_PRETTY_PRINT | JSON_UNESCAPED_SLASHES | JSON_UNESCAPED_UNICODE);
        }, $filename, ['Content-Type' => 'application/json']);
    }

    public function import(Request $request)
    {
        $request->validate([
            'file' => 'required|file|mimes:json|max:10240',
        ]);

        $data = json_decode(file_get_contents($request->file('file')->getRealPath()), true);

        if (json_last_error() !== JSON_ERROR_NONE || !isset($data['questions']) || !is_array($data['questions'])) {
            return back()->withErrors(['error' => 'Invalid file. Please upload a JSON file exported from this system.']);
        }

        $created = 0;
        $updated = 0;
        $skipped = 0;

        DB::transaction(function () use ($data, &$created, &$updated, &$skipped) {
            foreach ($data['questions'] as $row) {
                if (empty($row['text']) || empty($row['category'])) {
                    $skipped++;
                    continue;
                }

                $category = QuestionCategory::firstOrCreate(['name' => trim($row['category'])]);

                $question = Question::where('category_id', $category->id)
                    ->where('text', $row['text'])
                    ->first();

                $attributes = [
                    'text'        => $row['text'],
                    'category_id' => $category->id,
                    'tip_0'       => $row['tip_0'] ?? null,
                    'tip_1'       => $row['tip_1'] ?? null,
                    'tip_2'       => $row['tip_2'] ?? null,
                    'tip_3'       => $row['tip_3'] ?? null,
                    'tip_4'       => $row['tip_4'] ?? null,
                    'is_active'   => array_key_exists('is_active', $row) ? (bool) $row['is_active'] : true,
                ];

                if ($question) {
                    $question->update($attributes);
                    $updated++;
                } else {
                    $question = Question::create($attributes);
                    $created++;
                }

                if (!empty($row['tags']) && is_array($row['tags'])) {
                    $tagIds = collect($row['tags'])->filter()->map(
                        fn ($name) => Tag::firstOrCreate(['name' => trim($name)])->id
                    );
                    $question->tags()->sync($tagIds);
                }
            }
        });

        $message = "Import complete: {$created} created, {$updated} updated";
        if ($skipped > 0) {
            $message .= ", {$skipped} skipped (missing text or category)";
        }

        return redirect()->route('admin.questions.index')->with('success', $message . '.');
    }

    public function toggleActive($id)
    {
        $question = Question::findOrFail($id); // Find question
        $question->is_active = !$question->is_active; // Toggle `is_active`
        $question->save(); // Save changes

        return back()->with('success', 'Question status updated successfully.');
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
