<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Invite;
use App\Models\Setting;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Str;

class InviteController extends Controller
{
    /**
     * Display invite management page.
     */
    public function index()
    {
        $invites = Invite::all(); // Fetch all invites
        $inviteOnly = Setting::where('name', 'invite_only')->value('value');
        return view('admin.invites.index', compact('invites', 'inviteOnly'));
    }

    /**
     * Generate a new invite code.
     */
    public function store(Request $request)
    {
        $request->validate([
            'max_uses' => 'required|integer|min:1',
            'expires_at' => 'nullable|date',
        ]);

        $code = Str::random(10); // Generate random code

        Invite::create([
            'code' => $code,
            'created_by' => Auth::id(),
            'max_uses' => $request->max_uses,
            'expires_at' => $request->expires_at,
        ]);

        return redirect()->route('admin.invites.index')->with('success', 'Invite created successfully!');
    }

    /**
     * Toggle invite-only mode.
     */
    public function toggleInviteOnly()
    {
        // Use the Setting model to toggle the "invite_only" feature
        Setting::toggle('invite_only');

        return redirect()->route('admin.invites.index')->with('success', 'Invite-only mode toggled successfully!');
    }

}
