@extends('layouts.app')

@section('content')
@php($role = $role ?? (auth()->user()->role->name ?? ''))  {{-- safe fallback --}}

<div class="container">
    <div class="d-flex align-items-center justify-content-between mb-3">
        <h2 class="mb-0">Follow-up Assignments</h2>
        @if((auth()->user()->role->name ?? '') === 'Admin')
            <a href="{{ route('assignments.create') }}" class="btn btn-primary">+ Assign Member</a>
        @endif
    </div>

    {{-- Filters --}}
    <form method="GET" class="card mb-3 shadow-sm border-0">
        <div class="card-body">
            <div class="row g-2">
                {{-- Status --}}
                <div class="col-12 col-md-3">
                    <label class="form-label mb-1">Status</label>
                    <select name="status" class="form-select">
                        <option value="">All</option>
                        @foreach (['Active','Reassigned','Completed'] as $st)
                            <option value="{{ $st }}" @selected(request('status')===$st)>{{ $st }}</option>
                        @endforeach
                    </select>
                </div>

                {{-- Team (Admin only) --}}
                @if($role === 'Admin')
                <div class="col-12 col-md-3">
                    <label class="form-label mb-1">Team</label>
                    <select name="team_id" class="form-select">
                        <option value="">All</option>
                        @isset($teams)
                            @foreach ($teams as $t)
                                <option value="{{ $t->id }}" @selected((string)request('team_id') === (string)$t->id)>{{ $t->name }}</option>
                            @endforeach
                        @endisset
                    </select>
                </div>
                @endif

                {{-- Assigned To --}}
                <div class="col-12 col-md-3">
                    <label class="form-label mb-1">Assigned To</label>
                    <select name="assigned_to" class="form-select">
                        <option value="">All</option>
                        @isset($assignees)
                            @foreach ($assignees as $u)
                                <option value="{{ $u->id }}" @selected((string)request('assigned_to') === (string)$u->id)>{{ $u->name }}</option>
                            @endforeach
                        @endisset
                    </select>
                </div>

                {{-- Keyword (member) --}}
                <div class="col-12 col-md-3">
                    <label class="form-label mb-1">Search Member</label>
                    <input type="text" name="q" value="{{ request('q') }}" class="form-control" placeholder="name or phone">
                </div>
            </div>

            <div class="mt-3 d-flex gap-2">
                <button class="btn btn-outline-primary">Filter</button>
                <a href="{{ route('assignments.index') }}" class="btn btn-outline-secondary">Reset</a>
            </div>
        </div>
    </form>

    @if(session('success'))
        <div class="alert alert-success">{{ session('success') }}</div>
    @endif
    @if($errors->any())
        <div class="alert alert-danger mb-3">
            <ul class="mb-0">
                @foreach($errors->all() as $e)
                    <li>{{ $e }}</li>
                @endforeach
            </ul>
        </div>
    @endif

    <div class="table-responsive">
        <table class="table table-bordered align-middle">
            <thead class="table-light">
                <tr>
                    <th>Team</th>
                    <th>Member</th>
                    <th>Assigned To</th>
                    <th>Assigned By</th>
                    <th style="width: 190px;">Status</th>
                    <th>Created</th>
                    <th style="width: 160px;">Actions</th>
                </tr>
            </thead>
            <tbody>
                @forelse($assignments as $assignment)
                    <tr>
                        <td>{{ $assignment->team->name ?? '—' }}</td>

                        <td>
                            {{ $assignment->member->full_name ?? 'N/A' }}
                            @if(optional($assignment->member)->phone)
                                <div class="text-muted small">{{ $assignment->member->phone }}</div>
                            @endif
                        </td>

                        <td>
                            @if(is_null($assignment->assigned_to)
                                && $role === 'Team Leader'
                                && optional(auth()->user()->leadsTeam)->id === optional($assignment->team)->id)
                                <form action="{{ route('assignments.assignToMember', $assignment->id) }}" method="POST" class="d-flex gap-2">
                                    @csrf
                                    <select name="assigned_to" class="form-select form-select-sm" required>
                                        <option value="" selected disabled>-- choose member --</option>
                                        @foreach(optional($assignment->team)->members ?? [] as $m)
                                            <option value="{{ $m->id }}">{{ $m->name }}</option>
                                        @endforeach
                                    </select>
                                    <button class="btn btn-sm btn-outline-primary">Assign</button>
                                </form>
                            @else
                                {{ $assignment->assignedTo->name ?? '—' }}
                            @endif
                        </td>

                        <td>{{ $assignment->assignedBy->name ?? 'N/A' }}</td>

                        <td>
                            <form action="{{ route('assignments.updateStatus', $assignment->id) }}" method="POST" class="d-inline">
                                @csrf
                                <select name="status" onchange="this.form.submit()" class="form-select form-select-sm">
                                    <option value="Active"      @selected($assignment->status === 'Active')>Active</option>
                                    <option value="Reassigned"  @selected($assignment->status === 'Reassigned')>Reassigned</option>
                                    <option value="Completed"   @selected($assignment->status === 'Completed')>Completed</option>
                                </select>
                            </form>
                        </td>

                        <td>{{ optional($assignment->created_at)->format('d M Y') }}</td>

                        <td class="text-nowrap align-top">
                            <div class="d-flex flex-column align-items-start">
                                <div class="mb-2">
                                    <a href="{{ route('followups.assignment', $assignment->id) }}" class="btn btn-outline-primary btn-sm">
                                        <i class="fa fa-eye" aria-hidden="true"></i> View
                                    </a>

                                    <a href="{{ route('followups.create', $assignment->id) }}" class="btn btn-primary btn-sm ms-2">
                                        <i class="fa fa-plus-circle" aria-hidden="true"></i> Add Follow-up
                                    </a>
                                </div>

                                {{-- Edit / Delete (only show to Admin or the user who created the assignment) --}}
                                {{-- @if($role === 'Admin' || auth()->id() === optional($assignment->assignedBy)->id)
                                    <div>
                                        <a href="{{ route('assignments.edit', $assignment->id) }}" class="btn btn-sm btn-outline-warning">
                                            <i class="fa fa-edit" aria-hidden="true"></i> Edit
                                        </a>

                                        <form action="{{ route('assignments.destroy', $assignment->id) }}" method="POST" class="d-inline ms-2" onsubmit="return confirm('Delete this assignment? This cannot be undone.');">
                                            @csrf
                                            @method('DELETE')
                                            <button type="submit" class="btn btn-sm btn-outline-danger">
                                                <i class="fa fa-trash" aria-hidden="true"></i> Delete
                                            </button>
                                        </form>
                                    </div>
                                @endif --}}
                            </div>
                        </td>
                    </tr>
                @empty
                    <tr><td colspan="7">No assignments found.</td></tr>
                @endforelse
            </tbody>
        </table>
    </div>

    {{ $assignments->links() }}
</div>
@endsection
