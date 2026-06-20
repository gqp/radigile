<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Models\TeamDomain;

class TeamDomainController extends Controller
{
    /**
     * Display a listing of the resource.
     */
    public function index()
    {
        $teamDomains = TeamDomain::paginate(10);
        return view('dashboard.admin.team-domains.index', compact('teamDomains')); // Pass data to the index view
    }

    /**
     * Show the form for creating a new resource.
     */
    public function create()
    {
        return view('dashboard.admin.team-domains.create'); // Render the create form
    }

    /**
     * Store a newly created resource in storage.
     */
    public function store(Request $request)
    {
        $request->validate([
            'name' => 'required|string|max:255|unique:team_domains,name',
        ]);

        TeamDomain::create([
            'name' => $request->name,
        ]);

        return redirect()->route('admin.team-domains.index')->with('success', 'Team Domain created successfully.');
    }

    /**
     * Display the specified resource.
     */
    public function show(TeamDomain $teamDomain)
    {
        return view('admin.team-domains.show', compact('teamDomain')); // Return the show view
    }

    /**dashboard.
     * Show the form for editing the specified resource.
     */
    public function edit(TeamDomain $teamDomain)
    {
        // Render the edit form with the existing team domain data
        return view('dashboard.admin.team-domains.edit', ['domain' => $teamDomain]);
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(Request $request, TeamDomain $teamDomain)
    {
        // Validate the request data
        $validatedData = $request->validate([
            'name' => ['required', 'string', 'max:255', 'unique:team_domains,name,' . $teamDomain->id],
        ]);

        // Update the resource with validated data
        $teamDomain->update($validatedData);

        // Redirect back to the index with a success message
        return redirect()
            ->route('admin.team-domains.index')
            ->with('success', 'Team Domain updated successfully.');
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy(TeamDomain $teamDomain)
    {
        try {
            // Attempt to delete the TeamDomain
            $teamDomain->delete();

            // Redirect with success
            return redirect()->route('admin.team-domains.index')->with('success', 'Team Domain deleted successfully.');
        } catch (\Exception $e) {
            // Handle any unexpected errors
            return redirect()->route('admin.team-domains.index')->with('error', 'Failed to delete the Team Domain. Please try again.');
        }
    }
}
