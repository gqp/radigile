<?php

namespace App\Http\Controllers\Auth;

use App\Http\Controllers\Controller;
use Illuminate\Foundation\Auth\AuthenticatesUsers;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class LoginController extends Controller
{
    use AuthenticatesUsers;

    /**
     * Where to redirect users after login.
     */
    protected function redirectTo()
    {
        return route('dashboard');
    }

    /**
     * Handle post-login actions.
     */
    protected function authenticated(Request $request, $user)
    {
        \Log::info('User logged in successfully.', ['user_id' => $user->id]);

        // Check if the user needs to reset their password
        if ($user->force_password_reset) {
            \Log::info('Force password reset triggered for user.', ['user_id' => $user->id]);
            return redirect()->route('password.force.reset')->with('warning', 'You must reset your password before proceeding.');
        }

        // Proceed to the intended location
        return redirect()->intended($this->redirectTo());
    }

    /**
     * Handle a login request.
     */
    public function login(Request $request)
    {
        $request->validate([
            'email' => 'required|email',
            'password' => 'required|string|min:6',
        ]);

        if (auth()->attempt($request->only('email', 'password'))) {
            // Regenerate the session ID to prevent session fixation.
            $request->session()->regenerate();

            $user = auth()->user();

            // Reload roles and other states
            $user->load('roles');

            // Update the last login timestamp
            $user->update([
                'last_login_at' => now(),
            ]);

            // Clear permissions cache (Spatie)
            app()[\Spatie\Permission\PermissionRegistrar::class]->forgetCachedPermissions();

            return $this->authenticated($request, $user);
        }

        return back()->withErrors(['email' => 'Invalid credentials provided.']);
    }

    /**
     * Log the user out and clean up the session.
     */
    public function logout(Request $request)
    {
        $user = auth()->user();

        // Clear permission cache for the user (Spatie)
        app()[\Spatie\Permission\PermissionRegistrar::class]->forgetCachedPermissions();

        // Perform logout
        auth()->logout();

        // Invalidate and regenerate session
        $request->session()->invalidate();
        $request->session()->regenerateToken();

        // Clear any session rows in the database (if SESSION_DRIVER=database)
        if (!is_null($user)) {
            \DB::table('sessions')->where('user_id', $user->id)->delete();
        }

        return redirect('/')->with('success', 'You have been logged out successfully.');
    }

    public function __construct()
    {
        $this->middleware('guest')->except('logout');
        $this->middleware('throttle:login')->only('login');
    }
}
