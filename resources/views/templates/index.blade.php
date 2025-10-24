@extends('layouts.app')

@section('content')
<div class="container py-4">
    <div class="d-flex justify-content-between align-items-center mb-3">
        <h2 class="fw-bold text-primary mb-0">
            <i class="bi bi-file-text me-2"></i> Message Templates
        </h2>
        <a href="{{ route('templates.create') }}" class="btn btn-success">
            <i class="bi bi-plus-circle me-1"></i> New Template
        </a>
    </div>

    @if(session('success'))
        <div class="alert alert-success">{{ session('success') }}</div>
    @endif

    <div class="card shadow-sm border-0">
        <div class="card-body p-0">
            @if($templates->count())
                <div class="table-responsive">
                    <table class="table table-striped align-middle mb-0">
                        <thead class="table-dark">
                            <tr>
                                <th style="width: 50px;">#</th>
                                <th>Title</th>
                                <th>Category</th>
                                <th>Channel</th>
                                <th>Last Updated</th>
                                <th style="width: 160px;">Actions</th>
                            </tr>
                        </thead>
                        <tbody>
                            @foreach($templates as $i => $t)
                                <tr>
                                    <td>{{ $templates->firstItem() + $i }}</td>
                                    <td>
                                        <a href="{{ route('templates.show', $t) }}" class="text-decoration-none fw-semibold text-dark">
                                            {{ $t->title }}
                                        </a>
                                    </td>
                                    <td>{{ ucfirst($t->category ?? 'General') }}</td>
                                    <td class="text-uppercase">{{ $t->channel }}</td>
                                    <td>{{ $t->updated_at->format('d M Y, H:i') }}</td>
                                    <td>
                                        <a href="{{ route('templates.edit', $t) }}" class="btn btn-sm btn-warning me-1">
                                            <i class="bi bi-pencil-square"></i>
                                        </a>
                                        <form action="{{ route('templates.destroy', $t) }}" method="POST" class="d-inline"
                                              onsubmit="return confirm('Delete this template?');">
                                            @csrf
                                            @method('DELETE')
                                            <button class="btn btn-sm btn-danger">
                                                <i class="bi bi-trash"></i>
                                            </button>
                                        </form>
                                    </td>
                                </tr>
                            @endforeach
                        </tbody>
                    </table>
                </div>

                <div class="p-3 border-top">
                    {{ $templates->links() }}
                </div>
            @else
                <div class="p-4 text-center text-muted">
                    <i class="bi bi-info-circle me-1"></i> No templates found.
                </div>
            @endif
        </div>
    </div>
</div>
@endsection
