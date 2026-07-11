@extends('layouts.app')

@section('content')

    @if ($errors->any())
        <div class="alert alert-danger">
            <div class="alert alert-danger">
                <ul>
                    @foreach ($errors->all() as $error)
                        <li>{{ $error }}</li>
                    @endforeach
                </ul>
            </div>
        </div>
    @endif

    <h1>Create a New Team</h1>

    <form method="POST" action="{{ route('admin.teams.store') }}">
        @csrf

        <div class="mb-3">
            <label for="name" class="form-label">Team Name</label>
            <input type="text" class="form-control" id="name" name="name" required>
        </div>

        <div class="mb-3">
            <label for="description" class="form-label">Description</label>
            <textarea class="form-control" id="description" name="description"></textarea>
        </div>

        <div class="mb-3">
            <label for="type" class="form-label">Team Visibility</label>
            <select class="form-control" id="type" name="type" required>
                <option value="private">Private</option>
                <option value="public">Public</option>
            </select>
        </div>

        <div class="form-group">
            <label for="team_domain_id" class="form-label">Team Domain</label>
            <select name="team_domain_id" id="team_domain_id" class="form-select" required>
                <option value="" disabled selected>Select a domain</option>
                @foreach($teamDomains as $domain)
                    <option value="{{ $domain->id }}">{{ $domain->name }}</option>
                @endforeach
            </select>
        </div>

        <div class="form-group">
            <label for="team_framework">Team Framework</label>
            <select name="team_framework" id="team_framework" class="form-control">
                @foreach ($teamFrameworks as $framework)
                    <option value="{{ $framework->id }}">{{ $framework->name }}</option>
                @endforeach
            </select>
        </div>

        <div class="mb-3">
            <label for="owner_id" class="form-label">Team Owner</label>
            <select class="form-control" id="owner_id" name="owner_id" required>
                @foreach($users as $user)
                    <option value="{{ $user->id }}">{{ $user->name }} ({{ $user->email }})</option>
                @endforeach
            </select>
        </div>

        <!-- Real-time Search for Registered Users -->
        <div class="mb-3">
            <label for="search_email" class="form-label">Search for Registered Users to Add to Team:</label>
            <input type="text" class="form-control" id="search_email" placeholder="Enter user email">
            <div id="search_results" class="mt-2"></div>
        </div>

        <!-- Invite Non-Registered Users -->
        <div class="mb-3">
            <label for="invite_emails" class="form-label">Add Non-Registered Users via Email</label>
            <textarea class="form-control" id="invite_emails" name="invite_emails" placeholder="Enter comma-separated email addresses"></textarea>
            <small class="text-muted">Enter email addresses of the users you want to add to the team. The system will automatically invite non-registered users and add registered users directly.</small>
        </div>

        <button type="submit" class="btn btn-primary">Create Team</button>
    </form>

@endsection

@push('scripts')
    <script>
        document.getElementById('search_email').addEventListener('input', function (e) {
            const query = e.target.value;

            // Clear previous results
            const resultsContainer = document.getElementById('search_results');
            resultsContainer.innerHTML = '';

            if (query.length > 2) {
                // Perform an AJAX request to search for the user
                fetch(`/admin/users/search?email=${query}`)
                    .then(response => response.json())
                    .then(data => {
                        if (data.success) {
                            // Display the matched user
                            resultsContainer.innerHTML = `
                            <div class="alert alert-success">
                                <p><strong>${data.user.name}</strong> (${data.user.email})</p>
                     b p~                <button type="button" class="btn btn-sm btn-primary" onclick="addUserToInvite('${data.user.email}')">Add</button>
                            </div>
                        `;
                        } else {
                            // Display a not found message
                            resultsContainer.innerHTML = `<div class="alert alert-warning">User not found.</div>`;
                        }
                    })
                    .catch(error => {
                        resultsContainer.innerHTML = `<div class="alert alert-danger">An error occurred. Please try again later.</div>`;
                        console.error('Error searching for user:', error);
                    });
            }
        });

        function addUserToInvite(email) {
            const inviteField = document.getElementById('invite_emails');
            if (inviteField.value) {
                inviteField.value += `, ${email}`;
            } else {
                inviteField.value = email;
            }
        }
    </script>
@endpush
