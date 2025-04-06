<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Mail\InviteNotification;
use App\Models\Invite;
use App\Models\User;
use App\Models\Setting;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Mail;
use Illuminate\Support\Str;

class InviteController extends Controller
{
    /**
     * Display the Invite Management Page.
     */
    public function index()
    {
        $invites = Invite::all();
        $inviteOnly = Setting::get('invite_only'); // Get current invite-only toggle status
        $users = User::all(); // Get all registered users for dropdown

        return view('admin.invites.index', compact('invites', 'inviteOnly', 'users'));
    }

    /**
     * Generate invite and send invitation.
     */
    public function store(Request $request)
    {
        $request->validate([
            'email' => 'nullable|email', // For non-registered users
            'user_id' => 'nullable|exists:users,id', // For registered users
            'max_uses' => 'required|integer|min:1',
            'expires_at' => 'nullable|date',
        ]);

        // Generate a unique invite code
        $code = Str::random(10);

        // Create the invite
        $invite = Invite::create([
            'code' => $code,
            'created_by' => Auth::id(),
            'max_uses' => $request->max_uses,
            'expires_at' => $request->expires_at,
        ]);

        // Notify the user via email
        if ($request->email) {
            // Send invite link to non-registered user's email
            Mail::to($request->email)->send(new InviteNotification($invite->code));
        }

        if ($request->user_id) {
            // Get the registered user and send them the invite notification
            $user = User::find($request->user_id);
            Mail::to($user->email)->send(new InviteNotification($invite->code));
        }

        return redirect()->route('admin.invites.index')->with('success', 'Invite generated and email notifications sent successfully!');
    }

    /**
     * Disable an active invite.
     */
    public function disable($id)
    {
        $invite = Invite::findOrFail($id);
        $invite->update(['is_active' => false]);

        return redirect()->route('admin.invites.index')->with('success', 'Invite disabled successfully!');
    }
}
