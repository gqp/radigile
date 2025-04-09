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

    public function toggleInviteOnly(Request $request)
    {
        $status = $request->input('status', false);

       ßSetting::updateOrCreate(
            ['name' => 'invite_only'], // Find the 'invite_only' setting
            ['value' => $status]      // Update its value
        );

        return redirect()->route('admin.invites.index')->with('success', 'Invite-only status updated successfully.');
    }


    /**
     * Invite Management Page.
     */
    public function index()
    {
        $invites = Invite::with(['creator', 'invitedUser'])->get(); // Eager load relationships
        $inviteOnly = Setting::get('invite_only'); // Get current invite-only toggle status

        return view('dashboard.admin.invites.index', [
            'invites' => $invites,
            'inviteOnly' => $inviteOnly,
        ]);
    }

    /**
     * Generate and send invitation.
     */
    public function store(Request $request)
    {
        $request->validate([
            'email' => 'required|array', // Email must be an array
            'email.*' => 'required|email', // Each item in the array must be a valid email
            'max_uses' => 'required|integer|min:1',
            'expires_at' => 'nullable|date',
        ]);

        $user = Auth::user(); // Get the authenticated user

        // Check if user has remaining invites
        if ($user->remaining_invites < count($request->email)) {
            return redirect()->back()->withErrors(['You do not have enough remaining invites.']);
        }

        foreach ($request->email as $email) {
            // Generate a unique invite code
            $code = Str::random(10);

            // Create the invite
            $invite = Invite::create([
                'code' => $code,
                'created_by' => $user->id,
                'max_uses' => $request->max_uses,
                'expires_at' => $request->expires_at,
            ]);

            // Send email to the provided address
            Mail::to($email)->send(new InviteNotification($invite->code));

            // Decrease the user's remaining invites
            $user->remaining_invites -= 1;
            $user->save();
        }

        return redirect()->route('admin.invites.index')->with('success', 'Invites sent successfully.');
    }


    /**
     * Enable ae disabled invite.
     */
    public function enable($id)
    {
        $invite = Invite::findOrFail($id);
        $invite->update(['is_active' => true]);

        return redirect()->route('admin.invites.index')->with('success', 'Invite enabled successfully!');
    }

    /**
     * Disable an enabled invite.
     */
    public function disable($id)
    {
        $invite = Invite::findOrFail($id);
        $invite->update(['is_active' => false]);

        return redirect()->route('admin.invites.index')->with('success', 'Invite disabled successfully!');
    }

    /**
     * Update an invite's details (e.g., max uses, expiration date).
     */
    public function update(Request $request, $id)
    {
        $request->validate([
            'max_uses' => 'required|integer|min:1',
            'expires_at' => 'nullable|date',
        ]);

        $invite = Invite::findOrFail($id);
        $invite->update([
            'max_uses' => $request->max_uses,
            'expires_at' => $request->expires_at,
        ]);

        return redirect()->route('admin.invites.index')->with('success', 'Invite updated successfully.');
    }
}
