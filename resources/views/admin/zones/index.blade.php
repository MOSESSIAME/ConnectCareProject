@extends('layouts.app')

@section('content')
<div class="container">
    <h2 class="fw-bold mb-4">Zones</h2>

    <a href="{{ route('admin.zones.create') }}" class="btn btn-primary mb-3">
        + Add Zone
    </a>

    @if(session('success'))
        <div class="alert alert-success">{{ session('success') }}</div>
    @endif

    <div class="table-responsive">
        <table class="table table-bordered bg-white shadow-sm">
            <thead class="table-light">
                <tr>
                    <th>#</th>
                    <th>Name</th>
                    <th>District</th>
                    <th>Created</th>
                    <th style="width: 220px;">Actions</th>
                </tr>
            </thead>
            <tbody>
                @forelse($records as $item)
                    <tr>
                        <td>{{ $loop->iteration }}</td>
                        <td>{{ $item->name }}</td>
                        <td>{{ $item->district->name ?? '—' }}</td>
                        <td>{{ $item->created_at?->format('Y-m-d') }}</td>
                        <td>
                            <a href="{{ route('admin.zones.edit', $item) }}" class="btn btn-sm btn-warning">
                                <i class="bi bi-pencil-square"></i> Edit
                            </a>
                            <form action="{{ route('admin.zones.destroy', $item) }}"
                                  method="POST" class="d-inline">
                                @csrf
                                @method('DELETE')
                                <button class="btn btn-sm btn-danger"
                                        onclick="return confirm('Delete this zone?')">
                                    <i class="bi bi-trash"></i> Delete
                                </button>
                            </form>
                        </td>
                    </tr>
                @empty
                    <tr>
                        <td colspan="5" class="text-center text-muted">No zones found.</td>
                    </tr>
                @endforelse
            </tbody>
        </table>
    </div>

    <div class="mt-3">
        {{ $records->links() }}
    </div>
</div>
@endsection
