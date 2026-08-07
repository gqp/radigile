<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Concerns\Searchable;
use App\Http\Controllers\Concerns\Sortable;
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
    use Sortable, Searchable;

    public function toggleInviteOnly(Request $request)
    {
        // Retrieve the 'status' from the request and cast it to an integer
        $status = (int) $request->input('invite_only', 0);

        // Update or create the 'invite_only' setting
        Setting::updateOrCreate(
            ['name' => 'invite_only'], // Find or create the setting by name
            ['value' => $status]      // Update its value
        );

        return redirect()->back()->with('success', "The 'Notify Me' feature has been " . ($status ? 'enabled' : 'disabled') . ".");
    }

    /**
     * Invite Management Page.
     */
    public function index()
    {
        $users = User::orderBy('name')->get();

        $inviteQuery = Invite::with(['creator', 'invitedUser']);
        $this->applySearch($inviteQuery, ['code', 'email', 'invitedUser.name', 'invitedUser.email']);
        $this->applySort($inviteQuery, ['code', 'max_uses', 'times_used', 'is_active', 'expires_at'], 'created_at', 'desc');
        $invites = $inviteQuery->paginate(15)->withQueryString();

        if (request()->ajax()) {
            return view('dashboard.admin.invites._results', compact('invites'));
        }

        $inviteOnly = Setting::get('invite_only'); // Get current invite-only toggle status

        return view('dashboard.admin.invites.index', [
            'invites' => $invites,
            'inviteOnly' => $inviteOnly,
            'users' => $users,
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
        $emails = $request->email;

        $successfulEmails = []; // Track successful emails
        $failedEmails = []; // Track failed emails

        // Batch-fetch registered users up front instead of one query per email.
        $registeredUsers = User::whereIn('email', $emails)->get()->keyBy('email');

        foreach ($emails as $email) {
            try {
                // Check if the email belongs to a registered user
                $registeredUser = $registeredUsers->get($email);

                if ($registeredUser) {
                    // If the user is registered, send a TeamInviteNotification
                    $teamName = 'Your Team Name'; // Replace this with your logic to determine the team name
                    $acceptUrl = route('team.invitation.accept', ['email' => $email]); // Replace with your accept URL logic

                    Mail::to($email)->queue(new TeamInviteNotification(null, $teamName, $acceptUrl)); // No invite code needed
                    $this->notifyInApp($registeredUser); // Notify in-app
                } else {
                    // If the user is not registered, create an invite and store the email
                    $inviteCode = Str::random(10); // Generate unique invite code

                    $invite = Invite::create([
                        'code' => $inviteCode,
                        'created_by' => $user->id,
                        'email' => $email, // Store the email for non-registered users
                        'max_uses' => $request->max_uses,
                        'expires_at' => $request->expires_at,
                        'is_active' => true,
                    ]);

                    $registrationUrl = route('register') . "?invite_code={$inviteCode}";
                    Mail::to($email)->queue(new InviteNotification($inviteCode, $registrationUrl));
                }

                $successfulEmails[] = $email;
            } catch (\Exception $e) {
                // Catch and handle errors, e.g., exception while sending email
                $failedEmails[] = $email;
            }
        }

        // Create flash messages for success and failure
        if (!empty($successfulEmails)) {
            session()->flash('success', 'Invitations sent successfully to: ' . implode(', ', $successfulEmails));
        }

        if (!empty($failedEmails)) {
            session()->flash('error', 'Failed to send invitations to: ' . implode(', ', $failedEmails));
        }

        return redirect()->back();
    }

    /**
     * Notify a registered user in-app about the team invitation.
     */
    protected function notifyInApp(User $user)
    {
        // Replace with your app's logic for creating in-app notifications
        $user->notifications()->create([
            'message' => 'You have been invited to join a team.',
            'type' => 'team_invitation',
            'data' => [], // Additional data if needed
        ]);
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

    public function createForRegisteredUser(Request $request)
    {
        $request->validate([
            'email' => 'required|email|exists:users,email', // Ensure the email is for a registered user
            'max_uses' => 'required|integer|min:1',
            'expires_at' => 'nullable|date',
        ]);

        $admin = Auth::user(); // Ensure only admins can initiate it
        $inviteCode = Str::random(10); // Generate a unique code

        // Find the registered user
        $user = User::where('email', $request->email)->first();

        // Create the invite
        $invite = Invite::create([
            'code' => $inviteCode,
            'created_by' => $admin->id, // Admin who created it
            'invited_user_id' => $user->id,
            'max_uses' => $request->max_uses,
            'expires_at' => $request->expires_at,
            'is_active' => true,
        ]);

        // Notify the registered user about the invite
        $registrationUrl = route('register') . "?invite_code={$inviteCode}";
        Mail::to($request->email)->queue(new InviteNotification($inviteCode, $registrationUrl));

        return redirect()->back()->with('success', 'Invite sent successfully to the registered user.');
    }
    
}
