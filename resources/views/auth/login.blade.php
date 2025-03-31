@if (session('status'))
    <div class="alert alert-success">
        {{ session('status') }}
    </div>
@endif

@if (session('message'))
    <div class="alert alert-info">
        {{ session('message') }}
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

<form method="POST" action="{{ route('login') }}">
    @csrf
    <label for="email">Email:</label>
    <input type="email" name="email" id="email" value="{{ old('email') }}" required>

    <label for="password">Password:</label>
    <input type="password" name="password" id="password" required>

    <!-- Remember Me -->
    <label for="remember">
        <input type="checkbox" name="remember" id="remember" {{ old('remember') ? 'checked' : '' }}> Remember Me
    </label>

    <button type="submit">Login</button>
</form>

<a href="{{ route('password.request') }}">Forgot Password?</a>
