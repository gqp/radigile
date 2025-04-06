<?php

namespace App\Http\Controllers\User;

use App\Http\Controllers\Controller;
use App\Models\Invite;
use Illuminate\Support\Facades\Auth;
use Illuminate\Http\Request;

class UserInviteController extends Controller
{
    /**
     * Display a list of invites assigned to the authenticated user.
     */
    public function index()
    {
        $userId = Auth::id();

        // Get all invites created by the user
        $invites = Invite::where('invited_user_id', $userId)->get();

        return view('dashboard.user.invites.index', compact('invites'));
    }

    /**
     * Share an invite code from invites created by the user.
     */
    public function share($id)
    {
        $userId = Auth::id();

        // Fetch invite and validate ownership
        $invite = Invite::where('id', $id)->where('invited_user_id', $userId)->firstOrFail();

        // Perform any sharing logic (e.g., generate shareable link/email)
        $shareLink = route('register', ['invite_code' => $invite->code]);

        return response()->json([
            'message' => 'Invite shared successfully!',
            'share_link' => $shareLink
        ]);
    }
}
