@extends('layouts.app')

@section('content')
<div class="container">
    <h2 class="mb-4 fw-bold">Manage Users</h2>

    {{-- Success Message --}}
    @if(session('success'))
        <div class="alert alert-success">{{ session('success') }}</div>
    @endif

    {{-- Add User Button --}}
    <div class="mb-3 text-end">
        <a href="{{ route('admin.users.create') }}" class="btn btn-primary">
            + Add New User
        </a>
    </div>

    {{-- Users Table --}}
    <div class="card shadow-sm">
        <div class="card-body p-0">
            <table class="table table-hover mb-0">
                <thead class="table-light">
                    <tr>
                        <th>#</th>
                        <th>Name</th>
                        <th>Email</th>
                        <th>Role</th>
                        <th>Created</th>
                        <th class="text-end">Actions</th>
                    </tr>
                </thead>
                <tbody>
                    @forelse($users as $index => $user)
                        <tr>
                            <td>{{ $users->firstItem() + $index }}</td>
                            <td>{{ $user->name }}</td>
                            <td>{{ $user->email }}</td>
                            <td><span class="badge bg-secondary">{{ $user->role->name ?? 'N/A' }}</span></td>
                            <td>{{ $user->created_at->format('d M Y') }}</td>
                            <td class="text-end">
                                <a href="{{ route('admin.users.edit', $user->id) }}" class="btn btn-sm btn-warning">Edit</a>
                                
                                <form action="{{ route('admin.users.destroy', $user->id) }}" method="POST" class="d-inline">
                                    @csrf
                                    @method('DELETE')
                                    <button class="btn btn-sm btn-danger" onclick="return confirm('Delete this user?')">Delete</button>
                                </form>

                                <form action="{{ route('admin.users.reset-password', $user->id) }}" method="POST" class="d-inline">
                                    @csrf
                                    <button class="btn btn-sm btn-info" onclick="return confirm('Reset password for this user?')">Reset</button>
                                </form>
                            </td>
                        </tr>
                    @empty
                        <tr>
                            <td colspan="6" class="text-center text-muted">No users found.</td>
                        </tr>
                    @endforelse
                </tbody>
            </table>
        </div>
    </div>

    {{-- Pagination --}}
    <div class="mt-3">
        {{ $users->links() }}
    </div>
</div>
@endsection
