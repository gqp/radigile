@extends('layouts.admin.app')

@section('title', 'admin Dashboard')

@section('content')
    <div class="container">
        <h1 class="my-4">Admin Dashboard</h1>

        {{-- Restrict dashboard content to admins --}}
        @role('admin')
        <div class="card">
            <div class="card-header">
                <h2>Welcome, Admin!</h2>
            </div>
            <div class="card-body">
                <p>You have full access to the system. Here are your admin privileges:</p>

                <ul class="list-group">
                    <li class="list-group-item">User Management</li>
                    <li class="list-group-item">Content Management</li>
                    <li class="list-group-item">System Reports</li>
                    <li class="list-group-item">Audit Logs</li>
                </ul>
            </div>
        </div>
        @else
            {{-- Fallback content for unauthorized access --}}
            <div class="alert alert-danger mt-3">
                <p>Unauthorized access. You do not have permissions to view this page.</p>
            </div>
            @endrole
    </div>
@endsection
