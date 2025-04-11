<aside class="main-sidebar sidebar-dark-primary elevation-4">
    <!-- Brand Logo -->
    <a href="{{ route('user.dashboard') }}" class="brand-link">
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
                    <a href="{{ route('user.dashboard') }}" class="nav-link">
                        <i class="nav-icon fas fa-tachometer-alt"></i>
                        <p>Dashboard</p>
                    </a>
                </li>
                <li class="nav-item">
                    <a href="{{ route('user.profile') }}" class="nav-link">
                        <i class="nav-icon fas fa-user"></i>
                        <p>Profile</p>
                    </a>
                </li>
                <!-- Add more links as needed -->
            </ul>
        </nav>
    </div>
</aside>
