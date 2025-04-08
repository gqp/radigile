<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\NotifyMe;
use App\Models\Setting;

class AdminNotifyController extends Controller
{
    /**
     * Show all "Notify Me" submissions to admins
     */
    public function index()
    {
        $submissions = NotifyMe::all();  // Retrieve all submissions
        $notifyMeStatus = Setting::get('notify_me'); // Fetch the global Notify Me status
        return view('dashboard.admin.notify.index', compact('submissions', 'notifyMeStatus'));
    }

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
}
