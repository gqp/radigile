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
    public function toggleGlobal()
    {
        Setting::toggle('notify_me'); // Toggle the "Notify Me" setting
        $newStatus = Setting::get('notify_me') ? 'enabled' : 'disabled';

        return redirect()->back()->with('success', "The 'Notify Me' feature has been {$newStatus}.");
    }
}
