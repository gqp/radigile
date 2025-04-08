<?php

namespace App\Http\Controllers;

use App\Models\NotifyMe;
use Illuminate\Http\Request;

class NotifyController extends Controller
{
    public function store(Request $request)
    {
        // Validate the incoming request data
        $request->validate([
            'name' => 'required|string|max:255',
            'email' => 'required|email|max:255',
            'company' => 'nullable|string|max:255',
        ]);

        // Store the data in the database
        NotifyMe::create([
            'name' => $request->input('name'),
            'email' => $request->input('email'),
            'company' => $request->input('company'),
        ]);

        // Redirect back with a success message
        return redirect()->back()->with('success', 'Thank you! We’ll notify you soon.');
    }
}
