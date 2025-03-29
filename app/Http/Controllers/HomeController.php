<?php

namespace App\Http\Controllers;
use Illuminate\Http\Request;

class HomeController extends Controller
{
    public function index()
    {
        // Pass any required data to the homepage view
        return view('homepage');
    }
}
