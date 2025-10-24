<!-- @extends('layouts.app')

@section('content')
<div class="container py-4">
    <h2 class="fw-bold text-primary mb-3">
        <i class="bi bi-journal-text me-2"></i> Follow-up History
    </h2>

    {{-- ✅ Assignment Info --}}
    @if(isset($assignment) && $assignment)
        <div class="card border-0 shadow-sm mb-4">
            <div class="card-body">
                <h5 class="fw-semibold mb-1">
                    Assigned Member: {{ $assignment->member->full_name ?? 'N/A' }}
                </h5>
                <p class="text-muted mb-0">
                    Current Status:
                    <span class="badge 
                        @if($assignment->status === 'Completed') bg-success
                        @elseif($assignment->status === 'Reassigned') bg-info
                        @else bg-warning text-dark @endif">
                        {{ $assignment->status }}
                    </span>
                </p>
            </div>
        </div>
    @endif

    {{-- ✅ Follow-up Logs Table --}}
    <div class="card border-0 shadow-sm">
        <div class="card-header bg-light fw-semibold d-flex justify-content-between align-items-center">
            <span><i class="bi bi-list-task me-2"></i> Follow-up Records</span>
            <a href="{{ route('followups.create', $assignment->id) }}" class="btn btn-sm btn-success">
                <i class="bi bi-plus-circle me-1"></i> Add Follow-up
            </a>
        </div>

        <div class="card-body">
            @if(isset($followups) && $followups->count())
                <div class="table-responsive">
                    <table class="table table-bordered table-striped align-middle mb-0">
                        <thead class="table-dark">
                            <tr>
                                <th>#</th>
                                <th>Method</th>
                                <th>Notes</th>
                                <th>Outcome</th>
                                <th>Status</th>
                                <th>Date Logged</th>
                            </tr>
                        </thead>
                        <tbody>
                            @foreach($followups as $index => $followup)
                                <tr>
                                    <td>{{ $index + 1 }}</td>
                                    <td>
                                        <i class="
                                            @switch($followup->method)
                                                @case('Call') bi bi-telephone @break
                                                @case('SMS') bi bi-chat-dots @break
                                                @case('WhatsApp') bi bi-whatsapp @break
                                                @case('Email') bi bi-envelope @break
                                                @case('Visit') bi bi-person-walking @break
                                                @default bi bi-chat-square
                                            @endswitch
                                        "></i> {{ $followup->method }}
                                    </td>
                                    <td>{{ $followup->notes ?? '—' }}</td>
                                    <td>{{ $followup->outcome ?? '—' }}</td>
                                    <td>
                                        <span class="badge
                                            @if($followup->status === 'Completed') bg-success
                                            @elseif($followup->status === 'In Progress') bg-info
                                            @else bg-warning text-dark @endif">
                                            {{ $followup->status }}
                                        </span>
                                    </td>
                                    <td>{{ $followup->created_at->format('d M, Y h:i A') }}</td>
                                </tr>
                            @endforeach
                        </tbody>
                    </table>
                </div>
            @else
                <div class="alert alert-light border text-center mb-0">
                    <i class="bi bi-info-circle text-primary me-2"></i>
                    No follow-ups recorded yet for this assignment.
                </div>
            @endif
        </div>
    </div>
</div>
@endsection

@push('styles')
<link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.11.1/font/bootstrap-icons.css">
@endpush -->
