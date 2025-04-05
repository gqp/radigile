@extends('layouts.app')

@section('content')
    <div class="container">
        @if (session('success'))
            <div class="alert alert-success">
                {{ session('success') }}
            </div>
        @endif

        @if (session('error'))
            <div class="alert alert-danger">
                {{ session('error') }}
            </div>
        @endif
        <div class="row justify-content-center">
            <div class="col-md-8">
                <div class="card">
                    <div class="card-header">
                        <h1>Hi Admin: {{ Auth::user()->name }}</h1>
                        <hr>

                    </div>

                    <div class="card-body">
                        <Span><strong>Admin Dashboard</strong></Span>
                    </div>
                </div>
            </div>
        </div>
    </div>
@endsection
