@extends('layouts.app')

@section('content')
<div class="container py-4">
    <div class="d-flex justify-content-between align-items-center mb-3">
        <h3 class="fw-bold text-primary mb-0">
            <i class="bi bi-inbox me-2"></i> Communications
        </h3>
        <a href="{{ route('communications.create') }}" class="btn btn-primary">
            <i class="bi bi-send me-1"></i> Compose
        </a>
    </div>

    @if(session('success'))
        <div class="alert alert-success">{{ session('success') }}</div>
    @endif

    <div class="card shadow-sm border-0">
        <div class="card-body p-0">
            @if($records->count())
                <div class="table-responsive">
                    <table class="table table-striped mb-0 align-middle">
                        <thead class="table-dark">
                            <tr>
                                <th>#</th>
                                <th>Title</th>
                                <th>Channel</th>
                                <th>Audience</th>
                                <th>Status</th>
                                <th>Scheduled</th>
                                <th>Created By</th>
                                <th>Created</th>
                            </tr>
                        </thead>
                        <tbody>
                            @foreach($records as $i => $c)
                                <tr>
                                    <td>{{ $records->firstItem() + $i }}</td>
                                    <td>{{ $c->title }}</td>
                                    <td class="text-uppercase">{{ $c->channel }}</td>
                                    <td>{{ str_replace('_',' ', $c->audience) }}</td>
                                    <td>
                                        <span class="badge {{ $c->status === 'sent' ? 'bg-success' : 'bg-secondary' }}">
                                            {{ ucfirst($c->status) }}
                                        </span>
                                    </td>
                                    <td>{{ optional($c->scheduled_at)->format('d M Y, H:i') ?? '-' }}</td>
                                    <td>{{ $c->creator->name ?? '—' }}</td>
                                    <td>{{ $c->created_at->format('d M Y, H:i') }}</td>
                                </tr>
                            @endforeach
                        </tbody>
                    </table>
                </div>
                <div class="p-3">
                    {{ $records->links() }}
                </div>
            @else
                <div class="p-4 text-center text-muted">No messages yet.</div>
            @endif
        </div>
    </div>
</div>
@endsection
