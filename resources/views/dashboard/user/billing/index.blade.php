@extends('layouts.app')

@section('content')
@include('layouts.partials.navbar')
<div class="container mt-4">

    @foreach (['success', 'error', 'info'] as $type)
        @if (session($type))
            <div class="alert alert-{{ $type === 'error' ? 'danger' : $type }} alert-dismissible fade show">
                {{ session($type) }}
                <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
            </div>
        @endif
    @endforeach

    <h4 class="mb-4"><i class="fas fa-credit-card"></i> Billing</h4>

    {{-- Current Subscription --}}
    <div class="card shadow-sm mb-4">
        <div class="card-header bg-dark text-white">
            <strong><i class="fas fa-file-invoice-dollar"></i> Your Subscription</strong>
        </div>
        <div class="card-body">
            @if ($subscription)
                <div class="d-flex align-items-start justify-content-between flex-wrap gap-3">
                    <div>
                        <h5 class="mb-1">{{ $subscription->plan->name }}</h5>
                        <span class="badge bg-success">Active</span>
                        @if ($subscription->plan->interval !== 'free')
                            <span class="badge bg-light text-dark border">${{ number_format($subscription->plan->price, 2) }}/{{ $subscription->plan->interval }}</span>
                        @endif
                        @if ($subscription->ends_at)
                            <small class="text-muted ms-2">Renews {{ $subscription->ends_at->format('M j, Y') }}</small>
                        @endif

                        @php $planFeatures = $subscription->plan->features ?? []; @endphp
                        @if (!empty($planFeatures))
                            <div class="mt-3">
                                <small class="text-muted fw-semibold d-block mb-1">Included features:</small>
                                @foreach ($planFeatures as $featureKey)
                                    <span class="badge bg-light text-dark border me-1 mb-1">
                                        <i class="fas fa-check text-success me-1"></i>
                                        {{ \App\Models\Plan::$availableFeatures[$featureKey] ?? $featureKey }}
                                    </span>
                                @endforeach
                            </div>
                        @else
                            <p class="text-muted small mt-2 mb-0">No additional features included on this plan.</p>
                        @endif

                        <div class="mt-2">
                            <small class="text-muted">
                                Teams: <strong>{{ $subscription->plan->max_teams }}</strong> &nbsp;|&nbsp;
                                Members per team: <strong>{{ $subscription->plan->max_members }}</strong>
                            </small>
                        </div>
                    </div>

                    @if ($subscription->plan->interval !== 'free')
                        <div class="d-flex flex-column align-items-end gap-1">
                            <a href="{{ route('billing.portal') }}" class="btn btn-outline-secondary btn-sm">
                                <i class="fas fa-cog"></i> Manage Billing
                            </a>
                            <small class="text-muted">Change plan, update payment method, or cancel</small>
                        </div>
                    @endif
                </div>
            @else
                <div class="d-flex align-items-center justify-content-between flex-wrap gap-3">
                    <p class="mb-0 text-muted">You don't have an active plan yet.</p>
                    <form action="{{ route('subscribe.free') }}" method="POST">
                        @csrf
                        <button type="submit" class="btn btn-success btn-sm">Get Started Free</button>
                    </form>
                </div>
            @endif
        </div>
    </div>

    {{-- Available Plans --}}
    <div class="card shadow-sm">
        <div class="card-header bg-light">
            <strong><i class="fas fa-layer-group"></i> Available Plans</strong>
        </div>
        <div class="card-body">
            @forelse ($availablePlans as $plan)
                @php $isCurrentPlan = $subscription && $subscription->plan_id === $plan->id; @endphp
                <div class="d-flex align-items-center justify-content-between flex-wrap gap-3 py-2 {{ !$loop->last ? 'border-bottom' : '' }}">
                    <div>
                        <h6 class="mb-1">
                            {{ $plan->name }}
                            @if ($isCurrentPlan)
                                <span class="badge bg-success ms-1">Current Plan</span>
                            @endif
                        </h6>
                        <span class="text-muted">${{ number_format($plan->price, 2) }}/{{ $plan->interval }}</span>
                        <small class="text-muted d-block">
                            Teams: {{ $plan->max_teams }} &nbsp;|&nbsp; Members per team: {{ $plan->max_members }}
                        </small>
                    </div>

                    @if ($isCurrentPlan)
                        {{-- no action needed, already on this plan --}}
                    @elseif ($subscription && $subscription->plan->interval !== 'free')
                        <a href="{{ route('billing.portal') }}" class="btn btn-sm btn-outline-primary">
                            Switch via Billing Portal
                        </a>
                    @else
                        <a href="{{ route('billing.checkout', $plan) }}" class="btn btn-sm btn-primary">
                            Upgrade to {{ $plan->name }}
                        </a>
                    @endif
                </div>
            @empty
                <p class="text-muted mb-0">No paid plans are currently available.</p>
            @endforelse
        </div>
    </div>

</div>
@endsection
