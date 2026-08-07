<span id="admin-invites-total-value" class="d-none">{{ $invites->total() }}</span>

@if($invites->isEmpty())
    <p>No invites found.</p>
@else
    <div class="table-responsive">
    <table class="table table-bordered table-striped">
        <thead>
        <tr>
            <th>#</th>
            <x-sortable-th field="code" label="Code" />
            <th>Invited User</th>
            <th>Created By</th>
            <x-sortable-th field="max_uses" label="Max Uses" />
            <x-sortable-th field="times_used" label="Times Used" />
            <x-sortable-th field="is_active" label="Active" />
            <x-sortable-th field="expires_at" label="Expires At" />
            <th>Actions</th>
        </tr>
        </thead>
        <tbody>
        @foreach($invites as $invite)
            <tr>
                <td>{{ $loop->iteration }}</td>
                <td>{{ $invite->code }}</td>
                <td>
                    @if($invite->invitedUser)
                        {{-- Registered User --}}
                        <strong>{{ $invite->invitedUser->name }}</strong>
                        ({{ $invite->invitedUser->email }})
                    @else
                        {{-- Non-registered User --}}
                        <span class="text-danger">
                                Non-registered User
                            </span>
                        ({{ $invite->email }})
                    @endif
                </td>
                <td>{{ $invite->creator->name ?? 'N/A' }}</td>
                <td>{{ $invite->max_uses }}</td>
                <td>{{ $invite->times_used }}</td>
                <td>
                        <span class="badge bg-{{ $invite->is_active ? 'success' : 'danger' }}">
                            {{ $invite->is_active ? 'Active' : 'Inactive' }}
                        </span>
                </td>
                <td>{{ $invite->expires_at ? $invite->expires_at->format('Y-m-d H:i:s') : 'Does not expire' }}</td>
                <td>
                    @if($invite->is_active)
                        <form method="POST" action="{{ route('admin.invites.disable', $invite->id) }}" class="d-inline">
                            @csrf
                            @method('PUT')
                            <button type="submit" class="btn btn-sm btn-danger">Disable</button>
                        </form>
                    @else
                        <form method="POST" action="{{ route('admin.invites.enable', $invite->id) }}" class="d-inline">
                            @csrf
                            @method('PUT')
                            <button type="submit" class="btn btn-sm btn-success">Enable</button>
                        </form>
                    @endif
                </td>
            </tr>
        @endforeach
        </tbody>
    </table>
    </div>
    <div class="mt-3">
        {{ $invites->links() }}
    </div>
@endif
