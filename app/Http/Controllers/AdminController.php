<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class AdminController extends Controller
{
    public function index()
    {
        return view("dashboard.admin.index");
    }
    public function profile()
    {
        return view("dashboard.admin.profile");
    }
    public function settings()
    {
        return view("dashboard.admin.settings");
    }

    public function updateName(Request $request)
    {
        // Validate the new name
        $request->validate([
            'name' => 'required|string|max:255',
        ]);

        // Update the admin's name (use the currently logged-in admin)
        $admin = Auth::user();
        $admin->update([
            'name' => $request->name,
        ]);

        // Redirect back with a success message
        return back()->with('success', 'Name updated successfully.');
    }

}
