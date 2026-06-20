<nav class="navbar navbar-expand-lg navbar-light bg-light">
    <div class="container">
        {{-- Brand/Logo --}}
        <a class="navbar-brand" href="{{ route('home') }}">
            <img src="{{ asset('imgs/logo-purple.png') }}" alt="Logo" class="img-fluid" style="max-height: 80px;">
        </a>

        {{-- Navbar Toggler --}}
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

        {{-- Navbar Menu --}}
        <div class="collapse navbar-collapse" id="navbarNav">
            <ul class="navbar-nav ms-auto custom-nav">
                {{-- About Link --}}
                <li class="nav-item">
                    <a class="nav-link {{ request()->is('about') ? 'active' : '' }}" href="{{ route('about') }}">About</a>
                </li>

                {{-- Guest Links --}}
                @guest
                    <li class="nav-item">
                        <a class="nav-link {{ request()->is('/') ? 'active' : '' }}" href="{{ route('home') }}">Home</a>
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
                    {{-- Dashboard Link (Admins only) --}}
                    @if (auth()->user()->hasRole('Admin'))
                        <li class="nav-item">
                            <a class="nav-link {{ request()->routeIs('admin.dashboard') ? 'active' : '' }}" href="{{ route('admin.dashboard') }}">Dashboard</a>
                        </li>
                    @endif

                    {{-- Management Dropdown --}}
                    @if (auth()->user()->hasRole('Admin'))
                        <li class="nav-item dropdown">
                            <a class="nav-link dropdown-toggle {{ request()->routeIs(
                                'admin.roles.*',
                                'admin.users.*',
                                'admin.teams.*',
                                'admin.question-categories.*',
                                'admin.questions.*',
                                'admin.tags.*',
                                'admin.invites.*',
                                'admin.notify-me',
                                'admin.plans.*',
                                'admin.subscriptions.*'
                            ) ? 'active' : '' }}"
                               href="#" id="managementDropdown" role="button" data-bs-toggle="dropdown" aria-expanded="false">
                                Management
                            </a>
                            <ul class="dropdown-menu" aria-labelledby="managementDropdown">
                                <li><a class="dropdown-item {{ request()->routeIs('admin.roles.index') ? 'active' : '' }}" href="{{ route('admin.roles.index') }}">Roles</a></li>
                                <li><a class="dropdown-item {{ request()->routeIs('admin.users.index') ? 'active' : '' }}" href="{{ route('admin.users.index') }}">Users</a></li>
                                <li><a class="dropdown-item {{ request()->routeIs('admin.teams.index') ? 'active' : '' }}" href="{{ route('admin.teams.index') }}">Teams</a></li>
                                <li><a class="dropdown-item {{ request()->routeIs('admin.question-categories.index') ? 'active' : '' }}" href="{{ route('admin.question-categories.index') }}">Question Categories</a></li>
                                <li><a class="dropdown-item {{ request()->routeIs('admin.questions.index') ? 'active' : '' }}" href="{{ route('admin.questions.index') }}">Questions</a></li>
                                <li><a class="dropdown-item {{ request()->routeIs('admin.tags.index') ? 'active' : '' }}" href="{{ route('admin.tags.index') }}">Tags</a></li>
                                <li><a class="dropdown-item {{ request()->routeIs('admin.invites.index') ? 'active' : '' }}" href="{{ route('admin.invites.index') }}">Invites</a></li>
                                <li><a class="dropdown-item {{ request()->routeIs('admin.notify-me') ? 'active' : '' }}" href="{{ route('admin.notify-me') }}">Notify-Me</a></li>
                                <li><a class="dropdown-item {{ request()->routeIs('admin.plans.index') ? 'active' : '' }}" href="{{ route('admin.plans.index') }}">Plans</a></li>
                                <li><a class="dropdown-item {{ request()->routeIs('admin.subscriptions.index') ? 'active' : '' }}" href="{{ route('admin.subscriptions.index') }}">Subscriptions</a></li>
                            </ul>
                        </li>
                    @endif

                    {{-- Settings Link (Admins only) --}}
                    @if (auth()->user()->hasRole('Admin'))
                        <li class="nav-item">
                            <a class="nav-link {{ request()->routeIs('admin.settings') ? 'active' : '' }}" href="{{ route('admin.settings') }}">Settings</a>
                        </li>
                    @endif

                    {{-- Profile Link (All Authenticated Users) --}}
                    <li class="nav-item">
                        <a class="nav-link {{ request()->routeIs('user.profile', 'admin.profile') ? 'active' : '' }}"
                           href="{{ auth()->user()->hasRole('Admin') ? route('admin.profile') : route('user.profile') }}">
                            Profile
                        </a>
                    </li>

                    {{-- Logout Link (Common for All Users) --}}
                    <li class="nav-item">
                        <a class="nav-link" href="{{ route('logout') }}"
                           onclick="event.preventDefault(); document.getElementById('logout-form').submit();">
                            Logout
                        </a>
                        <form id="logout-form" action="{{ route('logout') }}" method="POST" class="d-none">
                            @csrf
                        </form>
                    </li>
                @endauth
            </ul>
        </div>
    </div>
</nav>
