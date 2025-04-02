@extends('layouts.admin.admin')

@section('title', 'Admin Dashboard')

@section('content')

    @if (session('status'))
        <div class="alert alert-success">
            {{ session('status') }}
        </div>
    @endif

    @if (session('message'))
        <div class="alert alert-info">
            {{ session('message') }}
        </div>
    @endif

    @if ($errors->any())
        <div class="alert alert-danger">
            <ul>
                @foreach ($errors->all() as $error)
                    <li>{{ $error }}</li>
                @endforeach
            </ul>
        </div>
    @endif

    <div class="container">
        <h1 class="my-4">Admin Dashboard</h1>
        <nav class="dashboard-nav">
            <a href="#" onclick="event.preventDefault(); document.getElementById('logout-form').submit();">Logout</a>
        </nav>
        <!-- Normal User's Logout Button -->
        <form id="logout-form" action="{{ route('logout') }}" method="POST" style="">
            <input type="hidden" name="_token" value="{{ csrf_token() }}">
            <button type="submit">Logout</button>
        </form>
        <div class="card">
            <div class="card-header">
                <h2>Welcome, Admins!</h2>
            </div>
            <div class="card-body">
                <p>You have full access to the system. Here are your admin privileges:</p>

                <ul class="list-group">
                    <li class="list-group-item">User Management</li>
                    <li class="list-group-item">Content Management</li>
                    <li class="list-group-item">System Reports</li>
                    <li class="list-group-item">Audit Logs</li>
                </ul>
            </div>
        </div>
    </div>

@endsection
