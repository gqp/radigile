@extends('layouts.admin')

@section('content')
    <!-- Include Navbar -->
    @include('layouts.admin.navbar')
    <div class="app-container">
        <div class="container mt-5">
            <div class="row justify-content-center">
                <div class="col-md-10">
                    {{-- Notify Me Submissions Card --}}
                    <div class="card shadow-sm">
                        {{-- Card Header --}}
                        <div class="card-header bg-primary text-white d-flex justify-content-between align-items-center">
                            <h4 class="mb-0">
                                <i class="bi bi-table"></i> Notify Me Submissions
                            </h4>
                            <button class="btn btn-outline-light btn-sm">
                                <i class="bi bi-arrow-clockwise"></i> Refresh
                            </button>
                        </div>

                        {{-- Card Body --}}
                        <div class="card-body">
                            @if (session('success'))
                                <div class="alert alert-success">
                                    {{ session('success') }}
                                </div>
                            @endif

                            {{-- Table Heading --}}
                            <div class="d-flex justify-content-between align-items-center flex-wrap gap-2">
                                <h5 class="text-secondary mb-0">
                                    <i class="bi bi-list-ul"></i> <span id="admin-notify-count">{{ $submissions->total() }}</span> Submissions
                                </h5>
                                <x-search-box id="admin-notify-search" placeholder="Search name, email, or company..." live />
                            </div>
                            <hr>

                            {{-- Submissions Table --}}
                            <div id="admin-notify-results" data-live-url="{{ route('admin.notify-me') }}">
                                @include('dashboard.admin.notify._results', ['submissions' => $submissions])
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
@endsection

@push('styles')
    <!-- Custom Table Styles -->
    <style>
        .table-hover tbody tr:hover {
            background-color: #f8f9fa;
        }

        .btn-primary {
            background-color: #663399;
            border-color: #663399;
        }

        .btn-primary:hover {
            background-color: #552b81;
            border-color: #552b81;
        }

        .btn-outline-light:hover {
            color: #fff;
            background-color: #552b81;
            border-color: #552b81;
        }
    </style>
@endpush

@push('scripts')
<script>
    document.addEventListener('DOMContentLoaded', function () {
        initLiveSearch('admin-notify-search', 'admin-notify-results', {
            onSwap: function () {
                const totalEl = document.getElementById('admin-notify-total-value');
                if (totalEl) {
                    document.getElementById('admin-notify-count').textContent = totalEl.textContent;
                }
            },
        });
    });
</script>
@endpush
