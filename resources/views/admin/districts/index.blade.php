@extends('layouts.app')

@section('content')
<div class="container">
    <h2 class="fw-bold mb-4">Districts</h2>

    <a href="{{ route('admin.districts.create') }}" class="btn btn-primary mb-3">
        + Add District
    </a>

    @if(session('success'))
        <div class="alert alert-success">{{ session('success') }}</div>
    @endif

    <table class="table table-bordered bg-white shadow-sm">
        <thead class="table-light">
            <tr>
                <th>#</th>
                <th>Name</th>
                <th>Created</th>
                <th>Actions</th>
            </tr>
        </thead>
        <tbody>
            @forelse($records as $item)
                <tr>
                    <td>{{ $loop->iteration }}</td>
                    <td>{{ $item->name }}</td>
                    <td>{{ $item->created_at?->format('Y-m-d') }}</td>
                    <td>
                        <a href="{{ route('admin.districts.edit', $item) }}" class="btn btn-sm btn-warning">Edit</a>
                        <form action="{{ route('admin.districts.destroy', $item) }}" method="POST" class="d-inline">
                            @csrf
                            @method('DELETE')
                            <button class="btn btn-sm btn-danger"
                                    onclick="return confirm('Delete this district?')">
                                Delete
                            </button>
                        </form>
                    </td>
                </tr>
            @empty
                <tr>
                    <td colspan="4" class="text-center text-muted">No districts found.</td>
                </tr>
            @endforelse
        </tbody>
    </table>

    {{ $records->links() }}
</div>
@endsection
