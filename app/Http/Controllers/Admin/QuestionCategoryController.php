<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\QuestionCategory;
use Illuminate\Http\Request;

class QuestionCategoryController extends Controller
{
    public function index()
    {
        $categories = QuestionCategory::all();
        return view('dashboard.admin.question-categories.index', compact('categories'));

    }

    public function create()
    {
        $questionCategories = QuestionCategory::all();
        return view('dashboard.admin.question-categories.create', compact('questionCategories'));
    }

    public function edit($id)
    {
        // Retrieve the category by ID, or throw a 404 if not found
        $category = QuestionCategory::findOrFail($id);

        // Return the view with the category
        return view('dashboard.admin.question-categories.edit', compact('category'));
    }


    public function store(Request $request)
    {
        // Validate the request data
        $validated = $request->validate([
            'name' => 'required|string',
            'description' => 'nullable|string',
        ]);

        try {
            // Attempt to create a new category
            QuestionCategory::create($validated);

            // Redirect to the category index page with a success message
            return redirect()->route('admin.question-categories.index')->with('success', 'Category created successfully.');
        } catch (\Exception $e) {
            // Log the error and redirect back with an error message
            \Log::error('Error creating category: ' . $e->getMessage());
            return back()->withErrors(['error' => 'An error occurred while creating the category. Please try again.']);
        }
    }

    public function show($id)
    {
        $category = QuestionCategory::findOrFail($id);
        return response()->json($category);
    }

    public function update(Request $request, $id)
    {
        // Validate the request data
        $validated = $request->validate([
            'name' => 'required|string|max:255',
            'description' => 'nullable|string',
        ]);

        try {
            // Retrieve the category by ID
            $category = QuestionCategory::findOrFail($id);

            // Update the category with the validated data
            $category->update($validated);

            // Redirect to the category index with a success message
            return redirect()->route('admin.question-categories.index')->with('success', 'Category updated successfully.');
        } catch (\Exception $e) {
            // Log the error and redirect back with an error message
            \Log::error('Error updating category: ' . $e->getMessage());
            return back()->withErrors(['error' => 'An error occurred while updating the category. Please try again.']);
        }
    }

    public function destroy($id)
    {
        try {
            // Attempt to find and delete the category
            $category = QuestionCategory::findOrFail($id);
            $category->delete();

            // Redirect back with a success message
            return back()->with('success', 'Category deleted successfully.');
        } catch (\Exception $e) {
            // Handle any unexpected errors
            return back()->withErrors(['error' => 'Failed to delete the category. Please try again.']);
        }
    }
}
