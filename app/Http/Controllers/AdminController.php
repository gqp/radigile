<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;

class AdminController extends Controller
{
    public function index()
    {
        return view("dashboard.admin.index");
    }
    public function profile()
    {
        return view("dashboard.admin.profile");
    }
    public function settings()
    {
        return view("dashboard.admin.settings");
    }
}
