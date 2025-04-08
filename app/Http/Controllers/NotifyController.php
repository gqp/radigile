<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;

class NotifyController extends Controller
{
    public function store(Request $request)
    {
        $request->validate([
            'name' => 'required|string|max:255',
            'email' => 'required|email|max:255',
            'company' => 'nullable|string|max:255',
        ]);

        // Here you could save the data to your database or further process it
        // Example:
        // NotifyMe::create($request->only(['name', 'email', 'company']));

        return redirect()->back()->with('success', 'Thank you! We’ll notify you soon.');
    }
}
