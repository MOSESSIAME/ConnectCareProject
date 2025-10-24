@extends('layouts.app')

@section('content')
<div class="container">
    <div class="d-flex justify-content-between align-items-center mb-4">
        <h2 class="fw-bold text-primary">{{ $title ?? 'Homecells' }}</h2>
        <a href="{{ route('admin.homecells.create') }}" class="btn btn-success">
            + Add Homecell
        </a>
    </div>

    @if(session('success'))
        <div class="alert alert-success">{{ session('success') }}</div>
    @endif

    <div class="card shadow-sm">
        <div class="card-header bg-dark text-white">Homecell List</div>
        <div class="card-body p-0">
            @if($homecells->count())
                <table class="table table-bordered table-striped mb-0 align-middle">
                    <thead class="table-dark">
                        <tr>
                            <th>#</th>
                            <th>Name</th>
                            <th>Zone</th>
                            <th>Leader</th>
                            <th>Created</th>
                            <th style="width: 180px;">Actions</th>
                        </tr>
                    </thead>
                    <tbody>
                        @foreach($homecells as $i => $homecell)
                            <tr>
                                <td>{{ $homecells->firstItem() + $i }}</td>
                                <td>{{ $homecell->name }}</td>
                                <td>{{ $homecell->zone->name ?? 'N/A' }}</td>
                                <td>{{ $homecell->leader->name ?? 'Not Assigned' }}</td>
                                <td>{{ optional($homecell->created_at)->format('d M, Y') }}</td>
                                <td>
                                    <a href="{{ route('admin.homecells.edit', $homecell) }}" class="btn btn-sm btn-warning">
                                        Edit
                                    </a>
                                    <form action="{{ route('admin.homecells.destroy', $homecell) }}" method="POST" class="d-inline">
                                        @csrf
                                        @method('DELETE')
                                        <button class="btn btn-sm btn-danger"
                                                onclick="return confirm('Delete this homecell?')">
                                            Delete
                                        </button>
                                    </form>
                                </td>
                            </tr>
                        @endforeach
                    </tbody>
                </table>

                <div class="mt-3 px-3">
                    {{ $homecells->links() }}
                </div>
            @else
                <p class="text-muted text-center p-3 mb-0">No homecells found.</p>
            @endif
        </div>
    </div>
</div>
@endsection
