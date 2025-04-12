<?php

namespace App\Http\Controllers\Auth;

use App\Http\Controllers\Controller;
use App\Models\User;
use App\Models\Plan;
use App\Models\Invite;
use App\Models\Setting;
use App\Models\Subscription;
use Illuminate\Foundation\Auth\RegistersUsers;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Validator;
use Illuminate\Contracts\Auth\MustVerifyEmail;

class RegisterController extends Controller
{
    use RegistersUsers;

    /**
     * Where to redirect users after registration.
     *
     * @var string
     */
    protected $redirectTo = '/user/dashboard';

    /**
     * Create a new controller instance.
     *
     * @return void
     */
    public function __construct()
    {
        $this->middleware('guest');
    }

    /**
     * Show the registration form.
     * Passes the invite-only setting to the view.
     *
     * @return \Illuminate\Contracts\View\View|\Illuminate\Contracts\View\Factory
     */
    public function showRegistrationForm()
    {
        // Fetch the invite-only setting from the database
        $inviteOnly = Setting::get('invite_only');

        // Pass invite-only status to the registration view
        return view('auth.register', compact('inviteOnly'));
    }

    /**
     * Get a validator for an incoming registration request.
     *
     * @param array $data
     * @return \Illuminate\Contracts\Validation\Validator
     */
    protected function validator(array $data)
    {
        // Check if Invite-Only mode is enabled
        $isInviteOnly = Setting::get('invite_only');

        // Define basic rules
        $rules = [
            'name' => ['required', 'string', 'max:255'],
            'email' => ['required', 'string', 'email', 'max:255', 'unique:users'],
            'password' => ['required', 'string', 'min:8', 'confirmed'],
        ];

        // Add invite code validation if Invite-Only mode is active
        if ($isInviteOnly) {
            $rules['invite_code'] = ['required', 'exists:invites,code'];
        }

        return Validator::make($data, $rules);
    }

    /**
     * Create a new user instance after a valid registration.
     *
     * @param array $data
     * @return \App\Models\User
     */
    protected function create(array $data)
    {
        // Check if Invite-Only mode is enabled
        $isInviteOnly = Setting::get('invite_only');

        // If Invite Only is enabled, verify the invite code
        $invite = null;
        if ($isInviteOnly) {
            $invite = Invite::where('code', $data['invite_code'])->first();

            if (!$invite || !$invite->isValid()) {
                abort(403, 'Invalid invite code');
            }
        }

        // Fetch the free plan
        $freePlan = Plan::where('name', 'Free')->first();

        // Create the user
        $user = User::create([
            'name' => $data['name'],
            'email' => $data['email'],
            'password' => Hash::make($data['password']),
        ]);

        // Assign default role (User)
        $user->assignRole('User');

        // Assign the user a free subscription if the freePlan exists
        if ($freePlan) {
            Subscription::create([
                'user_id' => $user->id,
                'plan_id' => $freePlan->id,
                'starts_at' => now(),
                'ends_at' => null, // Free plan has no end date
                'is_active' => true,
            ]);
        }

        // Mark the invite as used if applicable
        if ($invite) {
            $invite->update(['times_used' => $invite->times_used + 1, 'invited_user_id' => $user->id]);
        }

        return $user;
    }

    /**
     * Index method to handle any additional logic for the registration controller.
     * (Restored to retain functionality.)
     *
     * @param Request $request
     * @return \Illuminate\Contracts\View\View|\Illuminate\Contracts\View\Factory
     */
    public function index(Request $request)
    {
        // Example implementation; adjust as per your requirements
        return view('auth.register', [
            'users' => User::all(),
            'inviteOnly' => Setting::get('invite_only'), // Include invite-only status
        ]);
    }
}
