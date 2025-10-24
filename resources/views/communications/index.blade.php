@extends('layouts.app')

@section('content')
<div class="container">
    <h2 class="fw-bold mb-4">Communications</h2>

    @if(session('success'))
        <div class="alert alert-success">{{ session('success') }}</div>
    @endif

    <div class="mb-3">
        <a href="{{ route('communications.create') }}" class="btn btn-primary">+ New Message</a>
    </div>

    <div class="card shadow-sm">
        <div class="card-body p-0">
            <div class="table-responsive">
                <table class="table mb-0 align-middle">
                    <thead class="table-light">
                        <tr>
                            <th>Sent</th>
                            <th>Title</th>
                            <th>Channel</th>
                            <th>Audience</th>
                            <th>Status</th>
                            <th>Created By</th>
                        </tr>
                    </thead>
                    <tbody>
                        @forelse($records as $c)
                            <tr>
                                <td>{{ $c->sent_at?->format('d M Y H:i') ?? '—' }}</td>
                                <td><a href="{{ route('communications.show', $c) }}">{{ $c->title }}</a></td>
                                <td class="text-uppercase">{{ $c->channel }}</td>
                                <td>{{ Str::headline($c->audience) }}</td>
                                <td><span class="badge text-bg-{{ $c->status === 'sent' ? 'success' : ($c->status === 'failed' ? 'danger':'secondary') }}">{{ $c->status }}</span></td>
                                <td>{{ $c->creator?->name ?? '—' }}</td>
                            </tr>
                        @empty
                            <tr><td colspan="6" class="text-center text-muted p-4">No messages yet.</td></tr>
                        @endforelse
                    </tbody>
                </table>
            </div>
        </div>
        <div class="card-footer">
            {{ $records->links() }}
        </div>
    </div>
</div>
@endsection
