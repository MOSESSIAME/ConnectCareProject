@extends('layouts.app')

@section('content')
@php
    $badge = function ($s) {
        return match($s) {
            'Completed'  => 'bg-success',
            'Reassigned' => 'bg-warning text-dark',
            'Active'     => 'bg-primary',
            default      => 'bg-secondary'
        };
    };

    // Support both controller variable names: prefer $counts (new), fall back to $stats (legacy)
    $k = $counts ?? ($stats ?? []);
@endphp

<div class="container py-3">

    <h2 class="fw-bold mb-3">Welcome, {{ Auth::user()->name }}</h2>

    {{-- No team notice --}}
    @if(empty($team?->id))
        <div class="alert alert-warning">
            {{-- Your account isn’t attached to a team yet. Ask Admin to assign a <code>team_id</code>. --}}
        </div>
    @endif

    {{-- KPI row (Updated: show Active+Reassigned, Unassigned Members, Completed) --}}
    <div class="row g-3 mb-4 text-center">
        <div class="col-md-4">
            <div class="card border-0 shadow-sm rounded-4" style="background:#0d6efd">
                <div class="card-body text-white">
                    <div class="small opacity-75">Active / Reassigned</div>
                    <div class="fs-1 fw-bold">{{ $k['active_reassigned'] ?? $k['open'] ?? 0 }}</div>
                </div>
            </div>
        </div>

        <div class="col-md-4">
            <div class="card border-0 shadow-sm rounded-4" style="background:#ffc107">
                <div class="card-body text-dark">
                    <div class="small opacity-75">Unassigned Members</div>
                    <div class="fs-1 fw-bold">{{ $k['unassigned_members'] ?? 0 }}</div>
                </div>
            </div>
        </div>

        <div class="col-md-4">
            <div class="card border-0 shadow-sm rounded-4" style="background:#198754">
                <div class="card-body text-white">
                    <div class="small opacity-75">Completed</div>
                    <div class="fs-1 fw-bold">{{ $k['completed'] ?? 0 }}</div>
                </div>
            </div>
        </div>
    </div>

    {{-- Team Members --}}
    <div class="card border-0 shadow-sm mb-4">
        <div class="card-header bg-light fw-semibold">
            My Team
        </div>
        <div class="card-body">
            @if(($teamMembers ?? collect())->count())
                <div class="d-flex flex-wrap gap-2">
                    @foreach($teamMembers as $tm)
                        <span class="badge text-bg-secondary px-3 py-2">
                            <i class="bi bi-person-fill me-1"></i>{{ $tm->name }}
                        </span>
                    @endforeach
                </div>
            @else
                <div class="alert alert-light border mb-0">No team members set.</div>
            @endif
        </div>
    </div>

    {{-- Recent Assignments --}}
    <div class="card border-0 shadow-sm">
        <div class="card-header bg-light fw-semibold d-flex justify-content-between align-items-center">
            <span><i class="bi bi-list-task me-2"></i> Recent Team Assignments</span>
            <small class="text-muted">{{ now()->format('d M Y') }}</small>
        </div>

        <div class="card-body">
            @if(($recentAssignments ?? collect())->count())
                <div class="table-responsive">
                    <table class="table table-bordered align-middle">
                        <thead class="table-dark">
                            <tr>
                                <th style="width:5%">#</th>
                                <th>Member</th>
                                <th>Assigned To</th>
                                <th>Status</th>
                                <th style="width: 260px;">Actions</th>
                            </tr>
                        </thead>
                        <tbody>
                            @foreach($recentAssignments as $i => $as)
                                <tr>
                                    <td>{{ $i + 1 }}</td>
                                    <td>
                                        {{ $as->member->full_name ?? 'N/A' }}
                                        @if(optional($as->member)->phone)
                                            <div class="small text-muted">{{ $as->member->phone }}</div>
                                        @endif
                                    </td>
                                    <td>
                                        @if(is_null($as->assigned_to))
                                            <span class="text-muted">— unassigned —</span>
                                        @else
                                            {{ $as->assignedTo->name ?? '—' }}
                                        @endif
                                    </td>
                                    <td>
                                        <span class="badge {{ $badge($as->status) }}">{{ $as->status }}</span>
                                    </td>
                                    <td>
                                        @if(is_null($as->assigned_to) && optional(auth()->user()->leadsTeam)->id === optional($as->team)->id)
                                            <form action="{{ route('assignments.assignToMember', $as->id) }}" method="POST" class="d-flex gap-2">
                                                @csrf
                                                <select name="assigned_to" class="form-select form-select-sm" required>
                                                    <option value="" selected disabled>— choose team member —</option>
                                                    @foreach(($teamMembers ?? []) as $tm)
                                                        <option value="{{ $tm->id }}">{{ $tm->name }}</option>
                                                    @endforeach
                                                </select>
                                                <button class="btn btn-sm btn-outline-primary">Assign</button>
                                            </form>
                                        @else
                                            <div class="d-flex gap-2">
                                                <a href="{{ route('followups.index', $as->id) }}" class="btn btn-sm btn-outline-secondary">
                                                    <i class="bi bi-eye me-1"></i> View
                                                </a>
                                                <a href="{{ route('followups.create', $as->id) }}" class="btn btn-sm btn-success">
                                                    <i class="bi bi-plus-circle me-1"></i> Add Follow-up
                                                </a>
                                                <a href="{{ route('assignments.reassign') }}?assignment_id={{ $as->id }}" class="btn btn-sm btn-warning">
                                                    <i class="bi bi-shuffle me-1"></i> Reassign
                                                </a>
                                            </div>
                                        @endif
                                    </td>
                                </tr>
                            @endforeach
                        </tbody>
                    </table>
                </div>
            @else
                <div class="alert alert-light border mb-0">
                    No assignments yet for your team.
                </div>
            @endif
        </div>
    </div>
</div>
@endsection