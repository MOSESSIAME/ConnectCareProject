@extends('layouts.app')

@section('content')
<div class="container py-4">
    <h2 class="fw-bold mb-3">Manage Users</h2>

    {{-- Flash messages --}}
    @if(session('success'))
        <div class="alert alert-success">{{ session('success') }}</div>
    @endif
    @if(session('error'))
        <div class="alert alert-danger">{{ session('error') }}</div>
    @endif

    {{-- Filters --}}
    <form method="GET" action="{{ route('admin.users.index') }}" class="card shadow-sm border-0 mb-3">
        <div class="card-body">
            <div class="row g-3 align-items-end">
                {{-- Search --}}
                <div class="col-md-4">
                    <label class="form-label">Search</label>
                    <input type="text" name="q" value="{{ $q ?? '' }}" class="form-control"
                           placeholder="Search name or email…">
                </div>

                {{-- Role --}}
                <div class="col-md-3">
                    <label class="form-label">Role</label>
                    <select name="role" class="form-select">
                        <option value="">-- Any role --</option>
                        @foreach($roles as $r)
                            <option value="{{ $r }}" {{ ($role ?? '') === $r ? 'selected' : '' }}>
                                {{ $r }}
                            </option>
                        @endforeach
                    </select>
                </div>

                {{-- Date range --}}
                <div class="col-md-2">
                    <label class="form-label">Created from</label>
                    <input type="date" name="from" value="{{ $from ?? '' }}" class="form-control">
                </div>
                <div class="col-md-2">
                    <label class="form-label">to</label>
                    <input type="date" name="to" value="{{ $to ?? '' }}" class="form-control">
                </div>

                {{-- Sort --}}
                <div class="col-md-1 d-none d-md-block"></div>
                <div class="col-md-3">
                    <label class="form-label">Sort by</label>
                    <div class="input-group">
                        <select name="sort" class="form-select">
                            <option value="created_at" @selected(($sort ?? '')==='created_at')>Created</option>
                            <option value="name"       @selected(($sort ?? '')==='name')>Name</option>
                            <option value="email"      @selected(($sort ?? '')==='email')>Email</option>
                        </select>
                        <select name="dir" class="form-select" style="max-width: 120px;">
                            <option value="desc" @selected(($dir ?? '')==='desc')>Desc</option>
                            <option value="asc"  @selected(($dir ?? '')==='asc')>Asc</option>
                        </select>
                    </div>
                </div>

                <div class="col-md-3 text-end ms-auto">
                    <button class="btn btn-primary me-2"><i class="bi bi-funnel"></i> Filter</button>
                    <a href="{{ route('admin.users.index') }}" class="btn btn-outline-secondary">Reset</a>
                </div>
            </div>
        </div>
    </form>

    <div class="d-flex justify-content-end mb-2">
        <a href="{{ route('admin.users.create') }}" class="btn btn-primary">
            + Add New User
        </a>
    </div>

    {{-- Users table --}}
    <div class="table-responsive">
        <table class="table table-striped table-bordered align-middle bg-white">
            <thead class="table-dark">
                <tr>
                    <th style="width: 60px;">#</th>
                    <th>Name</th>
                    <th>Email</th>
                    <th style="width:160px;">Role</th>
                    <th style="width:160px;">Created</th>
                    <th style="width:220px;">Actions</th>
                </tr>
            </thead>
            <tbody>
                @forelse($users as $idx => $user)
                    <tr>
                        <td>{{ ($users->firstItem() ?? 1) + $idx }}</td>
                        <td>{{ $user->name }}</td>
                        <td>{{ $user->email }}</td>
                        <td>
                            <span class="badge text-bg-secondary">
                                {{ $user->role->name ?? '—' }}
                            </span>
                        </td>
                        <td>{{ $user->created_at?->format('d M Y') }}</td>
                        <td>
                            <a href="{{ route('admin.users.edit', $user) }}" class="btn btn-sm btn-warning">Edit</a>

                            <form action="{{ route('admin.users.destroy', $user) }}"
                                  method="POST" class="d-inline"
                                  onsubmit="return confirm('Delete this user?')">
                                @csrf @method('DELETE')
                                <button class="btn btn-sm btn-danger">Delete</button>
                            </form>

                            <form action="{{ route('admin.users.reset-password', $user) }}"
                                  method="POST" class="d-inline"
                                  onsubmit="return confirm('Reset password for this user?')">
                                @csrf
                                <button class="btn btn-sm btn-info text-white">Reset</button>
                            </form>
                        </td>
                    </tr>
                @empty
                    <tr>
                        <td colspan="6" class="text-center text-muted">No users found.</td>
                    </tr>
                @endforelse
            </tbody>
        </table>
    </div>

    <div class="mt-3">
        {{ $users->onEachSide(1)->links() }}
    </div>
</div>
@endsection

@push('styles')
<link href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.11.1/font/bootstrap-icons.css" rel="stylesheet">
@endpush
