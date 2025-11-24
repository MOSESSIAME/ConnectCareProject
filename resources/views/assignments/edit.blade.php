{{-- @extends('layouts.app') --}}

{{-- @section('content') --}}
@php
    // $role = auth()->user()->role->name ?? '';
    // $assignment, $teams, $currentTeamMembers are expected from controller
// {{-- @endphp

// <div class="container">
//     <div class="d-flex align-items-center justify-content-between mb-3">
//         <h2 class="mb-0">Edit Assignment</h2>
//         <a href="{{ route('assignments.index') }}" class="btn btn-outline-secondary">Back to assignments</a>
//     </div>

//     @if(session('success'))
//         <div class="alert alert-success">{{ session('success') }}</div>
//     @endif
//     @if(session('error'))
//         <div class="alert alert-danger">{{ session('error') }}</div>
//     @endif
//     @if($errors->any())
//         <div class="alert alert-danger">
//             <ul class="mb-0">
//                 @foreach($errors->all() as $e)
//                     <li>{{ $e }}</li>
//                 @endforeach
//             </ul>
//         </div>
//     @endif

//     <div class="card shadow-sm">
//         <div class="card-body">
//             <form action="{{ route('assignments.update', $assignment->id) }}" method="POST">
//                 @csrf
//                 @method('PUT') --}}

//                 {{-- Member (readonly) --}}
//                 <div class="mb-3">
//                     <label class="form-label">Member</label>
//                     <div class="form-control-plaintext">
//                         <strong>{{ $assignment->member->full_name ?? 'N/A' }}</strong>
//                         @if(optional($assignment->member)->phone)
//                             <div class="small text-muted">{{ $assignment->member->phone }}</div>
//                         @endif
//                     </div>
//                 </div>

//                 {{-- Team (Admin can change, Leader sees fixed) --}}
//                 <div class="mb-3">
//                     <label class="form-label">Team</label>

//                     @if($role === 'Admin')
//                         <select id="team_select" name="team_id" class="form-select" required>
//                             @foreach($teams as $team)
//                                 <option value="{{ $team->id }}"
//                                     {{ $assignment->team_id == $team->id ? 'selected' : '' }}>
//                                     {{ $team->name }}
//                                 </option>
//                             @endforeach
//                         </select>
//                         <div class="form-text">Changing the team will refresh the "Assigned to" options.</div>
//                     @else
//                         {{-- Team Leader - show the team name readonly --}}
//                         <input type="text" class="form-control" value="{{ $assignment->team->name ?? '—' }}" readonly>
//                         <input type="hidden" name="team_id" value="{{ $assignment->team_id }}">
//                     @endif
//                 </div>

//                 {{-- Assigned To --}}
//                 <div class="mb-3">
//                     <label class="form-label">Assigned To</label>
//                     <select id="assigned_to_select" name="assigned_to" class="form-select">
//                         <option value="">-- unassigned --</option>

//                         {{-- If currentTeamMembers provided, show them; otherwise use $assignees if controller filled --}}
//                         @foreach(($currentTeamMembers ?? collect()) as $u)
//                             <option value="{{ $u->id }}" {{ $assignment->assigned_to == $u->id ? 'selected' : '' }}>
//                                 {{ $u->name }}
//                             </option>
//                         @endforeach
//                     </select>
//                     <div class="form-text">Pick a team member to assign (optional).</div>
//                 </div>

//                 {{-- Status --}}
//                 <div class="mb-3">
//                     <label class="form-label">Status</label>
//                     <select name="status" class="form-select" required>
//                         <option value="Active" {{ $assignment->status === 'Active' ? 'selected' : '' }}>Active</option>
//                         <option value="Reassigned" {{ $assignment->status === 'Reassigned' ? 'selected' : '' }}>Reassigned</option>
//                         <option value="Completed" {{ $assignment->status === 'Completed' ? 'selected' : '' }}>Completed</option>
//                     </select>
//                 </div>

//                 <div class="d-flex gap-2">
//                     <button class="btn btn-primary" type="submit">
//                         <i class="fa fa-save" aria-hidden="true"></i> Update
//                     </button>

//                     {{-- Delete (soft-delete) --}}
//                     <form action="{{ route('assignments.destroy', $assignment->id) }}" method="POST" onsubmit="return confirm('Delete this assignment?');" class="ms-2">
//                         @csrf
//                         @method('DELETE')
//                         <button class="btn btn-danger" type="submit">
//                             <i class="fa fa-trash" aria-hidden="true"></i> Delete
//                         </button>
//                     </form>

//                     <a href="{{ route('followups.assignment', $assignment->id) }}" class="btn btn-outline-secondary ms-auto">
//                         View follow-ups
//                     </a>
//                 </div>
//             </form>
//         </div>
//     </div>
// </div>

// {{-- Inline script to update assigned_to when Admin changes team --}}
// @push('scripts')
// <script>
// document.addEventListener('DOMContentLoaded', function () {
    // Teams with members data (server-provided). We build a map: teamId -> members array
    // The controller can pass a $teams collection (Team with members relation) which we'll use.
    // const teams = @json($teams ?? []);
    // const assignedSelect = document.getElementById('assigned_to_select');
    // const teamSelect = document.getElementById('team_select');

    // function populateAssignedForTeam(teamId, preserveSelected = true) {
        // find team in teams
        // const team = teams.find(t => String(t.id) === String(teamId));
        // save current selected
        // const cur = assignedSelect.value;
        // clear
    //     assignedSelect.innerHTML = '';
    //     const emptyOpt = document.createElement('option');
    //     emptyOpt.value = '';
    //     emptyOpt.textContent = '-- unassigned --';
    //     assignedSelect.appendChild(emptyOpt);

    //     if (!team || !Array.isArray(team.members)) {
    //         return;
    //     }

    //     team.members.forEach(m => {
    //         const opt = document.createElement('option');
    //         opt.value = m.id;
    //         opt.textContent = m.name;
    //         if (preserveSelected && String(cur) === String(m.id)) {
    //             opt.selected = true;
    //         }
    //         assignedSelect.appendChild(opt);
    //     });
    // }

    // if (teamSelect) {
    //     teamSelect.addEventListener('change', function () {
    //         populateAssignedForTeam(this.value, false);
    //     });
    // }

    // initial population (in case teams variable has different members than currentTeamMembers)
//     @if(isset($assignment->team_id))
//         (function(){ populateAssignedForTeam("{{ $assignment->team_id }}", true); })();
//     @endif
// });
// </script>
// @endpush

// @endsection
