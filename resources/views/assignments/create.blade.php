@extends('layouts.app')

@section('content')
<div class="container py-4">
    <div class="row justify-content-center">
        <div class="col-md-8">

            {{-- ✅ Success or Error messages --}}
            @if(session('success'))
                <div class="alert alert-success">{{ session('success') }}</div>
            @endif

            @if($errors->any())
                <div class="alert alert-danger">
                    <ul class="mb-0">
                        @foreach($errors->all() as $error)
                            <li>{{ $error }}</li>
                        @endforeach
                    </ul>
                </div>
            @endif

            {{-- ✅ Assignment Card --}}
            <div class="card shadow-sm border-0 rounded-4">
                <div class="card-header bg-primary text-white fw-semibold">
                    <i class="bi bi-people-fill me-2"></i> Assign Member to Team (and optionally to a Team Member)
                </div>

                <div class="card-body">
                    <form action="{{ route('assignments.store') }}" method="POST">
                        @csrf {{-- ✅ Prevents 419 Page Expired --}}

                        {{-- Member (standby) --}}
                        <div class="mb-3 text-start">
                            <label for="member_id" class="form-label fw-semibold">Select Member (Standby)</label>
                            <select name="member_id" id="member_id" class="form-select" required>
                                <option value="" selected disabled>-- Choose Member --</option>
                                @foreach($members as $member)
                                    <option value="{{ $member->id }}">
                                        {{ $member->full_name }}
                                        @if($member->phone) ({{ $member->phone }}) @endif
                                    </option>
                                @endforeach
                            </select>
                            <div class="form-text">Showing first-timers & new converts without an active assignment.</div>
                        </div>

                        {{-- Team --}}
                        <div class="mb-3 text-start">
                            <label for="team_id" class="form-label fw-semibold">Select Team</label>
                            <select name="team_id" id="team_id" class="form-select" required>
                                <option value="" selected disabled>-- Choose Team --</option>
                                @foreach($teams as $team)
                                    <option value="{{ $team->id }}">{{ $team->name }}</option>
                                @endforeach
                            </select>
                        </div>

                        {{-- Team Member (optional, populated after team is chosen) --}}
                        <div class="mb-3 text-start">
                            <label for="assigned_to" class="form-label fw-semibold">
                                Assign To Team Member <span class="text-muted fw-normal">(optional)</span>
                            </label>
                            <select name="assigned_to" id="assigned_to" class="form-select">
                                <option value="" selected>-- Select a team first --</option>
                            </select>
                            <div class="form-text">Leave blank to let the Team Leader assign later.</div>
                        </div>

                        {{-- Status (optional; defaults to Active) --}}
                        <div class="mb-3 text-start">
                            <label for="status" class="form-label fw-semibold">Status</label>
                            <select name="status" id="status" class="form-select">
                                <option value="Active" selected>Active</option>
                                <option value="Reassigned">Reassigned</option>
                                <option value="Completed">Completed</option>
                            </select>
                        </div>

                        {{-- Submit --}}
                        <div class="text-end">
                            <button type="submit" class="btn btn-primary px-4">
                                <i class="bi bi-save me-2"></i> Create Assignment
                            </button>
                            <a href="{{ route('assignments.index') }}" class="btn btn-secondary px-4">
                                Cancel
                            </a>
                        </div>
                    </form>
                </div>
            </div>

        </div>
    </div>
</div>
@endsection

@push('styles')
<link href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.11.1/font/bootstrap-icons.css" rel="stylesheet">
@endpush

@push('scripts')
<script>
document.getElementById('team_id').addEventListener('change', async (e) => {
    const teamId = e.target.value;
    const memberSelect = document.getElementById('assigned_to');

    memberSelect.innerHTML = '<option value="">Loading team members...</option>';

    if (!teamId) {
        memberSelect.innerHTML = '<option value="">-- Select a team first --</option>';
        return;
    }

    try {
        const res = await fetch(`/get-team-members/${teamId}`, { headers: { 'X-Requested-With': 'XMLHttpRequest' } });
        if (!res.ok) throw new Error('Network response was not ok');
        const data = await res.json();

        // Rebuild options
        let options = '<option value="">-- (optional) Choose Team Member --</option>';
        data.forEach(u => {
            options += `<option value="${u.id}">${u.name}</option>`;
        });
        memberSelect.innerHTML = options;
    } catch (err) {
        memberSelect.innerHTML = '<option value="">(Failed to load team members)</option>';
        console.error(err);
    }
});
</script>
@endpush
