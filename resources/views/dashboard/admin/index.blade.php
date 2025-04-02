@extends('layouts.app')

@section('content')
    <div class="container">
        <div class="row justify-content-center">
            <div class="col-md-8">
                <div class="card">
                    <div class="card-header">
                        <h1>Hi Admin: {{ Auth::user()->name }}</h1>
                        <hr>

                    </div>

                    <div class="card-body">
                        <Span><strong>Dashboard</strong></Span>
                    </div>
                </div>
            </div>
        </div>
    </div>
@endsection
