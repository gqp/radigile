<?php

namespace App\Http\Controllers;

use App\Jobs\RunDemoDataSeeder;
use Database\Seeders\DemoDataSeeder;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Hash;
use Illuminate\Validation\Rules\Password;

class AdminController extends Controller
{
    public function profile()
    {
        return view("dashboard.admin.profile");
    }
    public function settings()
    {
        return view("dashboard.admin.settings");
    }

    /**
     * Queue the demo data seeder — it generates 9 months of historical
     * assessments across several teams, which is too slow to run inline in
     * a web request, so it runs on the queue worker instead.
     */
    public function runDemoDataSeeder()
    {
        RunDemoDataSeeder::dispatch();

        return back()->with('success', 'Demo data generation has been queued. It can take a minute or two to finish — refresh the relevant pages shortly to see it.');
    }

    /**
     * Deleting demo teams/users cascades through everything under them
     * (assessments, responses, subscriptions, etc.) via foreign keys, so
     * this is fast enough to run inline rather than queueing it.
     */
    public function removeDemoData()
    {
        $counts = (new DemoDataSeeder())->tearDown();

        if (array_sum($counts) === 0) {
            return back()->with('info', 'No demo data found to remove.');
        }

        return back()->with('success', "Removed demo data: {$counts['users']} users, {$counts['teams']} teams, {$counts['invites']} invite codes — along with all assessments, responses, and subscriptions tied to them.");
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
            'password' => ['required', 'confirmed', Password::defaults()], // 'confirmed' means password_confirmation is required
        ]);

        $admin = Auth::user(); // Get the currently logged-in admin

        // Check if the current password matches the one in the database
        if (!Hash::check($request->current_password, $admin->password)) {
            return back()->withErrors(['current_password' => 'The current password is incorrect.']);
        }

        try {
            // Update the admin's password
            $admin->update([
                'password' => Hash::make($request->password), // Hash the new password before storing it
            ]);

            // Redirect back with a success message
            return back()->with('success', 'Password updated successfully!');
        } catch (\Exception $e) {
            return back()->withErrors(['error' => 'An unexpected error occurred. Please try again later.']);
        }
    }

    public function updateProfile(Request $request)
    {
        $request->validate([
            'name' => 'required|string|max:255',
            'email' => 'required|email|unique:users,email,' . Auth::id(), // Validate unique email except for current user
        ]);

        $admin = Auth::user();

        try {
            $admin->update([
                'name' => $request->name,
                'email' => $request->email,
            ]);

            return back()->with('success', 'Profile updated successfully!');
        } catch (\Exception $e) {
            return back()->withErrors(['error' => 'An unexpected error occurred. Please try again later.']);
        }
    }


}
