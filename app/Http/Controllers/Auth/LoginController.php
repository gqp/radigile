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
//            \Log::info('RedirectTo: Redirecting Admin user.', ['user_id' => $user->id]);
            return route('admin.dashboard');
        }

        if ($user->hasRole('User')) {
//            \Log::info('RedirectTo: Redirecting User.', ['user_id' => $user->id]);
            return route('user.dashboard');
        }

//        \Log::warning('RedirectTo: No role matched, sending to default route.', ['user_id' => $user->id]);
        return '/'; // Fallback
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
            // Bust role cache
            app()[\Spatie\Permission\PermissionRegistrar::class]->forgetCachedPermissions();

//            $userRoles = $user->getRoleNames()->toArray();

//            \Log::info('User logged in successfully:', [
//                'user_id' => $user->id,
//                'roles' => $userRoles(),
//            ]);

            return redirect()->intended($this->redirectTo()); // redirect after successful login
        }

        // Failed authentication
//        \Log::error('Login attempt failed:', ['email' => $request->email]);
        return back()->withErrors(['email' => 'Invalid credentials provided.']);
    }

    /**
     * Log the user out and clean up the session.
     */
    public function logout(Request $request)
    {
        $user = auth()->user();
//        $userRoles = $user ? $user->getRoleNames()->toArray() : [];


        // Log current session state
//        \Log::info('Logging out user:', [
//            'user_id' => $user->id ?? null,
//            'user_roles' => $userRoles,
//        ]);

        // Ensure Spatie cache is cleared for this user
        app()[\Spatie\Permission\PermissionRegistrar::class]->forgetCachedPermissions();

        // Perform logout
        auth()->logout();

        // Invalidate and regenerate session
//        \Log::info('Invalidating session...');
        $request->session()->invalidate();
        $request->session()->regenerateToken();

        // Clear any session rows in the database (if using SESSION_DRIVER=database)
        if (!is_null($user)) {
            \DB::table('sessions')->where('user_id', $user->id)->delete();
        }

//        \Log::info('User logged out and session cleared:', ['user_id' => $user->id]);

        return redirect('/')->with('success', 'You have been logged out successfully.');
    }


}
