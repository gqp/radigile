<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\NotifyMe;

class AdminNotifyController extends Controller
{
    // Show all "Notify Me" submissions to admins
    public function index()
    {
        $submissions = NotifyMe::all();  // Retrieve all submissions
        return view('dashboard.admin.notify.index', compact('submissions'));
    }

    // Toggle the "Notify Me" status
    public function toggle($id)
    {
        $submission = NotifyMe::findOrFail($id);
        $submission->notify_status = !$submission->notify_status;  // Toggle the status
        $submission->save();

        return redirect()->back()->with('success', 'Notification status updated successfully!');
    }
}
