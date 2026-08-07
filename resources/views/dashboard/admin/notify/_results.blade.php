<span id="admin-notify-total-value" class="d-none">{{ $submissions->total() }}</span>

@if($submissions->isNotEmpty())
    <div class="table-responsive">
        <table class="table table-hover table-bordered align-middle">
            <thead class="table-light">
            <tr>
                <th>#</th>
                <x-sortable-th field="name" label="Name" />
                <x-sortable-th field="email" label="Email" />
                <x-sortable-th field="company" label="Company" />
                <x-sortable-th field="created_at" label="Submitted At" />
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
                        <button class="btn btn-primary btn-sm" data-bs-toggle="modal"
                                data-bs-target="#inviteModal-{{ $submission->id }}">
                            <i class="bi bi-envelope-paper"></i> Send Invite
                        </button>
                    </td>
                </tr>

                {{-- Invite Modal --}}
                <div class="modal fade" id="inviteModal-{{ $submission->id }}" tabindex="-1"
                     aria-labelledby="inviteModalLabel-{{ $submission->id }}"
                     aria-hidden="true">
                    <div class="modal-dialog modal-dialog-centered">
                        <div class="modal-content">
                            <form action="{{ route('admin.notify.send-invite', $submission->id) }}"
                                  method="POST">
                                @csrf
                                <div class="modal-header">
                                    <h5 class="modal-title"
                                        id="inviteModalLabel-{{ $submission->id }}">Send
                                        Invite</h5>
                                    <button type="button" class="btn-close"
                                            data-bs-dismiss="modal"
                                            aria-label="Close"></button>
                                </div>
                                <div class="modal-body">
                                    <div class="form-group mb-3">
                                        <label for="max_uses-{{ $submission->id }}"
                                               class="form-label">Maximum Uses:</label>
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
                                        <label for="expires_at-{{ $submission->id }}"
                                               class="form-label">Expiration Date:</label>
                                        <input
                                                type="date"
                                                name="expires_at"
                                                id="expires_at-{{ $submission->id }}"
                                                class="form-control"
                                        >
                                    </div>
                                </div>
                                <div class="modal-footer">
                                    <button type="button" class="btn btn-secondary"
                                            data-bs-dismiss="modal">
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
    <div class="mt-3">
        {{ $submissions->links() }}
    </div>
@else
    <div class="alert alert-warning text-center">
        <i class="bi bi-exclamation-circle"></i> No submissions found.
    </div>
@endif
