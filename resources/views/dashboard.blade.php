@extends('layouts.app')

@section('title', 'Dashboard')

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
        <header class="dashboard-header">
            <h1>Welcome to Your Dashboard</h1>
        </header>

        <nav class="dashboard-nav">
            <a href="#" onclick="event.preventDefault(); document.getElementById('logout-form').submit();">Logout</a>
        </nav>

        <main class="dashboard-main">
            <h2>Dashboard Overview</h2>
            <p>
                This is your dashboard where you can see all your activities, notifications, and updates at a glance.
            </p>
            <p>Start exploring your application now!</p>
        </main>

        <footer class="dashboard-footer">
            &copy; {{ date('Y') }} Radigile, All rights reserved.
        </footer>

        <!-- Normal User's Logout Button -->
        <form id="logout-form" action="{{ route('logout') }}" method="POST" style="display: none;">
            @csrf
        </form>
    </div>
@endsection
