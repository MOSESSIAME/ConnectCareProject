@extends('layouts.app')

@section('content')
@php
    // For Admin mode, prepare a clean JSON payload: teams → members
    $teamsPayload = null;
    if (($mode ?? '') === 'admin' && isset($teams)) {
        $teamsPayload = $teams->map(function ($t) {
            return [
                'id'      => $t->id,
                'name'    => $t->name,
                'members' => $t->members
                    ->map(fn ($u) => ['id' => $u->id, 'name' => $u->name])
                    ->values(),
            ];
        })->values();
    }
@endphp

<div class="container py-3">
    <h2 class="fw-bold mb-3">{{ $mode === 'admin' ? 'Reassign (Cross-team allowed)' : 'Reassign Within My Team' }}</h2>

    @if(session('success'))
        <div class="alert alert-success">{{ session('success') }}</div>
    @endif
    @if($errors->any())
        <div class="alert alert-danger">
            <ul class="mb-0">@foreach($errors->all() as $e)<li>{{ $e }}</li>@endforeach</ul>
        </div>
    @endif

    <div class="table-responsive">
        <table class="table table-bordered align-middle">
            <thead class="table-light">
                <tr>
                    <th>Member</th>
                    <th>Current Team</th>
                    <th>Current Assignee</th>
                    <th style="width: 520px;">Reassign</th>
                </tr>
            </thead>
            <tbody>
            @forelse($assignments as $a)
                <tr>
                    <td>
                        {{ $a->member->full_name ?? 'N/A' }}
                        @if(optional($a->member)->phone)
                            <div class="text-muted small">{{ $a->member->phone }}</div>
                        @endif
                    </td>
                    <td>{{ $a->team->name ?? '—' }}</td>
                    <td>{{ $a->assignedTo->name ?? '—' }}</td>
                    <td>
                        <form action="{{ route('assignments.reassign.post') }}" method="POST" class="row g-2 align-items-center">
                            @csrf
                            <input type="hidden" name="assignment_id" value="{{ $a->id }}">

                            @if($mode === 'admin')
                                <div class="col-md-5">
                                    <select name="team_id"
                                            class="form-select admin-team"
                                            required
                                            data-members='@json($teamsPayload)'>
                                        @foreach($teams as $t)
                                            <option value="{{ $t->id }}" @selected($a->team_id == $t->id)>{{ $t->name }}</option>
                                        @endforeach
                                    </select>
                                </div>
                                <div class="col-md-5">
                                    <select name="assigned_to" class="form-select admin-user">
                                        {{-- Initial list = members of current team --}}
                                        <option value="">(optional) choose user</option>
                                        @if($a->team)
                                            @foreach($a->team->members ?? [] as $u)
                                                <option value="{{ $u->id }}" @selected($a->assigned_to == $u->id)>{{ $u->name }}</option>
                                            @endforeach
                                        @endif
                                    </select>
                                </div>
                                <div class="col-md-2">
                                    <button class="btn btn-primary w-100">Reassign</button>
                                </div>
                            @else
                                <div class="col-md-8">
                                    <select name="assigned_to" class="form-select" required>
                                        <option value="" disabled selected>-- choose member --</option>
                                        @foreach($teamMembers as $u)
                                            <option value="{{ $u->id }}" @selected($a->assigned_to == $u->id)>{{ $u->name }}</option>
                                        @endforeach
                                    </select>
                                </div>
                                <div class="col-md-4">
                                    <button class="btn btn-primary w-100">Reassign</button>
                                </div>
                            @endif
                        </form>
                    </td>
                </tr>
            @empty
                <tr><td colspan="4">No assignments available for reassignment.</td></tr>
            @endforelse
            </tbody>
        </table>
    </div>

    <div class="mt-3">
        {{ $assignments->links() }}
    </div>
</div>
@endsection

@push('scripts')
@if($mode === 'admin')
<script>
/**
 * Admin: when team changes, rebuild the user list from the data-members payload.
 */
document.querySelectorAll('.admin-team').forEach(function(sel){
    sel.addEventListener('change', function(){
        const form     = sel.closest('form');
        const userSel  = form.querySelector('.admin-user');
        const payload  = JSON.parse(sel.getAttribute('data-members') || '[]');
        const teamId   = sel.value;

        userSel.innerHTML = '<option value="">(optional) choose user</option>';

        const team = payload.find(t => String(t.id) === String(teamId));
        if (team && Array.isArray(team.members)) {
            team.members.forEach(u => {
                const opt = document.createElement('option');
                opt.value = u.id;
                opt.textContent = u.name;
                userSel.appendChild(opt);
            });
        }
    });
});
</script>
@endif
@endpush
