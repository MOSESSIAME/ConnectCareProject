@extends('layouts.app')

@section('content')
@php
    // Map statuses to badge classes
    $badgeFor = fn($s) => match($s) {
        'Completed'  => 'bg-success',
        'Reassigned' => 'bg-warning text-dark',
        'Active'     => 'bg-info',
        default      => 'bg-secondary'
    };
@endphp

<div class="container py-4">
    {{-- Welcome --}}
    <h2 class="fw-bold text-primary mb-1">Welcome, {{ Auth::user()->name }}</h2>
    <p class="text-muted mb-4">Team Member Dashboard</p>

    {{-- Quick Stats --}}
    <div class="row g-3 mb-4 text-center">
        <div class="col-md-4">
            <div class="card border-0 shadow-sm rounded-4 bg-info text-white">
                <div class="card-body">
                    <h6 class="fw-semibold mb-1">Total Assignments</h6>
                    <h3 class="fw-bold">{{ $followUpStats['total'] ?? 0 }}</h3>
                </div>
            </div>
        </div>
        <div class="col-md-4">
            <div class="card border-0 shadow-sm rounded-4 bg-success text-white">
                <div class="card-body">
                    <h6 class="fw-semibold mb-1">Completed</h6>
                    <h3 class="fw-bold">{{ $followUpStats['completed'] ?? 0 }}</h3>
                </div>
            </div>
        </div>
        <div class="col-md-4">
            <div class="card border-0 shadow-sm rounded-4 bg-warning text-dark">
                <div class="card-body">
                    <h6 class="fw-semibold mb-1">Pending (Active / Reassigned)</h6>
                    <h3 class="fw-bold">{{ $followUpStats['pending'] ?? 0 }}</h3>
                </div>
            </div>
        </div>
    </div>

    {{-- Assignments List --}}
    <div class="card border-0 shadow-sm">
        <div class="card-header bg-light fw-semibold d-flex justify-content-between align-items-center">
            <span><i class="bi bi-list-task me-2"></i> Your Recent Assignments</span>
            <small class="text-muted">{{ now()->format('d M Y') }}</small>
        </div>

        <div class="card-body">
            @if(isset($assignments) && $assignments->count())
                <div class="table-responsive">
                    <table class="table table-bordered table-striped align-middle mb-0">
                        <thead class="table-dark">
                            <tr>
                                <th style="width: 5%">#</th>
                                <th>Member</th>
                                <th>Team</th>
                                <th>Status</th>
                                <th class="text-center" style="width: 20%">Actions</th>
                            </tr>
                        </thead>
                        <tbody>
                            @foreach($assignments as $i => $assignment)
                                <tr>
                                    <td>{{ $i + 1 }}</td>

                                    <td>
                                        {{ $assignment->member->full_name ?? 'N/A' }}
                                        @if(optional($assignment->member)->phone)
                                            <div class="text-muted small">{{ $assignment->member->phone }}</div>
                                        @endif
                                    </td>

                                    <td>
                                        @if($assignment->team)
                                            <span class="badge text-bg-secondary">{{ $assignment->team->name }}</span>
                                        @else
                                            <span class="text-muted">—</span>
                                        @endif
                                    </td>

                                    <td>
                                        <span class="badge {{ $badgeFor($assignment->status) }}">
                                            {{ $assignment->status }}
                                        </span>
                                    </td>

                                    <td class="text-center">
                                        {{-- ✅ Updated Button --}}
                                        <a href="{{ route('followups.index', $assignment->id) }}"
                                           class="btn btn-sm btn-primary d-flex align-items-center justify-content-center gap-1 px-3 rounded-pill shadow-sm">
                                            <i class="bi bi-chat-dots"></i>
                                            <span>Open Follow-up</span>
                                        </a>
                                    </td>
                                </tr>
                            @endforeach
                        </tbody>
                    </table>
                </div>
            @else
                <div class="alert alert-light border text-center mb-0">
                    <i class="bi bi-info-circle me-1 text-primary"></i>
                    You currently have no assigned follow-ups.
                </div>
            @endif
        </div>
    </div>
</div>
@endsection

@push('styles')
<link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.11.1/font/bootstrap-icons.css">
@endpush
