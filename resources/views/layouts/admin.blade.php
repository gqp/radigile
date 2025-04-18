<!doctype html>
<html lang="{{ str_replace('_', '-', app()->getLocale()) }}">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">

    <!-- CSRF Token -->
    <meta name="csrf-token" content="{{ csrf_token() }}">

    <title>{{ config('app.name', 'Radigile.com - A New Era of Team Growth') }}</title>

    <!-- Fonts -->
    <link rel="dns-prefetch" href="//fonts.gstatic.com">
    <link href="https://fonts.googleapis.com/css2?family=Nunito:wght@300;400;600;700&display=swap" rel="stylesheet">

    <!-- AdminLTE CSS -->
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/admin-lte/3.2.0/css/adminlte.min.css">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.0.0-beta3/css/all.min.css">

    {{-- Custom CSS --}}
    @vite(['resources/sass/app.scss'])
</head>

<body class="hold-transition sidebar-mini">
<!-- Include Sidebar -->
@include('layouts.admin.sidebar')

<!-- Content Wrapper -->
<div class="content-wrapper">
    <!-- Main Content -->
    <div class="content">
        <div class="container app-container"> <!-- Add standardized class -->
            @yield('content')
        </div>
    </div>
</div>

<!-- Include Footer -->
@include('layouts.admin.footer')

<!-- Scripts -->
<script src="https://cdnjs.cloudflare.com/ajax/libs/jquery/3.6.4/jquery.min.js"></script>
<script src="https://cdn.jsdelivr.net/npm/bootstrap@5.2.3/dist/js/bootstrap.bundle.min.js"></script>
<script src="https://cdnjs.cloudflare.com/ajax/libs/admin-lte/3.2.0/js/adminlte.min.js"></script>
<script src="https://cdn.jsdelivr.net/npm/chart.js"></script>
@vite(['resources/js/app.js'])
<!-- Yield Additional Scripts -->
@yield('scripts')
@stack('scripts')
</body>
</html>
