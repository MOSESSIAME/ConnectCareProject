@extends('layouts.app')

@section('content')
<div class="container py-4">
    <h2 class="fw-bold text-primary mb-3">
        <i class="bi bi-clock-history me-2"></i> My Follow-up History
    </h2>
    <p class="text-muted mb-4">
        View all follow-ups you’ve logged so far on assigned members.
    </p>

    {{-- ✅ Success message --}}
    @if(session('success'))
        <div class="alert alert-success">{{ session('success') }}</div>
    @endif

    {{-- ✅ Follow-ups Table --}}
    <div class="card shadow-sm border-0">
        <div class="card-header bg-light fw-semibold">
            <i class="bi bi-list-task me-1"></i> Follow-up Records
        </div>
        <div class="card-body">
            @if($followups->count())
                <div class="table-responsive">
                    <table class="table table-striped table-bordered align-middle">
                        <thead class="table-dark">
                            <tr>
                                <th>#</th>
                                <th>Member</th>
                                <th>Method</th>
                                <th>Outcome</th>
                                <th>Status</th>
                                <th>Date Logged</th>
                            </tr>
                        </thead>
                        <tbody>
                            @foreach($followups as $key => $followup)
                                <tr>
                                    <td>{{ $key + 1 }}</td>
                                    <td>{{ $followup->assignment->member->full_name ?? 'N/A' }}</td>
                                    <td>{{ $followup->method }}</td>
                                    <td>{{ $followup->outcome ?? '—' }}</td>
                                    <td>
                                        <span class="badge 
                                            @if($followup->status === 'Completed') bg-success
                                            @elseif($followup->status === 'In Progress') bg-info
                                            @else bg-warning text-dark
                                            @endif">
                                            {{ $followup->status }}
                                        </span>
                                    </td>
                                    <td>{{ $followup->created_at->format('d M Y') }}</td>
                                </tr>
                            @endforeach
                        </tbody>
                    </table>
                </div>

                {{-- Pagination --}}
                <div class="mt-3">
                    {{ $followups->links() }}
                </div>
            @else
                <div class="alert alert-light border text-center mb-0">
                    <i class="bi bi-info-circle text-primary me-1"></i>
                    You have not logged any follow-ups yet.
                </div>
            @endif
        </div>
    </div>
</div>
@endsection

@push('styles')
<link href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.11.1/font/bootstrap-icons.css" rel="stylesheet">
@endpush
