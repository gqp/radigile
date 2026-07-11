<aside class="main-sidebar sidebar-dark-primary elevation-4">
    <a href="{{ route('dashboard') }}" class="brand-link">
        <span class="brand-text font-weight-light">
            <img src="{{ asset('imgs/logo-purple.png') }}" alt="Logo" class="img-fluid" style="max-height: 80px;">
        </span>
    </a>

    <div class="sidebar">
        <nav class="mt-2">
            <ul class="nav nav-pills nav-sidebar flex-column" data-widget="treeview" role="menu" data-accordion="false">

                {{-- Dashboard --}}
                <li class="nav-item">
                    <a href="{{ route('dashboard') }}" class="nav-link {{ request()->routeIs('dashboard') ? 'active' : '' }}">
                        <i class="nav-icon fas fa-tachometer-alt"></i>
                        <p>Dashboard</p>
                    </a>
                </li>

                {{-- My Teams --}}
                <li class="nav-item">
                    <a href="{{ route('user.teams.index') }}" class="nav-link {{ request()->routeIs('user.teams.*') ? 'active' : '' }}">
                        <i class="nav-icon fas fa-user-friends"></i>
                        <p>My Teams</p>
                    </a>
                </li>

                {{-- Assessments --}}
                <li class="nav-item">
                    <a href="{{ route('user.assessments.index') }}" class="nav-link {{ request()->routeIs('user.assessments.*') ? 'active' : '' }}">
                        <i class="nav-icon fas fa-clipboard-list"></i>
                        <p>Assessments</p>
                    </a>
                </li>

                {{-- Profile --}}
                <li class="nav-item">
                    @if(Auth::user()->hasPermissionTo('access-admin-panel'))
                        <a href="{{ route('admin.profile') }}" class="nav-link {{ request()->routeIs('admin.profile') ? 'active' : '' }}">
                    @else
                        <a href="{{ route('user.profile') }}" class="nav-link {{ request()->routeIs('user.profile') ? 'active' : '' }}">
                    @endif
                        <i class="nav-icon fas fa-user"></i>
                        <p>Profile</p>
                    </a>
                </li>

                {{-- Billing --}}
                <li class="nav-item">
                    <a href="{{ route('billing.index') }}" class="nav-link {{ request()->routeIs('billing.*') ? 'active' : '' }}">
                        <i class="nav-icon fas fa-credit-card"></i>
                        <p>Billing</p>
                    </a>
                </li>

                @if(Auth::user()->hasPermissionTo('access-admin-panel'))
                {{-- ── Admin Section ── --}}
                <li class="nav-header" style="color: #c2c7d0; font-size: 0.7rem; letter-spacing: 0.05rem; padding: 10px 15px 4px;">ADMINISTRATION</li>

                <li class="nav-item">
                    <a href="{{ route('admin.users.index') }}" class="nav-link {{ request()->routeIs('admin.users.*') ? 'active' : '' }}">
                        <i class="nav-icon fas fa-users"></i>
                        <p>Users</p>
                    </a>
                </li>

                <li class="nav-item">
                    <a href="{{ route('admin.plans.index') }}" class="nav-link {{ request()->routeIs('admin.plans.*') ? 'active' : '' }}">
                        <i class="nav-icon fas fa-file-lines"></i>
                        <p>Plans</p>
                    </a>
                </li>

                <li class="nav-item">
                    <a href="{{ route('admin.subscriptions.index') }}" class="nav-link {{ request()->routeIs('admin.subscriptions.*') ? 'active' : '' }}">
                        <i class="nav-icon fas fa-handshake"></i>
                        <p>Subscriptions</p>
                    </a>
                </li>

                <li class="nav-item">
                    <a href="{{ route('admin.roles.index') }}" class="nav-link {{ request()->routeIs('admin.roles.*') ? 'active' : '' }}">
                        <i class="nav-icon fas fa-shield-alt"></i>
                        <p>Roles</p>
                    </a>
                </li>

                {{-- Manage Teams --}}
                <li class="nav-item {{ request()->routeIs('admin.teams.*') || request()->routeIs('team-frameworks.*') || request()->routeIs('team-domains.*') || request()->routeIs('admin.team-member-roles.*') ? 'menu-open' : '' }}">
                    <a href="#" class="nav-link {{ request()->routeIs('admin.teams.*') || request()->routeIs('team-frameworks.*') || request()->routeIs('team-domains.*') || request()->routeIs('admin.team-member-roles.*') ? 'active' : '' }}">
                        <i class="nav-icon fas fa-layer-group"></i>
                        <p>Manage Teams <i class="right fas fa-angle-left"></i></p>
                    </a>
                    <ul class="nav nav-treeview" style="padding-left: 15px;">
                        <li class="nav-item">
                            <a href="{{ route('admin.team-frameworks.index') }}" class="nav-link {{ request()->routeIs('team-frameworks.*') ? 'active' : '' }}">
                                <i class="fas fa-cogs nav-icon"></i>
                                <p>Frameworks</p>
                            </a>
                        </li>
                        <li class="nav-item">
                            <a href="{{ route('admin.team-domains.index') }}" class="nav-link {{ request()->routeIs('team-domains.*') ? 'active' : '' }}">
                                <i class="fas fa-network-wired nav-icon"></i>
                                <p>Domains</p>
                            </a>
                        </li>
                        <li class="nav-item">
                            <a href="{{ route('admin.teams.index') }}" class="nav-link {{ request()->routeIs('admin.teams.index') || request()->routeIs('admin.teams.show') || request()->routeIs('admin.teams.create') || request()->routeIs('admin.teams.edit') ? 'active' : '' }}">
                                <i class="fas fa-users nav-icon"></i>
                                <p>All Teams</p>
                            </a>
                        </li>
                        <li class="nav-item">
                            <a href="{{ route('admin.team-member-roles.index') }}" class="nav-link {{ request()->routeIs('admin.team-member-roles.*') ? 'active' : '' }}">
                                <i class="fas fa-user-tag nav-icon"></i>
                                <p>Member Roles</p>
                            </a>
                        </li>
                    </ul>
                </li>

                {{-- Manage Questions --}}
                <li class="nav-item {{ request()->routeIs('admin.questions.*') || request()->routeIs('admin.question-categories.*') ? 'menu-open' : '' }}">
                    <a href="#" class="nav-link {{ request()->routeIs('admin.questions.*') || request()->routeIs('admin.question-categories.*') ? 'active' : '' }}">
                        <i class="nav-icon fas fa-question-circle"></i>
                        <p>Questions <i class="right fas fa-angle-left"></i></p>
                    </a>
                    <ul class="nav nav-treeview" style="padding-left: 15px;">
                        <li class="nav-item">
                            <a href="{{ route('admin.question-categories.index') }}" class="nav-link {{ request()->routeIs('admin.question-categories.*') ? 'active' : '' }}">
                                <i class="fas fa-list-alt nav-icon"></i>
                                <p>Categories</p>
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

                {{-- Manage Tags --}}
                <li class="nav-item {{ request()->routeIs('admin.tags.*') ? 'menu-open' : '' }}">
                    <a href="#" class="nav-link {{ request()->routeIs('admin.tags.*') ? 'active' : '' }}">
                        <i class="nav-icon fas fa-tags"></i>
                        <p>Tags <i class="right fas fa-angle-left"></i></p>
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
                                <p>Add Tag</p>
                            </a>
                        </li>
                    </ul>
                </li>

                <li class="nav-item">
                    <a href="{{ route('admin.notify-me') }}" class="nav-link {{ request()->routeIs('admin.notify-me') ? 'active' : '' }}">
                        <i class="nav-icon fas fa-message"></i>
                        <p>Notify-me</p>
                    </a>
                </li>

                <li class="nav-item">
                    <a href="{{ route('admin.invites.index') }}" class="nav-link {{ request()->routeIs('admin.invites.*') ? 'active' : '' }}">
                        <i class="nav-icon fas fa-envelope-open-text"></i>
                        <p>Invites</p>
                    </a>
                </li>

                <li class="nav-item">
                    <a href="{{ route('admin.settings') }}" class="nav-link {{ request()->routeIs('admin.settings') ? 'active' : '' }}">
                        <i class="nav-icon fas fa-cog"></i>
                        <p>Settings</p>
                    </a>
                </li>
                @endif

                {{-- Logout --}}
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
