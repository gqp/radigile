@extends('layouts.app')

@section('content')

    @if ($errors->any())
        <div class="alert alert-danger">
            <ul>
                @foreach ($errors->all() as $error)
                    <li>{{ $error }}</li>
                @endforeach
            </ul>
        </div>
    @endif


    <div class="container mx-auto p-6">
    <h1 class="text-2xl font-bold mb-4">Register</h1>

    <!-- Display Validation Errors -->
    @if ($errors->any())
        <div class="mb-4">
            <ul class="list-disc list-inside text-red-600">
                @foreach ($errors->all() as $error)
                    <li>{{ $error }}</li>
                @endforeach
            </ul>
        </div>
    @endif

    <form action="{{ route('register') }}" method="POST">
        @csrf

        <!-- First-Name Field -->
        <div class="mb-4">
            <label for="first-name" class="block text-sm font-medium text-gray-700">First Name</label>
            <input type="text" id="first-name" name="first-name" value="{{ old('first-name') }}" required
                   class="block w-full mt-1 px-4 py-2 border rounded-md">
        </div>

        <!-- Last-Name Field -->
        <div class="mb-4">
            <label for="last-name" class="block text-sm font-medium text-gray-700">Last Name</label>
            <input type="text" id="last-name" name="last-name" value="{{ old('last-name') }}" required
                   class="block w-full mt-1 px-4 py-2 border rounded-md">
        </div>

        <!-- Email Field -->
        <div class="mb-4">
            <label for="email" class="block text-sm font-medium text-gray-700">Email</label>
            <input type="email" id="email" name="email" value="{{ old('email') }}" required
                   class="block w-full mt-1 px-4 py-2 border rounded-md">
        </div>

        <!-- Password Field -->
        <div class="mb-4">
            <label for="password" class="block text-sm font-medium text-gray-700">Password</label>
            <input type="password" id="password" name="password" required
                   class="block w-full mt-1 px-4 py-2 border rounded-md">
            <small class="text-gray-500">Must be at least 8 characters, include an uppercase letter, a lowercase letter, and a number.</small>
        </div>

        <!-- Invitation Code -->
        <div class="mb-4">
            <label for="invitation_code" class="block text-sm font-medium text-gray-700">Invitation Code</label>
            <input type="text" id="invitation_code" name="invitation_code" value="{{ old('invitation_code') }}" required
                   class="block w-full mt-1 px-4 py-2 border rounded-md">
        </div>

        <!-- Terms and Conditions -->
        <div class="mb-4">
            <label class="flex items-center">
                <input type="checkbox" name="agreed_to_terms" required
                       class="mr-2">
                I agree to the <a href="#" class="text-blue-500 hover:underline">terms and conditions</a>.
            </label>
        </div>

        <!-- Submit Button -->
        <div>
            <button type="submit" class="px-4 py-2 bg-blue-600 text-white rounded-md hover:bg-blue-700">
                Register
            </button>
        </div>
    </form>
</div>

@endsection
