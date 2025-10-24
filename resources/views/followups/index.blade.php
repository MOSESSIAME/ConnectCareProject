@extends('layouts.app')

@section('content')
<div class="container py-4">
    <h2 class="fw-bold text-primary mb-3">
        <i class="bi bi-clipboard-check me-2"></i> Follow-ups for {{ $assignment->member->full_name ?? 'N/A' }}
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
                                <th>#</th>
                                <th>Method</th>
                                <th>Outcome</th>
                                <th>Status</th>
                                <th>Date Logged</th>
                            </tr>
                        </thead>
                        <tbody>
                            @foreach($histories as $key => $followup)
                                <tr>
                                    <td>{{ $key + 1 }}</td>
                                    <td>{{ $followup->method }}</td>
                                    <td>{{ $followup->outcome ?? '—' }}</td>
                                    <td>
                                        <span class="badge 
                                            @if($followup->status === 'Completed') bg-success
                                            @elseif($followup->status === 'In Progress') bg-info
                                            @else bg-warning text-dark @endif">
                                            {{ $followup->status }}
                                        </span>
                                    </td>
                                    <td>{{ $followup->created_at->format('d M Y') }}</td>
                                </tr>
                            @endforeach
                        </tbody>
                    </table>
                </div>
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
