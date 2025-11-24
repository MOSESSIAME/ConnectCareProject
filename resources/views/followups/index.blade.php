@extends('layouts.app')

@section('content')
<div class="container py-4">
    <h2 class="fw-bold text-primary mb-3">
        <i class="bi bi-clipboard-check me-2"></i>
        Follow-ups for {{ $assignment->member->full_name ?? 'N/A' }}
    </h2>

    <p class="text-muted mb-4">
        Below is the list of follow-up records logged for this member.
    </p>

    @if(session('success'))
        <div class="alert alert-success">{{ session('success') }}</div>
    @endif

    <div class="card shadow-sm border-0">
        <div class="card-header bg-light fw-semibold d-flex justify-content-between align-items-center">
            <span><i class="bi bi-list-task me-1"></i> Follow-up Logs</span>
            <a href="{{ route('followups.create', $assignment->id) }}" class="btn btn-sm btn-primary">
                <i class="bi bi-plus-circle"></i> Add Follow-up
            </a>
        </div>

        <div class="card-body">
            @if($histories->count())
                <div class="table-responsive">
                    <table class="table table-striped table-bordered align-middle">
                        <thead class="table-dark">
                            <tr>
                                <th style="width: 60px;">#</th>
                                <th style="width: 140px;">Method</th>
                                <th>Notes</th>
                                <th style="width: 180px;">Outcome</th>
                                <th style="width: 160px;">Status</th>
                                <th style="width: 160px;">Date Logged</th>
                                <th style="width:140px;">Actions</th>
                            </tr>
                        </thead>
                        <tbody>
                            @foreach($histories as $i => $followup)
                                <tr>
                                    <td>{{ $i + 1 }}</td>

                                    <td>{{ $followup->method }}</td>

                                    <td>
                                        @php
                                            $notes = trim((string) $followup->notes);
                                        @endphp
                                        @if($notes !== '')
                                            <span title="{{ $notes }}">
                                                {{ \Illuminate\Support\Str::limit($notes, 120) }}
                                            </span>
                                        @else
                                            <span class="text-muted">—</span>
                                        @endif
                                    </td>

                                    <td>
                                        @php
                                            $outcome = $followup->outcome ?? '—';
                                            $outcomeClass = match($outcome) {
                                                'Reached', 'Visited', 'Prayed With' => 'bg-success',
                                                'No Answer', 'Busy', 'Switched Off', 'Left Message' => 'bg-warning text-dark',
                                                'Wrong Number', 'Declined' => 'bg-danger',
                                                default => 'bg-secondary'
                                            };
                                        @endphp
                                        <span class="badge {{ $outcomeClass }}">{{ $outcome }}</span>
                                    </td>

                                    <td>
                                        @php
                                            $statusClass = match($followup->status) {
                                                'Completed' => 'bg-success',
                                                'In Progress' => 'bg-info',
                                                default => 'bg-warning text-dark'
                                            };
                                        @endphp
                                        <span class="badge {{ $statusClass }}">{{ $followup->status }}</span>
                                    </td>

                                    <td>{{ $followup->created_at->format('d M Y') }}</td>

                                    <td class="text-nowrap">
                                        {{-- Edit --}}
                                        <a href="{{ route('followups.edit', $followup->id) }}" class="btn btn-sm btn-outline-secondary">
                                            <i class="bi bi-pencil"></i> Edit
                                        </a>

                                        {{-- Delete (confirm) --}}
                                        <form action="{{ route('followups.destroy', $followup->id) }}" method="POST" class="d-inline ms-1" onsubmit="return confirm('Delete this follow-up?');">
                                            @csrf
                                            @method('DELETE')
                                            <button class="btn btn-sm btn-danger">
                                                <i class="bi bi-trash"></i> Delete
                                            </button>
                                        </form>
                                    </td>
                                </tr>
                            @endforeach
                        </tbody>
                    </table>
                </div>

                {{-- If $histories is a paginator, show links --}}
                @if(method_exists($histories, 'links'))
                    <div class="mt-3">
                        {{ $histories->links() }}
                    </div>
                @endif
            @else
                <div class="alert alert-light border text-center mb-0">
                    <i class="bi bi-info-circle text-primary me-1"></i>
                    No follow-up records found for this assignment.
                </div>
            @endif
        </div>
    </div>
</div>
@endsection

@push('styles')
<link href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.11.1/font/bootstrap-icons.css" rel="stylesheet">
@endpush
