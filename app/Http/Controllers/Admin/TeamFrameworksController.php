<?php

namespace App\Http\Controllers\Admin;
use App\Http\Controllers\Controller;
use App\Models\TeamFramework;
use Illuminate\Http\Request;

class TeamFrameworksController extends Controller
{
    // Display a list of team frameworks
    public function index()
    {
        // Fetch all team frameworks from the database
        $frameworks = TeamFramework::cached();

        // Return the index view with the frameworks
        return view('dashboard.admin.team-frameworks.index', compact('frameworks'));
    }

    // Display the form for creating a new framework
    public function create()
    {
        // Return the create view (framework creation form)
        return view('dashboard.admin.team-frameworks.create');
    }

    // Store a newly created framework in the database
    public function store(Request $request)
    {
        // Validate the incoming request
        $validated = $request->validate([
            'name' => 'required|string|max:255',
        ]);

        try {
            // Create the framework in the database
            TeamFramework::create($validated); // Correct usage of the model

            // Redirect to the index with a success message
            return redirect()->route('admin.team-frameworks.index')->with('success', 'Framework created successfully.');
        } catch (\Exception $e) {
            // Log any exception that occurs
            \Log::error('Error creating framework: ' . $e->getMessage());

            // Redirect back with an error message
            return back()->withErrors(['error' => 'An error occurred while creating the framework. Please try again.']);
        }
    }

    // Show the form for editing the specified framework
    public function edit($id)
    {
        // Find the framework by ID or return a 404 error
        $framework = TeamFramework::findOrFail($id); // Correct usage of the model

        // Return the edit view with the framework data
        return view('dashboard.admin.team-frameworks.edit', compact('framework'));
    }

    // Update the specified framework in the database
    public function update(Request $request, $id)
    {
        // Validate the input data
        $validated = $request->validate([
            'name' => 'required|string|max:255',
        ]);

        try {
            // Find the framework by ID
            $framework = TeamFramework::findOrFail($id); // Correct usage of the model

            // Update the framework with the validated data
            $framework->update($validated);

            // Redirect to the index with a success message
            return redirect()->route('admin.team-frameworks.index')->with('success', 'Framework updated successfully.');
        } catch (\Exception $e) {
            // Log any exception that occurs
            \Log::error('Error updating framework: ' . $e->getMessage());

            // Redirect back with an error message
            return back()->withErrors(['error' => 'An error occurred while updating the framework. Please try again.']);
        }
    }

    // Remove the specified framework from the database
    public function destroy($id)
    {
        try {
            // Find the framework by ID and delete it
            $framework = TeamFramework::findOrFail($id); // Correct usage of the model
            $framework->delete();

            // Redirect to the index with a success message
            return redirect()->route('admin.team-frameworks.index')->with('success', 'Framework deleted successfully.');
        } catch (\Exception $e) {
            // Log any exception that occurs
            \Log::error('Error deleting framework: ' . $e->getMessage());

            // Redirect back with an error message
            return back()->withErrors(['error' => 'An error occurred while deleting the framework. Please try again.']);
        }
    }
}
