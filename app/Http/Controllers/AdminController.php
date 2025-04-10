<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Hash;

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

    public function updatePassword(Request $request)
    {
        // Validate the incoming request data
        $request->validate([
            'current_password' => 'required',
            'password' => 'required|min:8|confirmed', // 'confirmed' means password_confirmation is required
        ]);

        $admin = Auth::user(); // Get the currently logged-in admin

        // Check if the current password matches the one in the database
        if (!Hash::check($request->current_password, $admin->password)) {
            return back()->withErrors(['current_password' => 'The current password is incorrect.']);
        }

        // Update the admin's password
        $admin->update([
            'password' => Hash::make($request->password), // Hash the new password before storing it
        ]);

        // Redirect back with a success message
        return back()->with('success', 'Password updated successfully!');
    }


}
