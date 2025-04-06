<nav class="navbar navbar-expand-lg navbar-light bg-light">
    <div class="container">
        <a class="navbar-brand" href="{{ route('home') }}">Radigile.com - A New Era of Team Growth</a>
        <button
            class="navbar-toggler"
            type="button"
            data-bs-toggle="collapse"
            data-bs-target="#navbarNav"
            aria-controls="navbarNav"
            aria-expanded="false"
            aria-label="Toggle navigation">
            <span class="navbar-toggler-icon"></span>
        </button>

        <div class="collapse navbar-collapse" id="navbarNav">
            <ul class="navbar-nav ms-auto">
                {{-- Guest Links --}}
                @guest
                    <li class="nav-item">
                        <a class="nav-link {{ request()->is('/') ? 'active' : '' }}" href="{{ route('home') }}">Home</a>
                    </li>
                    <li class="nav-item">
                        <a class="nav-link {{ request()->is('about') ? 'active' : '' }}" href="{{ route('about') }}">About</a>
                    </li>
                    <li class="nav-item">
                        <a class="nav-link {{ request()->routeIs('login') ? 'active' : '' }}" href="{{ route('login') }}">Login</a>
                    </li>
                    @if (Route::has('register'))
                        <li class="nav-item">
                            <a class="nav-link {{ request()->routeIs('register') ? 'active' : '' }}" href="{{ route('register') }}">Register</a>
                        </li>
                    @endif
                @endguest

                {{-- Authenticated User Links --}}
                @auth
                    {{-- Admin-Specific Links --}}
                    @if (auth()->user()->hasRole('Admin'))
                        <li class="nav-item">
                            <a class="nav-link {{ request()->routeIs('admin.dashboard') ? 'active' : '' }}" href="{{ route('admin.dashboard') }}">Admin Dashboard</a>
                        </li>
                        <li class="nav-item">
                            <a class="nav-link {{ request()->routeIs('admin.profile') ? 'active' : '' }}" href="{{ route('admin.profile') }}">Profile</a>
                        </li>
                        <li class="nav-item">
                            <a class="nav-link {{ request()->routeIs('admin.settings') ? 'active' : '' }}" href="{{ route('admin.settings') }}">Settings</a>
                        </li>
                        <li class="nav-item">
                            <a class="nav-link {{ request()->routeIs('roles.index') ? 'active' : '' }}" href="{{ route('roles.index') }}">Manage Roles</a>
                        </li>
                        <li class="nav-item">
                            <a class="nav-link {{ request()->routeIs('admin.invites.index') ? 'active' : '' }}" href="{{ route('admin.invites.index') }}">Manage Invites</a>
                        </li>
                        <li class="nav-item">
                            <a class="nav-link {{ request()->routeIs('admin.manage-users') ? 'active' : '' }}" href="{{ route('admin.manage-users') }}">Manage Users</a>
                        </li>
                    @endif

                    {{-- Regular User Links --}}
                    @if (auth()->user()->hasRole('User'))
                        <li class="nav-item">
                            <a class="nav-link {{ request()->routeIs('user.dashboard') ? 'active' : '' }}" href="{{ route('user.dashboard') }}">User Dashboard</a>
                        </li>
                        <li class="nav-item">
                            <a class="nav-link {{ request()->routeIs('user.profile') ? 'active' : '' }}" href="{{ route('user.profile') }}">Profile</a>
                        </li>
                        <li class="nav-item">
                            <a class="nav-link {{ request()->routeIs('user.invites') ? 'active' : '' }}" href="{{ route('user.invites') }}">
