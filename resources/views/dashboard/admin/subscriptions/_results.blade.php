<span id="admin-subscriptions-total-value" class="d-none">{{ $subscriptions->total() }}</span>

@if ($subscriptions->isNotEmpty())
    <div class="table-responsive">
        <table class="table table-hover table-bordered align-middle">
            <thead class="table-light">
            <tr>
                <th>User</th>
                <th>Plan</th>
                <x-sortable-th field="starts_at" label="Starts At" />
                <x-sortable-th field="ends_at" label="Ends At" />
                <x-sortable-th field="is_active" label="Status" />
                <th class="text-center">Actions</th>
            </tr>
            </thead>
            <tbody>
            @foreach ($subscriptions as $subscription)
                <tr>
                    <td>{{ $subscription->user->name }}</td>
                    <td>{{ $subscription->plan->name }}</td>
                    <td>{{ $subscription->starts_at->format('Y-m-d') }}</td>
                    <td>{{ $subscription->ends_at ? $subscription->ends_at->format('Y-m-d') : 'N/A' }}</td>
                    <td>
                        <span class="badge {{ $subscription->is_active ? 'bg-success' : 'bg-danger' }}">
                            {{ $subscription->is_active ? 'Active' : 'Inactive' }}
                        </span>
                    </td>
                    <td class="text-center">
                        {{-- Delete Button --}}
                        <form action="{{ route('admin.subscriptions.destroy', $subscription->id) }}" method="POST" style="display: inline-block;"
                              onsubmit="return confirm('Are you sure you want to delete this subscription? This cannot be undone.');">
                            @csrf
                            @method('DELETE')
                            <button type="submit" class="btn btn-sm btn-danger">
                                <i class="bi bi-trash"></i> Delete
                            </button>
                        </form>

                        <a href="{{ route('admin.subscriptions.edit', $subscription->id) }}"
                           class="btn btn-sm btn-warning">
                            <i class="bi bi-pencil-square"></i> Edit
                        </a>
                    </td>
                </tr>
            @endforeach
            </tbody>
        </table>
    </div>
    <div class="mt-3">
        {{ $subscriptions->links() }}
    </div>
@else
    <div class="alert alert-warning text-center">
        <i class="bi bi-exclamation-circle"></i> No subscriptions found.
    </div>
@endif
