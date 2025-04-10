@extends('layouts.app')

@section('content')
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
                        <h5 class="text-secondary">
                            <i class="bi bi-list-ul"></i> Submissions
                        </h5>
                        <hr>

                        {{-- Submissions Table --}}
                        @if($submissions->isNotEmpty())
                            <div class="table-responsive">
                                <table class="table table-hover table-bordered align-middle">
                                    <thead class="table-light">
                                    <tr>
                                        <th>#</th>
                                        <th>Name</th>
                                        <th>Email</th>
                                        <th>Company</th>
                                        <th>Submitted At</th>
                                        <th class="text-center">Actions</th>
                                    </tr>
                                    </thead>
                                    <tbody>
                                    @foreach ($submissions as $submission)
                                        <tr>
                                            <td>{{ $submission->id }}</td>
                                            <td>{{ $submission->name }}</td>
                                            <td>{{ $submission->email }}</td>
                                            <td>{{ $submission->company ?? 'N/A' }}</td>
                                            <td>{{ $submission->created_at->format('M d, Y h:i A') }}</td>
                                            <td class="text-center">
                                                {{-- Send Invite Button --}}
                                                <button class="btn btn-primary btn-sm" data-bs-toggle="modal" data-bs-target="#inviteModal-{{ $submission->id }}">
                                                    <i class="bi bi-envelope-paper"></i> Send Invite
                                                </button>
                                            </td>
                                        </tr>

                                        {{-- Invite Modal --}}
                                        <div class="modal fade" id="inviteModal-{{ $submission->id }}" tabindex="-1" aria-labelledby="inviteModalLabel-{{ $submission->id }}" aria-hidden="true">
                                            <div class="modal-dialog modal-dialog-centered">
                                                <div class="modal-content">
                                                    <form action="{{ route('admin.notify.send-invite', $submission->id) }}" method="POST">
                                                        @csrf
                                                        <div class="modal-header">
                                                            <h5 class="modal-title" id="inviteModalLabel-{{ $submission->id }}">Send Invite</h5>
                                                            <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                                                        </div>
                                                        <div class="modal-body">
                                                            <div class="form-group mb-3">
                                                                <label for="max_uses-{{ $submission->id }}" class="form-label">Maximum Uses:</label>
                                                                <input
                                                                    type="number"
                                                                    name="max_uses"
                                                                    id="max_uses-{{ $submission->id }}"
                                                                    class="form-control"
                                                                    min="1"
                                                                    required
                                                                >
                                                            </div>
                                                            <div class="form-group mb-3">
                                                                <label for="expires_at-{{ $submission->id }}" class="form-label">Expiration Date:</label>
                                                                <input
                                                                    type="date"
                                                                    name="expires_at"
                                                                    id="expires_at-{{ $submission->id }}"
                                                                    class="form-control"
                                                                >
                                                            </div>
                                                        </div>
                                                        <div class="modal-footer">
                                                            <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">
                                                                Cancel
                                                            </button>
                                                            <button type="submit" class="btn btn-primary">
                                                                <i class="bi bi-send"></i> Send
                                                            </button>
                                                        </div>
                                                    </form>
                                                </div>
                                            </div>
                                        </div>
                                    @endforeach
                                    </tbody>
                                </table>
                            </div>
                        @else
                            <div class="alert alert-warning text-center">
                                <i class="bi bi-exclamation-circle"></i> No submissions found.
                            </div>
                        @endif
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
