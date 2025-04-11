<div class="container-fluid bg-light py-2">
    <div class="d-flex justify-content-between align-items-center">
        <!-- User Profile and Name -->
        <div class="d-flex align-items-center">
            <img src="{{ asset('profile/Default-Profile.png') }}"
                 alt="{{ Auth::user()->name }}"
                 class="rounded-circle"
                 width="40"
                 height="40">
            <strong class="ms-2">{{ Auth::user()->name }}</strong>
        </div>

        <!-- Actions: Notifications and Logout -->
        <div class="d-flex align-items-center">
            <!-- Notifications Icon -->
            <a href="#" class="me-3 text-dark position-relative">
                <i class="fas fa-bell fa-lg"></i>
               {{-- @if ($notificationCount > 0) --}}
                    <span class="position-absolute top-0 start-100 translate-middle badge rounded-pill bg-danger">
                       {{-- {{ $notificationCount }} --}}
                    </span>
               {{-- @endif --}}
            </a>

            <!-- Logout Button -->
            <a href="{{ route('logout') }}"
               class="btn btn-sm btn-outline-danger"
               onclick="event.preventDefault(); document.getElementById('logout-form').submit();">
                Logout
            </a>

            <form id="logout-form" action="{{ route('logout') }}" method="POST" class="d-none">
                @csrf
            </form>
        </div>
    </div>
</div>
