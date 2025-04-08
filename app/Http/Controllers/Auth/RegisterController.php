<?php

namespace App\Http\Controllers\Auth;

use App\Http\Controllers\Controller;
use App\Models\User;
use App\Models\Invite;
use App\Models\Setting;
use Illuminate\Foundation\Auth\RegistersUsers;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Validator;

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

        // Create the user
        $user = User::create([
            'name' => $data['name'],
            'email' => $data['email'],
            'password' => Hash::make($data['password']),
        ]);

        // Assign default role (User)
        $user->assignRole('User');

        // Mark the invite as used if applicable
        if ($invite) {
            $invite->update(['times_used' => $invite->times_used + 1, 'invited_user_id' => $user->id]);
        }

        return $user;
    }
}
