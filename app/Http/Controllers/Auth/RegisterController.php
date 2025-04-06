<?php

namespace App\Http\Controllers\Auth;

use App\Http\Controllers\Controller;
use App\Models\User;
use App\Models\Invite;
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
     * @param  array  $data
     * @return \Illuminate\Contracts\Validation\Validator
     */
    protected function validator(array $data)
    {
        return Validator::make($data, [
            'name' => ['required', 'string', 'max:255'],
            'nId' => ['required', 'integer', 'digits:14', 'unique:users,nid'],
            'email' => ['required', 'string', 'email', 'max:255', 'unique:users'],
            'password' => ['required', 'string', 'min:8', 'confirmed'],
            'invite_code' => ['required', 'exists:invites,code'], // Validate code exists
        ]);
    }

    /**
     * Create a new user instance after a valid registration.
     *
     * @param  array  $data
     * @return \App\Models\User
     */
    protected function create(array $data)
    {
        // Find invite
        $invite = Invite::where('code', $data['invite_code'])->first();

        if (!$invite || !$invite->isValid()) {
            abort(403, 'Invalid invite code');
        }

        // Register user
        $user = User::create([
            'name' => $data['name'],
            'nId' => $data['nId'],
            'email' => $data['email'],
            'password' => Hash::make($data['password']),
        ]);

        // Assign default role (User)
        $user->assignRole('User');

        // Mark invite as used
        $invite->update(['times_used' => $invite->times_used + 1, 'invited_user_id' => $user->id]);

        return $user;
    }
}
