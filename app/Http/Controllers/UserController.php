<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\User;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Auth;

class UserController extends Controller
{
    public function index()
    {
        $user = auth()->user();

        // Avoid redirection loop for Admin
        if ($user->hasRole('Admin')) {
            if (request()->route()->getName() !== 'admin.dashboard') {
                return redirect()->route('admin.dashboard');
            }
        }

        // Avoid redirection loop for User
        if ($user->hasRole('User')) {
            if (request()->route()->getName() !== 'user.dashboard') {
                return redirect()->route('user.dashboard');
            }
        }
        dd(auth()->user()->roles->pluck('name')); // Verify roles attached to the authenticated user
        // Default fallback if no role is assigned
        return redirect('/')->with('error', 'Unauthorized access.');
    }

    public function profile()
    {
        $user = Auth::user(); // Get the currently authenticated user
        return view('dashboard.user.profile', compact('user')); // Pass user data to the profile view
    }


    public function create()
    {
        return view("dashboard.admin.users.create");
    }

    public function store(Request $request)
    {
        $request->validate([
            'name' => 'required|string|max:255',
            'email' => 'required|email|unique:users,email',
            'password' => 'required|string|min:8|confirmed',
        ]);

        User::create([
            'name' => $request->name,
            'email' => $request->email,
            'password' => Hash::make($request->password),
        ]);

        return redirect()->route('admin.users.index')->with('success', 'User created successfully.');
    }

    public function edit($id)
    {
        $user = User::findOrFail($id);
        return view("dashboard.admin.users.edit", compact('user'));
    }

    public function update(Request $request, $id)
    {
        $user = User::findOrFail($id);

        $request->validate([
            'name' => 'required|string|max:255',
            'email' => 'required|email|unique:users,email,' . $user->id,
            'password' => 'nullable|string|min:8|confirmed',
        ]);

        $user->update([
            'name' => $request->name,
            'email' => $request->email,
            'password' => $request->password ? Hash::make($request->password) : $user->password,
        ]);

        return redirect()->route('admin.users.index')->with('success', 'User updated successfully.');
    }

    public function manage()
    {
        // Check if the authenticated user has the 'Admin' role
        if (!auth()->user()->hasRole('Admin')) {
            abort(403, 'Access denied');
        }


        $users = User::all(); // Pull all users
        return view('dashboard.admin.users.manage', compact('users')); // Send users to admin manage view
    }


    public function destroy($id)
    {
        $user = User::findOrFail($id);
        $user->delete();

        return redirect()->route('admin.users.index')->with('success', 'User deleted successfully.');
    }
}
