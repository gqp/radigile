@if (session('status'))
    <div class="alert alert-success">
        {{ session('status') }}
    </div>
@endif

@if ($errors->any())
    <div class="alert alert-danger">
        <ul>
            @foreach ($errors->all() as $error)
                <li>{{ $error }}</li>
            @endforeach
        </ul>
    </div>
@endif

<form method="POST" action="{{ route('password.email') }}">
    @csrf
    <label for="email">Enter your email address:</label>
    <input type="email" name="email" id="email" value="{{ old('email') }}" required>

    <button type="submit">Send Password Reset Link</button>
</form>

<a href="{{ route('login') }}">Back to Login</a>
