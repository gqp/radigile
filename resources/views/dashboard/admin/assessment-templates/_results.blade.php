<span id="admin-assessment-templates-total-value" class="d-none">{{ $templates->total() }}</span>

@if ($templates->isEmpty())
    <div class="alert alert-light border">No assessment templates found.</div>
@else
    <div class="card shadow-sm">
        <div class="card-body p-0">
            {{-- Table view --}}
            <div class="table-responsive admin-assessment-templates-view-table">
                <table class="table table-hover align-middle mb-0">
                    <thead class="table-light">
                        <tr>
                            <x-sortable-th field="title" label="Title" />
                            <th>Scope</th>
                            <th>Created By</th>
                            <th class="text-center">Questions</th>
                            <x-sortable-th field="created_at" label="Created" />
                        </tr>
                    </thead>
                    <tbody>
                        @foreach ($templates as $template)
                        <tr>
                            <td><strong>{{ $template->title }}</strong>
                                @if ($template->description)
                                    <br><small class="text-muted">{{ Str::limit($template->description, 60) }}</small>
                                @endif
                            </td>
                            <td>
                                @if ($template->is_public)
                                    <span class="badge bg-success">Public</span>
                                @else
                                    <span class="badge bg-secondary">{{ $template->team->name ?? 'Team' }}</span>
                                @endif
                            </td>
                            <td>{{ $template->creator->name }}</td>
                            <td class="text-center">{{ $template->questions_count }}</td>
                            <td class="text-muted small">{{ $template->created_at->format('d M Y') }}</td>
                        </tr>
                        @endforeach
                    </tbody>
                </table>
            </div>

            {{-- Card view --}}
            <div class="row g-4 p-3 admin-assessment-templates-view-cards d-none">
                @foreach ($templates as $template)
                @php
                    $headerClass = $template->is_public ? 'bg-success' : 'bg-secondary';
                @endphp
                <div class="col-md-6 col-xl-4">
                    <div class="card h-100 shadow border-0">
                        <div class="card-header {{ $headerClass }} text-white d-flex align-items-center gap-2 py-3">
                            <i class="bi bi-bookmark-star fa-lg"></i>
                            <h5 class="mb-0 fw-bold text-truncate">{{ $template->title }}</h5>
                        </div>
                        <div class="card-body">
                            <div class="d-flex flex-wrap gap-2 mb-3">
                                <span class="badge {{ $headerClass }} px-3 py-2">
                                    {{ $template->is_public ? 'Public' : ($template->team->name ?? 'Team') }}
                                </span>
                            </div>
                            <p class="mb-1 small text-muted"><i class="fas fa-user-edit me-1"></i> Created by {{ $template->creator->name }}</p>
                            <p class="mb-1 small text-muted"><i class="fas fa-question-circle me-1"></i> {{ $template->questions_count }} questions</p>
                            <p class="text-muted small mb-0"><i class="fas fa-calendar me-1"></i> Created {{ $template->created_at->format('d M Y') }}</p>
                        </div>
                    </div>
                </div>
                @endforeach
            </div>

            <div class="p-3">
                {{ $templates->links() }}
            </div>
        </div>
    </div>
@endif
