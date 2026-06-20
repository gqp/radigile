@extends('layouts.admin')

@section('content')
    <div class="container">
        <div class="row justify-content-center">
            <div class="col-md-10">
                <div class="card">
                    <div class="card-header d-flex justify-content-between align-items-center">
                        <span>Team Domains</span>
                        <a href="{{ route('admin.team-domains.create') }}" class="btn btn-primary btn-sm">Add New Domain</a>
                        {{-- Flash success message --}}
                        @if (session('success'))
                            <div class="alert alert-success">
                                {{ session('success') }}
                            </div>
                        @endif

                        {{-- General error messages (only show if no success message exists) --}}
                        @if (!$errors->isEmpty() && !session('success'))
                            @if ($errors->has('error')) {{-- Check if there's a general error --}}
                            <div class="alert alert-danger">
                                {{ $errors->first('error') }}
                            </div>
                            @else {{-- Show validation errors --}}
                            <div class="alert alert-danger">
                                <ul>
                                    @foreach ($errors->all() as $error)
                                        <li>{{ $error }}</li>
                                    @endforeach
                                </ul>
                            </div>
                            @endif
                        @endif
                    </div>

                    <div class="card-body">
                        @if($teamDomains->isEmpty())
                            <p class="text-center">No team domains found.</p>
                        @else
                            <table class="table table-striped">
                                <thead>
                                <tr>
                                    <th>ID</th>
                                    <th>Name</th>
                                    <th>Created At</th>
                                    <th>Actions</th>
                                </tr>
                                </thead>
                                <tbody>
                                @foreach($teamDomains as $domain)
                                    <tr>
                                        <td>{{ $domain->id }}</td>
                                        <td>{{ $domain->name }}</td>
                                        <td>{{ $domain->created_at->format('Y-m-d H:i') }}</td>
                                        <td>
                                            <a href="{{ route('admin.team-domains.edit', $domain->id) }}" class="btn btn-sm btn-warning">Edit</a>
                                            <form action="{{ route('admin.team-domains.destroy', $domain->id) }}" method="POST" class="d-inline-block">
                                                @csrf
                                                @method('DELETE')
                                                <button type="submit" class="btn btn-sm btn-danger" onclick="return confirm('Are you sure you want to delete this domain?')">Delete</button>
                                            </form>
                                        </td>
                                    </tr>
                                @endforeach
                                </tbody>
                            </table>

                            <div class="d-flex justify-content-center">
                                {{ $teamDomains->links() }}
                            </div>
                        @endif
                    </div>
                </div>
            </div>
        </div>
    </div>
@endsection
