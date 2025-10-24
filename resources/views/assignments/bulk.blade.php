@extends('layouts.app')

@section('content')
@php
    // Build a simple teams → members payload for the data attribute.
    $teamsPayload = $teams->map(function ($t) {
        return [
            'id'      => $t->id,
            'name'    => $t->name,
            'members' => $t->members
                ->map(fn ($u) => ['id' => $u->id, 'name' => $u->name])
                ->values(),
        ];
    })->values();
@endphp

<div class="container py-3">
    <h2 class="fw-bold mb-3">Bulk Assign Standby Members</h2>

    @if(session('success'))
        <div class="alert alert-success">{{ session('success') }}</div>
    @endif
    @if($errors->any())
        <div class="alert alert-danger">
            <ul class="mb-0">@foreach($errors->all() as $e)<li>{{ $e }}</li>@endforeach</ul>
        </div>
    @endif

    <form action="{{ route('assignments.bulkAssign.post') }}" method="POST" class="card shadow-sm border-0 mb-3">
        @csrf
        <div class="card-body">
            <div class="row g-3">
                {{-- Left: list of standby members with checkboxes --}}
                <div class="col-lg-7">
                    <div class="border rounded p-3" style="max-height: 520px; overflow: auto;">
                        <div class="d-flex justify-content-between align-items-center mb-2">
                            <h6 class="mb-0">Standby Members</h6>
                            <button type="button" class="btn btn-sm btn-outline-secondary" id="checkAll">Toggle All</button>
                        </div>

                        @forelse($members as $m)
                            <div class="form-check border-bottom py-2">
                                <input class="form-check-input" type="checkbox" name="member_ids[]"
                                       value="{{ $m->id }}" id="m{{ $m->id }}">
                                <label class="form-check-label" for="m{{ $m->id }}">
                                    <strong>{{ $m->full_name }}</strong>
                                    @if($m->phone)<span class="text-muted"> — {{ $m->phone }}</span>@endif
                                </label>
                            </div>
                        @empty
                            <div class="text-muted">No standby members found.</div>
                        @endforelse
                    </div>
                </div>

                {{-- Right: target team + optional user --}}
                <div class="col-lg-5">
                    <div class="border rounded p-3">
                        <h6 class="mb-2">Target</h6>

                        <div class="mb-3">
                            <label class="form-label">Team <span class="text-danger">*</span></label>
                            <select name="team_id"
                                    class="form-select bulk-team"
                                    required
                                    data-members='@json($teamsPayload)'>
                                <option value="" selected disabled>-- choose team --</option>
                                @foreach($teams as $t)
                                    <option value="{{ $t->id }}">{{ $t->name }}</option>
                                @endforeach
                            </select>
                        </div>

                        <div class="mb-3">
                            <label class="form-label">Assign To (optional)</label>
                            <select name="assigned_to" class="form-select bulk-user" disabled>
                                <option value="">(optional) choose member</option>
                            </select>
                        </div>

                        <button class="btn btn-primary w-100">Bulk Assign</button>
                    </div>
                </div>
            </div>
        </div>
    </form>
</div>
@endsection

@push('scripts')
<script>
document.getElementById('checkAll')?.addEventListener('click', function(){
    document.querySelectorAll('input[name="member_ids[]"]').forEach(cb => cb.checked = !cb.checked);
});

const teamSel = document.querySelector('.bulk-team');
const userSel = document.querySelector('.bulk-user');

teamSel?.addEventListener('change', function(){
    const data   = JSON.parse(teamSel.getAttribute('data-members') || '[]');
    const teamId = teamSel.value;

    userSel.innerHTML = '<option value="">(optional) choose member</option>';
    userSel.disabled = true;

    const team = data.find(t => String(t.id) === String(teamId));
    if (team && team.members && team.members.length) {
        team.members.forEach(m => {
            const opt = document.createElement('option');
            opt.value = m.id;
            opt.textContent = m.name;
            userSel.appendChild(opt);
        });
        userSel.disabled = false;
    }
});
</script>
@endpush
