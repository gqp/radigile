<?php

namespace App\Http\Controllers\Auth;

use App\Http\Controllers\Controller;
use Illuminate\Foundation\Auth\AuthenticatesUsers;
use Illuminate\Http\Request;
use Illuminate\Http\RedirectResponse;
use Illuminate\Support\Facades\Cache;


class LoginController extends Controller
{
    use AuthenticatesUsers;

    /**
     * Where to redirect users after login.
     *
     * @return string
     */

    protected function authenticated(Request $request, $user)
    {
        return redirect()->intended($this->redirectTo());
    }
    /**
     * Redirect users after login based on their role.
     */
    protected function redirectTo()
    {
        $user = auth()->user();

        if ($user->hasRole('Admin')) {
            return route('admin.dashboard');
        }

        if ($user->hasRole('User')) {
            return route('user.dashboard');
        }

        return '/'; // Default fallback
    }

    /**
     * Create a new controller instance.
     *
     * @return void
     */
    public function __construct()
    {
        $this->middleware('guest')->except('logout');
    }

    /**
     * Handle a login request to the application.
     */
    public function login(Request $request)
    {
        // Validate the incoming request
        $request->validate([
            'email' => 'required|email',
            'password' => 'required|string|min:6',
        ]);

        // Attempt authentication
        if (auth()->attempt($request->only(['email', 'password']))) {

            // Fetch User
            $user = auth()->user();

            // Explicitly reload roles from the database
            $user->load('roles');

            \Log::info('User logged in successfully:', [
                'user_id' => $user->id,
                'roles' => $user->getRoleNames(),
            ]);

            // Bust role cache
            app()[\Spatie\Permission\PermissionRegistrar::class]->forgetCachedPermissions();

            return redirect()->intended($this->redirectTo()); // redirect after successful login
        }

        // Failed authentication
        \Log::error('Login attempt failed:', ['email' => $request->email]);
        return back()->withErrors(['email' => 'Invalid credentials provided.']);
    }

    /**
     * Log the user out and clean up the session.
     */
    public function logout(Request $request)
    {
        // Get user ID before logging out
        $userId = auth()->id();

        // Log the user out
        auth()->logout();

        // Invalidate the current session
        $request->session()->invalidate();
        $request->session()->regenerateToken();

        // Clean up session table for the user
        if (!is_null($userId)) {
            \DB::table('sessions')->where('user_id', $userId)->delete();
        }

        // Clear role/permission cache for this user
        Cache::forget("spatie.permission.cache");

        \Log::info('User logged out successfully:', ['user_id' => $userId]);

        return redirect('/')->with('success', 'You have been logged out successfully.');
    }

}
