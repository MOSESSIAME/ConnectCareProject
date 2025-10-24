@extends('layouts.app')

@section('content')
<div class="container">
    <div class="d-flex justify-content-between align-items-center mb-3">
        <h2 class="fw-bold mb-0">Services</h2>
        <a href="{{ route('services.create') }}" class="btn btn-success">+ Add Service</a>
    </div>

    @if(session('success'))
        <div class="alert alert-success">{{ session('success') }}</div>
    @endif

    <div class="table-responsive">
        <table class="table table-bordered bg-white align-middle">
            <thead class="table-light">
                <tr>
                    <th>#</th>
                    <th>Name</th>
                    <th>Date</th>
                    <th style="width: 200px;">Actions</th>
                </tr>
            </thead>
            <tbody>
            @forelse ($services as $s)
                <tr>
                    <td>{{ $loop->iteration + ($services->currentPage() - 1) * $services->perPage() }}</td>
                    <td>{{ $s->name }}</td>
                    <td>{{ optional($s->service_date)->format('d M Y') }}</td>
                    <td>
                        <a href="{{ route('services.edit', $s->id) }}" class="btn btn-sm btn-warning">Edit</a>
                        <form action="{{ route('services.destroy', $s->id) }}" method="POST" class="d-inline">
                            @csrf @method('DELETE')
                            <button class="btn btn-sm btn-danger" onclick="return confirm('Delete this service?')">
                                Delete
                            </button>
                        </form>
                    </td>
                </tr>
            @empty
                <tr><td colspan="4" class="text-center text-muted">No services found.</td></tr>
            @endforelse
            </tbody>
        </table>
    </div>

    <div class="mt-3">
        {{ $services->links() }}
    </div>
</div>
@endsection
