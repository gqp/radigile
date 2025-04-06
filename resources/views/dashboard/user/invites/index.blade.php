@extends('layouts.app')

@section('content')
    <div class="container">
        <h1>My Invites</h1>

        <form action="{{ route('user.invites.send') }}" method="POST">
            @csrf

            <div class="mb-3">
                <label for="email" class="form-label">Recipient's Email</label>
                <input type="email" class="form-control" id="email" name="email" required>
            </div>

            <div class="mb-3">
                <label for="max_uses" class="form-label">Maximum Uses</label>
                <input type="number" class="form-control" id="max_uses" name="max_uses" value="1" required>
            </div>

            <div class="mb-3">
                <label for="expires_at" class="form-label">Expiration Date</label>
                <input type="date" class="form-control" id="expires_at" name="expires_at">
            </div>

            <button type="submit" class="btn btn-primary">Send Invite</button>
        </form>

        <h2>Sent Invites</h2>
        <ul>
            @foreach($invites as $invite)
                <li>
                    Code: {{ $invite->code }}
                    @if($invite->is_active)
                        (Active)
                    @else
                        (Expired)
                    @endif
                </li>
            @endforeach
        </ul>
    </div>
@endsection
