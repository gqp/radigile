<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\User;
use App\Models\Subscription;
use App\Models\Plan;
use Spatie\Permission\Models\Role;
use Spatie\Permission\Models\Permission;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Auth;

class UserController extends Controller
{
    public function toggleActive($id)
    {
        $user = User::findOrFail($id); // Find user
        $user->is_active = !$user->is_active; // Toggle `is_active`
        $user->save(); // Save changes

        return redirect()->route('admin.users.index')->with('success', 'User status updated successfully.');
    }

    public function index()
    {
        $users = User::with('roles')->get();
        return view('dashboard.user.index', compact('users'));


    }

    public function profile()
    {
        $user = Auth::user(); // Get the currently authenticated user
        return view('dashboard.user.profile', compact('user')); // Pass user data to the profile view
    }


    public function create()
    {
        // Retrieve all available plans from the database
        $plans = Plan::all();
        $permissions = Permission::all();
        $roles = Role::all();

        // Pass the $plans variable to the view
        return view('dashboard.admin.users.create', compact('plans','permissions','roles'));
    }


    public function store(Request $request)
    {
        $request->validate([
            'name' => 'required|string|max:255',
            'email' => 'required|email|unique:users,email',
            'password' => 'required|string|min:8|confirmed',
            'role' => 'required|string|exists:roles,name;'
        ]);

        // Create the user with unverified email
        $user = User::create([
            'name' => $request->name,
            'email' => $request->email,
            'password' => Hash::make($request->password),
        ]);

        // Assign the role to the user
        $user->assignRole($request->role);

        // Send email verification notification
        $user->sendEmailVerificationNotification();

        return redirect()->route('admin.users.index')->with('success', 'User created successfully.');
    }

    public function edit($id)
    {
        // Retrieve the user by ID with subscription and roles
        $user = User::with('subscription.plan', 'roles')->findOrFail($id);

        // Retrieve all available plans
        $plans = Plan::all();
        $roles = Role::all();
        $permissions = Permission::all();

        $role = $user->roles->first();

        // Pass user and plans to the view
        return view('dashboard.admin.users.edit', compact('user','plans', 'roles', 'role', 'permissions'));

    }

    public function update(Request $request, $id)
    {
        $user = User::findOrFail($id);

        $request->validate([
            'name' => 'required|string|max:255',
            'email' => 'required|email|unique:users,email,' . $user->id,
            'password' => 'nullable|string|min:8|confirmed',
            'subscription' => 'nullable|integer|exists:plans,id',
            'role' => 'nullable|string|exists:roles,name',
        ]);

        // Update user details
        $user->update([
            'name' => $request->name,
            'email' => $request->email,
            'password' => $request->password ? Hash::make($request->password) : $user->password,
        ]);

        // Check if a subscription was submitted
        if ($request->filled('subscription')) {
            $subscription = $user->subscription;

            if ($subscription) {
                // Update the existing subscription
                $subscription->update([
                    'plan_id' => $request->subscription,
                    'is_active' => true, // Ensure subscription is active
                    'starts_at' => $subscription->starts_at ?? now(), // Set starts_at if it’s not already set
                    'ends_at' => null, // Set ends_at to null for ongoing subscriptions
                ]);
            } else {
                // Create a new subscription if none exists
                Subscription::create([
                    'user_id' => $user->id,
                    'plan_id' => $request->subscription,
                    'is_active' => true,
                    'starts_at' => now(),
                    'ends_at' => null,
                ]);
            }
        }

       if ($request->filled('role')) {
            $user->syncRoles([$request->role]);
        }

        return redirect()->route('admin.users.index')->with('success', 'User updated successfully.');
    }

    public function manage()
    {
        // Check if the authenticated user has the 'Admin' role
        if (!auth()->user()->hasRole('Admin')) {
            abort(403, 'Access denied');
        }

        $users = User::with('roles')->get(); // Pull all users with their roles
        $roles = Role::all(); // Fetch all available roles

        return view('dashboard.admin.users.index', compact('users', 'roles')); // Pass both users and roles
    }

    public function destroy($id)
    {
        $user = User::findOrFail($id);
        $user->delete();

        return redirect()->route('admin.users.index')->with('success', 'User deleted successfully.');
    }

    public function updatePassword(Request $request)
    {
        $user = Auth::user();

        $request->validate([
            'current_password' => 'required',
            'new_password' => 'required|string|min:8|confirmed',
        ]);

        // Verify the current password
        if (!Hash::check($request->current_password, $user->password)) {
            return back()->with('error', 'The current password is incorrect.');
        }

        // Check if the current password matches
        if (!Hash::check($request->current_password, $user->password)) {
            return back()->withErrors(['current_password' => 'The current password you entered is incorrect.']);
        }

        // Update the password
        $user->password = Hash::make($request->password);
        $user->save();

        return back()->with('success', 'Password successfully updated.');
    }

    public function updateName(Request $request)
    {
        $user = Auth::user();

        $request->validate([
            'name' => 'required|string|max:255',
        ]);

        // Save the new name
        $user->name = $request->name;
        $user->save();

        return back()->with('success', 'Name updated successfully.');
    }

}
