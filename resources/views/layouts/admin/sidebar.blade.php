<aside class="main-sidebar sidebar-dark-primary elevation-4">
    <!-- Brand Logo -->
    <a href="{{ route('admin.dashboard') }}" class="brand-link">
        <span class="brand-text font-weight-light">
            <img src="{{ asset('imgs/logo-purple.png') }}" alt="Logo" class="img-fluid" style="max-height: 80px;">
        </span>
    </a>

    <!-- Sidebar -->
    <div class="sidebar">
        <nav class="mt-2">
            <ul class="nav nav-pills nav-sidebar flex-column" data-widget="treeview" role="menu" data-accordion="false">

                <!-- Dashboard -->
                <li class="nav-item">
                    <a href="{{ route('admin.dashboard') }}" class="nav-link {{ request()->routeIs('admin.dashboard') ? 'active' : '' }}">
                        <i class="nav-icon fas fa-tachometer-alt"></i>
                        <p>Dashboard</p>
                    </a>
                </li>

                <!-- Plans -->
                <li class="nav-item">
                    <a href="{{ route('admin.plans.index') }}" class="nav-link {{ request()->routeIs('admin.plans.index') ? 'active' : '' }}">
                        <i class="nav-icon fas fa-file-lines"></i>
                        <p>Plans</p>
                    </a>
                </li>

                <!-- Subscriptions -->
                <li class="nav-item">
                    <a href="{{ route('admin.subscriptions.index') }}" class="nav-link {{ request()->routeIs('admin.subscriptions.index') ? 'active' : '' }}">
                        <i class="nav-icon fas fa-handshake"></i>
                        <p>Subscriptions</p>
                    </a>
                </li>

                <!-- Roles -->
                <li class="nav-item">
                    <a href="{{ route('admin.roles.index') }}" class="nav-link {{ request()->routeIs('admin.roles.index') ? 'active' : '' }}">
                        <i class="nav-icon fas fa-shield-alt"></i>
                        <p>Roles</p>
                    </a>
                </li>

                <!-- Users -->
                <li class="nav-item">
                    <a href="{{ route('admin.users.index') }}" class="nav-link {{ request()->routeIs('admin.users.index') ? 'active' : '' }}">
                        <i class="nav-icon fas fa-users"></i>
                        <p>Users</p>
                    </a>
                </li>

                <!-- Teams -->
                <li class="nav-item">
                    <a href="#" class="nav-link">
                        <i class="nav-icon fas fa-user-friends"></i>
                        <p>
                            Manage Teams
                            <i class="right fas fa-angle-left"></i>
                        </p>
                    </a>
                    <ul class="nav nav-treeview" style="padding-left: 15px;">
                        <li class="nav-item">
                            <a href="{{ route('admin.team-frameworks.index') }}" class="nav-link {{ request()->routeIs('team-frameworks.*') ? 'active' : '' }}">
                                <i class="fas fa-cogs nav-icon"></i>
                                <p>Team Frameworks</p>
                            </a>
                        </li>
                        <li class="nav-item">
                            <a href="{{ route('admin.team-domains.index') }}" class="nav-link {{ request()->routeIs('team-domains.*') ? 'active' : '' }}">
                                <i class="fas fa-network-wired nav-icon"></i>
                                <p>Team Domains</p>
                            </a>
                        </li>
                        <li class="nav-item">
                            <a href="{{ route('admin.teams.index') }}" class="nav-link {{ request()->routeIs('admin.teams.*') ? 'active' : '' }}">
                                <i class="fas fa-users nav-icon"></i>
                                <p>Teams</p>
                            </a>
                        </li>
                    </ul>
                </li>

                <!-- Questions -->
                <li class="nav-item">
                    <a href="#" class="nav-link">
                        <i class="nav-icon fas fa-question-circle"></i>
                        <p>
                            Manage Questions
                            <i class="right fas fa-angle-left"></i>
                        </p>
                    </a>
                    <ul class="nav nav-treeview" style="padding-left: 15px;">
                        <li class="nav-item">
                            <a href="{{ route('admin.question-categories.index') }}" class="nav-link {{ request()->routeIs('admin.question-categories.*') ? 'active' : '' }}">
                                <i class="fas fa-list-alt nav-icon"></i>
                                <p>Question Categories</p>
                            </a>
                        </li>
                        <li class="nav-item">
                            <a href="{{ route('admin.questions.index') }}" class="nav-link {{ request()->routeIs('admin.questions.*') ? 'active' : '' }}">
                                <i class="fas fa-question nav-icon"></i>
                                <p>Questions</p>
                            </a>
                        </li>
                    </ul>
                </li>

                <!-- Tags -->
                <li class="nav-item">
                    <a href="#" class="nav-link">
                        <i class="nav-icon fas fa-tags"></i>
                        <p>
                            Manage Tags
                            <i class="right fas fa-angle-left"></i>
                        </p>
                    </a>
                    <ul class="nav nav-treeview" style="padding-left: 15px;">
                        <li class="nav-item">
                            <a href="{{ route('admin.tags.index') }}" class="nav-link {{ request()->routeIs('admin.tags.index') ? 'active' : '' }}">
                                <i class="fas fa-list nav-icon"></i>
                                <p>All Tags</p>
                            </a>
                        </li>
                        <li class="nav-item">
                            <a href="{{ route('admin.tags.create') }}" class="nav-link {{ request()->routeIs('admin.tags.create') ? 'active' : '' }}">
                                <i class="fas fa-plus nav-icon"></i>
                                <p>Add New Tag</p>
                            </a>
                        </li>
                    </ul>
                </li>

                <!-- Notify-me -->
                <li class="nav-item">
                    <a href="{{ route('admin.notify-me') }}" class="nav-link {{ request()->routeIs('admin.notify-me') ? 'active' : '' }}">
                        <i class="nav-icon fas fa-message"></i>
                        <p>Notify-me</p>
                    </a>
                </li>

                <!-- Invites -->
                <li class="nav-item">
                    <a href="{{ route('admin.invites.index') }}" class="nav-link {{ request()->routeIs('admin.invites.index') ? 'active' : '' }}">
                        <i class="nav-icon fas fa-envelope-open-text"></i>
                        <p>Invites</p>
                    </a>
                </li>

                <!-- Profile -->
                <li class="nav-item">
                    <a href="{{ route('admin.profile') }}" class="nav-link {{ request()->routeIs('admin.profile') ? 'active' : '' }}">
                        <i class="nav-icon fas fa-user"></i>
                        <p>Profile</p>
                    </a>
                </li>

                <!-- Settings -->
                <li class="nav-item">
                    <a href="{{ route('admin.settings') }}" class="nav-link {{ request()->routeIs('admin.settings') ? 'active' : '' }}">
                        <i class="nav-icon fas fa-cog"></i>
                        <p>Settings</p>
                    </a>
                </li>

                <!-- Logout -->
                <li class="nav-item">
                    <form action="{{ route('logout') }}" method="POST">
                        @csrf
                        <button type="submit" class="btn btn-link nav-link text-left" style="color: #c2c7d0;">
                            <i class="nav-icon fas fa-sign-out-alt"></i>
                            <p>Logout</p>
                        </button>
                    </form>
                </li>
            </ul>
        </nav>
    </div>
</aside>
