@extends('layouts.app')

@section('content')
<div class="container">
    <h2 class="fw-bold mb-4">Teams</h2>

    <!-- Add Button -->
    <a href="{{ route('admin.teams.create') }}" class="btn btn-primary mb-3">
        + Add Team
    </a>

    <!-- Success Message -->
    @if(session('success'))
        <div class="alert alert-success">{{ session('success') }}</div>
    @endif

    <!-- Teams Table -->
    <div class="table-responsive">
        <table class="table table-bordered bg-white shadow-sm align-middle">
            <thead class="table-light">
                <tr>
                    <th>#</th>
                    <th>Name</th>
                    <th>Leader</th>
                    <th>Created</th>
                    <th>Actions</th>
                </tr>
            </thead>
            <tbody>
                @forelse($teams as $team)
                    <tr>
                        <td>{{ $loop->iteration }}</td>
                        <td>{{ $team->name }}</td>
                        <td>{{ $team->leader->name ?? '—' }}</td>
                        <td>{{ $team->created_at?->format('Y-m-d') }}</td>
                        <td>
                            <a href="{{ route('admin.teams.edit', $team) }}" class="btn btn-sm btn-warning">
                                <i class="bi bi-pencil-square"></i> Edit
                            </a>
                            <form action="{{ route('admin.teams.destroy', $team) }}" method="POST" class="d-inline">
                                @csrf
                                @method('DELETE')
                                <button class="btn btn-sm btn-danger"
                                        onclick="return confirm('Are you sure you want to delete this team?')">
                                    <i class="bi bi-trash"></i> Delete
                                </button>
                            </form>
                        </td>
                    </tr>
                @empty
                    <tr>
                        <td colspan="5" class="text-center text-muted">
                            No teams found.
                        </td>
                    </tr>
                @endforelse
            </tbody>
        </table>
    </div>

    <!-- Pagination -->
    <div class="mt-3">
        {{ $teams->links() }}
    </div>
</div>
@endsection
