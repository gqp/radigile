@extends('layouts.app')

@section('content')
    <div class="container">
        <h3>Share Your Invites</h3>

        <p>You have <strong>{{ $remainingInvites }}</strong> invites remaining.</p>

        {{-- Form to specify the number of email forms --}}
        <form id="generator-form" method="GET">
            <div class="mb-3">
                <label for="number_of_forms" class="form-label">Number of Email Forms to Display</label>
                <input
                    type="number"
                    id="number_of_forms"
                    name="number_of_forms"
                    class="form-control"
                    min="1"
                    max="{{ $remainingInvites }}"
                    required
                    value="{{ old('number_of_forms') }}">
            </div>
            <button type="submit" class="btn btn-primary">Generate Forms</button>
        </form>

        <hr>

        {{-- Display the email forms dynamically --}}
        @if (request()->has('number_of_forms'))
            @php
                $numberOfForms = (int) request('number_of_forms');
            @endphp

            @if ($numberOfForms > $remainingInvites)
                <div class="alert alert-danger">
                    You cannot generate more forms than your remaining invites (<strong>{{ $remainingInvites }}</strong>).
                </div>
            @else
                @for ($i = 0; $i < $numberOfForms; $i++)
                    <form action="{{ route('user.invites.send') }}" method="POST" class="mb-3">
                        @csrf

                        <div class="mb-3">
                            <label for="emails[{{ $i }}]" class="form-label">Recipient Email (Form {{ $i + 1 }})</label>
                            <input
                                type="email"
                                name="emails[{{ $i }}]"
                                id="emails[{{ $i }}]"
                                class="form-control"
                                required>
                        </div>

                        <button type="submit" class="btn btn-success">Send Invite</button>
                    </form>
                @endfor
            @endif
        @endif
    </div>
@endsection
