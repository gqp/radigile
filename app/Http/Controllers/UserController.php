<?php

namespace App\Http\Controllers;

use App\Mail\NewUserNotification;
use App\Models\Plan;
use App\Models\Subscription;
use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Str;
use Spatie\Permission\Models\Permission;
use Spatie\Permission\Models\Role;

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
        // Step 1: Validate the request data
        $request->validate([
            'name' => 'required|string|max:255',
            'email' => 'required|email|unique:users,email',
            'password' => 'nullable|string|min:8|confirmed',
            'role' => 'required|string|exists:roles,name',
            'test_user' => 'nullable|boolean',
            'send_notification' => 'nullable|boolean',
            'subscription' => 'nullable|integer|exists:plans,id', // Validate subscription plan
        ]);

        try {
            // Step 2: Determine if this is a test user or non-test user
            $isTestUser = $request->boolean('test_user', false);
            $sendNotification = $request->boolean('send_notification', false);

            // Generate a random password if none is provided
            $password = $request->password ?: Str::random(12);

            // Set force_password_reset based on the type of user and password entry
            $forcePasswordReset = (!$isTestUser && !$request->filled('password')) || ($isTestUser && !$request->filled('password'));

            // Step 3: Create the User
            $user = User::create([
                'name' => $request->name,
                'email' => $request->email,
                'password' => Hash::make($password),
                'force_password_reset' => $forcePasswordReset,
                'email_verified_at' => now(), // Automatically mark email as verified
            ]);

            // Assign the selected role to the user
            $user->assignRole($request->role);

            // Step 4: Handle Notifications
            if ($isTestUser) {
                // For Test Users: Only send the notification if explicitly requested
                if ($sendNotification) {
                    $user->notify(new NewUserNotification($password, $isTestUser));
                }
            } else {
                // For Non-Test Users: Always send the notification
                $user->notify(new NewUserNotification($password, $isTestUser));
            }

            // Step 5: Handle Subscription (Optional)
            if ($request->filled('subscription')) {
                $plan = Plan::findOrFail($request->subscription);

                Subscription::create([
                    'user_id' => $user->id,
                    'plan_id' => $plan->id,
                    'starts_at' => now(),
                    'ends_at' => $plan->interval === 'monthly' ? now()->addMonth() : now()->addYear(),
                    'is_active' => true,
                ]);
            }

            // Step 6: Redirect with success message
            return redirect()->route('admin.users.index')
                ->with('success', 'User created successfully.');
        } catch (\Exception $e) {
            \Log::error('User Creation Failed:', ['error' => $e->getMessage()]);
            return redirect()->back()->withErrors(['error' => 'There was an issue creating the user.']);
        }
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
            'is_active' => 'required|boolean',
        ]);

        // Update user details
        $user->update([
            'name' => $request->name,
            'email' => $request->email,
            'password' => $request->password ? Hash::make($request->password) : $user->password,
            'is_active' => $request->is_active,
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

        // Prevent self-deletion
        if (Auth::id() === $user->id) {
            return redirect()->route('admin.users.index')->with('error', 'You cannot delete yourself.');
        }

        // Delete the user
        $user->delete();

        return redirect()->route('admin.users.index')->with('success', 'User deleted successfully.');
    }

    public function updatePassword(Request $request)
    {
        $user = Auth::user();

        $request->validate([
            'email' => 'required|email|exists:users,email',
            'password' => 'required|string|min:8|confirmed',
        ]);

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
    public function showPasswordResetForm(Request $request)
    {
        $token = $request->route('token'); // Retrieve token from the route (if applicable)
        $email = $request->email; // Get the email if provided as a query string (optional)

        return view('auth.passwords.reset', compact('token', 'email'));
    }

    public function processPasswordReset(Request $request)
    {
        $request->validate([
            'password' => 'required|string|min:8|confirmed',
        ]);

        $user = Auth::user();
        $user->password = Hash::make($request->password);
        $user->force_password_reset = 0; // Disable forced password reset after setting a new password
        $user->save();

        return redirect()->route('user.dashboard')->with('success', 'Password reset successfully.');
    }


}
