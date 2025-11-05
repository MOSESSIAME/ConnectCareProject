{{-- resources/views/admin/teams/members.blade.php --}}
@extends('layouts.app')

@section('content')
<div class="container">
    <h2 class="fw-bold mb-3">Manage Members — {{ $team->name }}</h2>

    <a href="{{ route('admin.teams.index') }}" class="btn btn-secondary mb-3">
        <i class="bi bi-arrow-left"></i> Back
    </a>

    @if (session('success'))
        <div class="alert alert-success">{{ session('success') }}</div>
    @endif
    @if ($errors->any())
        <div class="alert alert-danger mb-3">
            <ul class="mb-0">
                @foreach ($errors->all() as $e) <li>{{ $e }}</li> @endforeach
            </ul>
        </div>
    @endif

    <div class="card shadow-sm mb-4">
        <div class="card-header fw-semibold">Add a user to this team</div>
        <div class="card-body">
            <form action="{{ route('admin.teams.members.store', $team) }}" method="POST" class="row g-2">
                @csrf
                <div class="col-md-6">
                    <select name="user_id" class="form-select" required>
                        <option value="">-- Choose user --</option>
                        @foreach($candidates as $u)
                            @php $isMember = $team->members->contains('id', $u->id); @endphp
                            @if(!$isMember)
                                <option value="{{ $u->id }}">
                                    {{ $u->name }}{{ $u->role ? ' — '.$u->role->name : '' }}
                                </option>
                            @endif
                        @endforeach
                    </select>
                </div>
                <div class="col-md-3">
                    <button class="btn btn-primary w-100">
                        <i class="bi bi-person-plus me-1"></i> Add to Team
                    </button>
                </div>
            </form>
        </div>
    </div>

    <div class="card shadow-sm">
        <div class="card-header fw-semibold">Current team members</div>
        <div class="card-body p-0">
            <div class="table-responsive">
                <table class="table table-striped mb-0 align-middle">
                    <thead>
                        <tr>
                            <th>#</th>
                            <th>Name / Email</th>
                            <th>Role</th>
                            <th style="width:140px">Actions</th>
                        </tr>
                    </thead>
                    <tbody>
                        @forelse($members as $i => $m)
                            <tr>
                                <td>{{ $i+1 }}</td>
                                <td>
                                    <div class="fw-semibold">{{ $m->name }}</div>
                                    <div class="text-muted small">{{ $m->email }}</div>
                                </td>
                                <td>{{ $m->role->name ?? '—' }}</td>
                                <td>
                                    <form method="POST" action="{{ route('admin.teams.members.destroy', [$team, $m]) }}"
                                          onsubmit="return confirm('Remove this member from the team?')">
                                        @csrf @method('DELETE')
                                        <button class="btn btn-sm btn-outline-danger">
                                            <i class="bi bi-person-dash"></i> Remove
                                        </button>
                                    </form>
                                </td>
                            </tr>
                        @empty
                            <tr><td colspan="4" class="text-center text-muted p-4">No members yet.</td></tr>
                        @endforelse
                    </tbody>
                </table>
            </div>
        </div>
    </div>
</div>
@endsection
