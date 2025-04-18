<aside class="main-sidebar sidebar-dark-primary elevation-4">
    <!-- Brand Logo -->
    <a href="{{ route('admin.dashboard') }}" class="brand-link">
        <span class="brand-text font-weight-light">
        <img src="{{ asset('imgs/logo-purple.png') }}" alt="Logo" class="img-fluid" style="max-height: 80px;">
        </span>
    </a>

    <!-- Sidebar -->
    <div class="sidebar">
        <!-- Sidebar Menu -->
        <nav class="mt-2">
            <ul class="nav nav-pills nav-sidebar flex-column" data-widget="treeview" role="menu" data-accordion="false">
                <li class="nav-item">
                    <a href="{{ route('admin.dashboard') }}" class="nav-link">
                        <i class="nav-icon fas fa-tachometer-alt"></i>
                        <p>Dashboard</p>
                    </a>
                </li>
                <li class="nav-item">
                    <a href="{{ route('admin.plans.index') }}" class="nav-link">
                        <i class="nav-icon fas fa-user"></i>
                        <p>Plans</p>
                    </a>
                </li>
                <li class="nav-item">
                    <a href="{{ route('admin.subscriptions.index') }}" class="nav-link">
                        <i class="nav-icon fas fa-user"></i>
                        <p>Subscriptions</p>
                    </a>
                </li>
                <li class="nav-item">
                    <a href="{{ route('admin.roles.index') }}" class="nav-link">
                        <i class="nav-icon fas fa-shield-alt"></i>
                        <p>Roles</p>
                    </a>
                </li>
                <li class="nav-item">
                    <a href="{{ route('admin.users.index') }}" class="nav-link">
                        <i class="nav-icon fas fa-users"></i>
                        <p>Users</p>
                    </a>
                </li>
                <li class="nav-item">
                    <a href="{{ route('admin.notify-me') }}" class="nav-link">
                        <i class="nav-icon fas fa-users"></i>
                        <p>Notify-me</p>
                    </a>
                </li>
                <li class="nav-item">
                    <a href="{{ route('admin.invites.index') }}" class="nav-link">
                        <i class="nav-icon fas fa-users"></i>
                        <p>Invites</p>
                    </a>
                </li>
                <li class="nav-item">
                    <a href="{{ route('admin.profile') }}" class="nav-link">
                        <i class="nav-icon fas fa-user"></i>
                        <p>Profile</p>
                    </a>
                </li>
                <li class="nav-item">
                    <a href="{{ route('admin.settings') }}" class="nav-link">
                        <i class="nav-icon fas fa-cog"></i>
                        <p>Settings</p>
                    </a>
                </li>
                <!-- Logout Button -->
                <li class="nav-item">
                    <form action="{{ route('logout') }}" method="POST">
                        @csrf
                        <button type="submit" class="btn btn-link nav-link text-left" style="color: #c2c7d0;">
                            <i class="nav-icon fas fa-sign-out-alt"></i>
                            <p>Logout</p>
                        </button>
                    </form>
                </li>

                <!-- Add more links as needed -->
            </ul>
        </nav>
    </div>
</aside>
