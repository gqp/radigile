<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Tag;
use Illuminate\Http\Request;

class TagController extends Controller
{
    /**
     * Display a listing of the tags.
     */
    public function index()
    {
        $tags = Tag::paginate(10); // Paginate tags for better UX
        return view('dashboard.admin.tags.index', compact('tags'));
    }

    /**
     * Show the form for creating a new tag.
     */
    public function create()
    {
        return view('dashboard.admin.tags.create');
    }

    /**
     * Store a newly created tag in the database.
     */
    public function store(Request $request)
    {
        $validated = $request->validate([
            'name' => 'required|string|max:255|unique:tags,name',
            'description' => 'nullable|string|max:500',
        ]);

        Tag::create($validated);

        return redirect()->route('admin.tags.index')->with('success', 'Tag created successfully!');
    }

    /**
     * Show the form for editing the specified tag.
     */
    public function edit($id)
    {
        $tag = Tag::findOrFail($id); // Fetch the tag or throw a 404 if not found
        return view('dashboard.admin.tags.edit', compact('tag')); // Pass the tag to the view
    }

    /**
     * Update the specified tag in the database.
     */
    public function update(Request $request, $id)
    {
        $tag = Tag::findOrFail($id); // Fetch the tag or throw a 404 if not found

        // Validate input values
        $validated = $request->validate([
            'name' => 'required|string|max:255|unique:tags,name,' . $id, // Ensure unique name
            'description' => 'nullable|string|max:500',
        ]);

        // Update the tag
        $tag->update($validated);

        // Redirect back to the tags index page with success message
        return redirect()->route('admin.tags.index')->with('success', 'Tag updated successfully!');
    }


    /**
     * Remove the specified tag from the database.
     */
    public function destroy(Tag $tag)
    {
        $tag->delete();

        return redirect()->route('admin.tags.index')->with('success', 'Tag deleted successfully.');
    }
}
