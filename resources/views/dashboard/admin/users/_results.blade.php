<span id="users-total-value" class="d-none">{{ $users->total() }}</span>

@if ($users->isEmpty())
    <p class="text-muted p-3 mb-0">No users found.</p>
@else
    {{-- Table view --}}
    <div class="table-responsive users-view-table">
        <table class="table table-hover align-middle mb-0">
            <thead class="table-light">
                <tr>
                    <x-sortable-th field="name" label="Name" />
                    <x-sortable-th field="email" label="Email" />
                    <th>Email Verified</th>
                    <th>Active</th>
                    <th>Role(s)</th>
                    <th>Subscription</th>
                    <x-sortable-th field="last_login_at" label="Last Login" />
                    <x-sortable-th field="created_at" label="Created" />
                    <th class="text-center">Actions</th>
                </tr>
            </thead>
            <tbody>
                @foreach ($users as $user)
                <tr>
                    <td>{{ $user->name }}</td>
                    <td>{{ $user->email }}</td>
                    <td>
                        @if ($user->email_verified_at)
                            <span class="badge bg-success">Verified</span>
                        @else
                            <span class="badge bg-danger">Not Verified</span>
                        @endif
                    </td>
                    <td>
                        <form method="POST" action="{{ route('admin.users.toggle-active', $user->id) }}">
                            @csrf
                            @method('PATCH')
                            <button type="submit" class="btn btn-sm {{ $user->is_active ? 'btn-success' : 'btn-secondary' }}">
                                {{ $user->is_active ? 'Active' : 'Inactive' }}
                            </button>
                        </form>
                    </td>
                    <td>{{ $user->roles->pluck('name')->join(', ') ?: 'None' }}</td>
                    <td>{{ $user->subscription->plan->name ?? 'None' }}</td>
                    <td class="text-muted small">{{ $user->last_login_at ? $user->last_login_at->format('d/m/Y H:i') : 'Never' }}</td>
                    <td class="text-muted small">{{ $user->created_at->format('d/m/Y H:i') }}</td>
                    <td class="text-center">
                        <a href="{{ route('admin.users.edit', $user->id) }}" class="btn btn-warning btn-sm">
                            <i class="fas fa-pen"></i> Edit
                        </a>
                        <form method="POST" action="{{ route('admin.users.destroy', $user->id) }}"
                              style="display: inline-block;"
                              onsubmit="return confirm('Are you sure you want to delete {{ $user->name }}?');">
                            @csrf
                            @method('DELETE')
                            <button type="submit" class="btn btn-danger btn-sm" {{ Auth::id() === $user->id ? 'disabled' : '' }}>
                                <i class="fas fa-trash"></i> Delete
                            </button>
                        </form>
                    </td>
                </tr>
                @endforeach
            </tbody>
        </table>
    </div>

    {{-- Card view --}}
    <div class="row g-4 p-3 users-view-cards d-none">
        @foreach ($users as $user)
        <div class="col-md-6 col-xl-4">
            <div class="card h-100 shadow border-0 user-card">
                <div class="card-header bg-primary text-white d-flex align-items-center gap-2 py-3">
                    <i class="fas fa-user fa-lg"></i>
                    <h5 class="mb-0 fw-bold text-truncate">{{ $user->name }}</h5>
                </div>
                <div class="card-body">
                    <p class="text-muted mb-3">{{ $user->email }}</p>
                    <div class="d-flex flex-wrap gap-2 mb-3">
                        @if ($user->email_verified_at)
                            <span class="badge bg-success px-3 py-2">Verified</span>
                        @else
                            <span class="badge bg-danger px-3 py-2">Not Verified</span>
                        @endif
                        <span class="badge bg-light text-dark border px-3 py-2">
                            <i class="fas fa-shield-alt me-1"></i> {{ $user->roles->pluck('name')->join(', ') ?: 'No role' }}
                        </span>
                        <span class="badge bg-light text-dark border px-3 py-2">
                            <i class="fas fa-credit-card me-1"></i> {{ $user->subscription->plan->name ?? 'No plan' }}
                        </span>
                    </div>
                    <p class="text-muted small mb-1"><i class="fas fa-clock me-1"></i> Last login: {{ $user->last_login_at ? $user->last_login_at->format('d M Y') : 'Never' }}</p>
                    <p class="text-muted small mb-0"><i class="fas fa-calendar me-1"></i> Created {{ $user->created_at->format('d M Y') }}</p>
                </div>
                <div class="card-footer bg-white border-top-0 d-flex gap-2 pb-3">
                    <form method="POST" action="{{ route('admin.users.toggle-active', $user->id) }}" class="flex-fill">
                        @csrf
                        @method('PATCH')
                        <button type="submit" class="btn btn-sm w-100 {{ $user->is_active ? 'btn-success' : 'btn-secondary' }}">
                            {{ $user->is_active ? 'Active' : 'Inactive' }}
                        </button>
                    </form>
                    <a href="{{ route('admin.users.edit', $user->id) }}" class="btn btn-outline-warning flex-fill">Edit</a>
                    <form method="POST" action="{{ route('admin.users.destroy', $user->id) }}"
                          class="flex-fill" onsubmit="return confirm('Are you sure you want to delete {{ $user->name }}?');">
                        @csrf
                        @method('DELETE')
                        <button type="submit" class="btn btn-outline-danger w-100" {{ Auth::id() === $user->id ? 'disabled' : '' }}>
                            Delete
                        </button>
                    </form>
                </div>
            </div>
        </div>
        @endforeach
    </div>

    <div class="p-3">
        {{ $users->links() }}
    </div>
@endif
