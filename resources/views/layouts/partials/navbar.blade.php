<div class="container-fluid bg-light py-2">
    <div class="d-flex justify-content-between align-items-center">
        <div class="d-flex align-items-center">
            <img src="{{ asset('profile/Default-Profile.png') }}"
                 alt="{{ Auth::user()->name }}"
                 class="rounded-circle"
                 width="40"
                 height="40">
            <strong class="ms-2">{{ Auth::user()->name }}</strong>
            @if(Auth::user()->hasPermissionTo('access-admin-panel'))
                <span class="badge bg-danger ms-2">Admin</span>
            @endif
        </div>

        <div class="d-flex align-items-center">
            <a href="#" class="me-3 text-dark position-relative">
                <i class="fas fa-bell fa-lg"></i>
            </a>
            <form action="{{ route('logout') }}" method="POST" class="d-inline">
                @csrf
                <button type="submit" class="btn btn-sm btn-outline-danger">Logout</button>
            </form>
        </div>
    </div>
</div>
