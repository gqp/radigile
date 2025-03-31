@extends('layouts.app')

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

@section('content')
    <div class="container">
        <h1>Welcome to the Homepage!</h1>
        <p>This is the main landing page of your application.</p>
    </div>
@endsection
