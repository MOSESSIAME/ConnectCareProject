@extends('layouts.app')

@section('content')
@php
    // Prepare team + member payload only for Admin mode
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
    <h2 class="fw-bold mb-3">
        {{ $mode === 'admin' ? 'Standby Pool (First-Timers & New Converts)' : 'Team Backlog (Unassigned in My Team)' }}
    </h2>

    {{-- Alerts --}}
    @if(session('success'))
        <div class="alert alert-success">{{ session('success') }}</div>
    @endif
    @if($errors->any())
        <div class="alert alert-danger">
            <ul class="mb-0">
                @foreach($errors->all() as $e)<li>{{ $e }}</li>@endforeach
            </ul>
        </div>
    @endif

    {{-- ADMIN VIEW --}}
    @if($mode === 'admin')
        <form method="GET" class="card shadow-sm border-0 mb-3">
            <div class="card-body row g-2">
                <div class="col-sm-6 col-lg-4">
                    <input type="text" name="q" value="{{ request('q') }}" class="form-control" placeholder="Search name or phone">
                </div>
                <div class="col-sm-3 col-lg-2">
                    <button class="btn btn-outline-primary w-100">Search</button>
                </div>
                <div class="col-sm-3 col-lg-2">
                    <a href="{{ route('assignments.standby') }}" class="btn btn-outline-secondary w-100">Reset</a>
                </div>
            </div>
        </form>

        <div class="table-responsive">
            <table class="table table-bordered align-middle">
                <thead class="table-light">
                    <tr>
                        <th>Member</th>
                        <th>Phone</th>
                        <th style="width: 440px;">Assign</th>
                    </tr>
                </thead>
                <tbody>
                    @forelse($members as $m)
                        <tr>
                            <td>{{ $m->full_name }}</td>
                            <td>{{ $m->phone }}</td>
                            <td class="align-middle">
                                <form action="{{ route('assignments.store') }}"
                                      method="POST"
                                      class="d-flex align-items-center gap-2 flex-wrap mb-0">
                                    @csrf
                                    <input type="hidden" name="member_id" value="{{ $m->id }}">

                                    <select name="team_id"
                                            class="form-select form-select-sm team-select"
                                            required
                                            style="min-width: 160px;"
                                            data-members='@json($teamsPayload)'>
                                        <option value="" selected disabled>Team</option>
                                        @foreach($teams as $t)
                                            <option value="{{ $t->id }}">{{ $t->name }}</option>
                                        @endforeach
                                    </select>

                                    <select name="assigned_to"
                                            class="form-select form-select-sm member-select"
                                            style="min-width: 160px;"
                                            disabled>
                                        <option value="">Member</option>
                                    </select>

                                    <button class="btn btn-sm btn-primary">Assign</button>
                                </form>
                            </td>
                        </tr>
                    @empty
                        <tr><td colspan="3">No standby members found.</td></tr>
                    @endforelse
                </tbody>
            </table>
        </div>

        <div class="mt-3">
            {{ $members->links() }}
        </div>

    {{-- TEAM LEADER VIEW --}}
    @else
        <div class="table-responsive">
            <table class="table table-bordered align-middle">
                <thead class="table-light">
                    <tr>
                        <th>Member</th>
                        <th>Created</th>
                        <th style="width: 400px;">Assign to Team Member</th>
                    </tr>
                </thead>
                <tbody>
                    @forelse($backlog as $a)
                        <tr>
                            <td>
                                {{ $a->member->full_name ?? 'N/A' }}
                                @if(optional($a->member)->phone)
                                    <div class="text-muted small">{{ $a->member->phone }}</div>
                                @endif
                            </td>
                            <td>{{ optional($a->created_at)->format('d M Y') }}</td>
                            <td>
                                <form action="{{ route('assignments.assignToMember', $a->id) }}"
                                      method="POST"
                                      class="d-flex align-items-center gap-2 flex-wrap mb-0">
                                    @csrf
                                    <select name="assigned_to"
                                            class="form-select form-select-sm"
                                            style="min-width: 200px;"
                                            required>
                                        <option value="" disabled selected>Member</option>
                                        @foreach($teamMembers as $u)
                                            <option value="{{ $u->id }}">{{ $u->name }}</option>
                                        @endforeach
                                    </select>
                                    <button class="btn btn-sm btn-primary">Assign</button>
                                </form>
                            </td>
                        </tr>
                    @empty
                        <tr><td colspan="3">No unassigned items in your team.</td></tr>
                    @endforelse
                </tbody>
            </table>
        </div>

        <div class="mt-3">
            {{ $backlog->links() }}
        </div>
    @endif
</div>
@endsection

@push('styles')
<style>
.table td, .table th {
    vertical-align: middle !important;
}
</style>
@endpush

@push('scripts')
<script>
/** Team → members dropdown dependency (Admin view only) */
document.querySelectorAll('.team-select').forEach(function(teamSelect){
    teamSelect.addEventListener('change', function(){
        const wrapper    = teamSelect.closest('form');
        const memberSel  = wrapper.querySelector('.member-select');
        const data       = JSON.parse(teamSelect.getAttribute('data-members') || '[]');
        const teamId     = teamSelect.value;

        memberSel.innerHTML = '<option value="">Member</option>';
        memberSel.disabled = true;

        const team = data.find(t => String(t.id) === String(teamId));
        if (team && team.members && team.members.length) {
            team.members.forEach(m => {
                const opt = document.createElement('option');
                opt.value = m.id;
                opt.textContent = m.name;
                memberSel.appendChild(opt);
            });
            memberSel.disabled = false;
        }
    });
});
</script>
@endpush
