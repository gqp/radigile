<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Concerns\Searchable;
use App\Http\Controllers\Concerns\Sortable;
use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Mail;
use App\Models\NotifyMe;
use App\Models\Setting;
use App\Models\Invite;
use App\Mail\InviteNotification;

class AdminNotifyController extends Controller
{
    use Sortable, Searchable;

    /**
     * Toggle the global "Notify Me" feature on/off.
     */
    public function toggleGlobal(Request $request)
    {
        // Make sure the request has the 'notify_me' value
        $newStatus = (int) $request->input('notify_me', 0);

        // Update the setting in the database
        Setting::where('name', 'notify_me')->update(['value' => $newStatus]);

        return redirect()->back()->with('success', "The 'Notify Me' feature has been " . ($newStatus ? 'enabled' : 'disabled') . ".");
    }

    /**
     * Show all "Notify Me" submissions to admins
     */
    public function index()
    {
        $query = NotifyMe::query();
        $this->applySearch($query, ['name', 'email', 'company']);
        $this->applySort($query, ['name', 'email', 'company', 'created_at'], 'created_at', 'desc');
        $submissions = $query->paginate(15)->withQueryString();

        if (request()->ajax()) {
            return view('dashboard.admin.notify._results', compact('submissions'));
        }

        $notifyMeStatus = Setting::get('notify_me'); // Fetch the global Notify Me status
        return view('dashboard.admin.notify.index', compact('submissions', 'notifyMeStatus'));
    }

    /**
     * Send Invite based on "Notify Me" Submission
     */
    public function sendInvite(Request $request, $id)
    {
        $submission = NotifyMe::findOrFail($id);

        // Validate Input
        $request->validate([
            'max_uses' => 'required|integer|min:1',
            'expires_at' => 'nullable|date',
        ]);

        // Generate Invite
        $invite = Invite::create([
            'code' => \Str::random(10),
            'created_by' => auth()->id(),
            'max_uses' => $request->max_uses,
            'expires_at' => $request->expires_at,
        ]);

        // Queue the email so this request doesn't block on the mail provider.
        $registrationUrl = route('register') . "?invite_code={$invite->code}";
        Mail::to($submission->email)->queue(new InviteNotification($invite->code, $registrationUrl));

        return redirect()->back()->with('success', 'Invite sent successfully!');
    }
}
