@extends('layouts.app')

@section('content')
<div class="container">
    <h1 class="mb-4">Members</h1>

    {{-- Flash --}}
    @if(session('success'))
        <div class="alert alert-success">{{ session('success') }}</div>
    @endif

    {{-- Filters --}}
    <form method="GET" action="{{ route('members.index') }}" class="card p-3 mb-3 shadow-sm border-0">
        <div class="row g-3 align-items-end">
            {{-- Search --}}
            <div class="col-md-3">
                <label class="form-label">Search (name / phone / email / address)</label>
                <input type="text" name="q" value="{{ $q ?? '' }}" class="form-control" placeholder="e.g. John, 097..., john@..., Kabulonga">
            </div>

            {{-- Type --}}
            <div class="col-md-2">
                <label class="form-label">Type</label>
                <select name="type" class="form-select">
                    <option value="">-- All Types --</option>
                    @foreach($types as $t)
                        <option value="{{ $t }}" {{ ($type ?? '') === $t ? 'selected' : '' }}>{{ $t }}</option>
                    @endforeach
                </select>
            </div>

            {{-- Service Unit --}}
            <div class="col-md-2">
                <label class="form-label">Service Unit</label>
                <select name="service_unit_id" class="form-select">
                    <option value="">-- Any --</option>
                    @foreach($serviceUnits as $su)
                        <option value="{{ $su->id }}" {{ (string)($serviceId ?? '') === (string)$su->id ? 'selected' : '' }}>
                            {{ $su->name }}
                        </option>
                    @endforeach
                </select>
            </div>

            {{-- Homecell --}}
            <div class="col-md-2">
                <label class="form-label">Homecell</label>
                <select name="homecell_id" class="form-select">
                    <option value="">-- Any --</option>
                    @foreach($homecells as $hc)
                        <option value="{{ $hc->id }}" {{ (string)($homecellId ?? '') === (string)$hc->id ? 'selected' : '' }}>
                            {{ $hc->name }}
                        </option>
                    @endforeach
                </select>
            </div>

            {{-- Foundation class --}}
            <div class="col-md-2">
                <label class="form-label">Foundation Class</label>
                <select name="foundation" class="form-select">
                    <option value="">-- All --</option>
                    <option value="completed" {{ ($foundation ?? '') === 'completed' ? 'selected' : '' }}>Completed</option>
                    <option value="pending"   {{ ($foundation ?? '') === 'pending'   ? 'selected' : '' }}>Pending</option>
                </select>
            </div>

            {{-- Date range --}}
            <div class="col-md-2">
                <label class="form-label">From</label>
                <input type="date" name="from" value="{{ $from ?? '' }}" class="form-control">
            </div>
            <div class="col-md-2">
                <label class="form-label">To</label>
                <input type="date" name="to" value="{{ $to ?? '' }}" class="form-control">
            </div>

            {{-- Sort --}}
            <div class="col-md-2">
                <label class="form-label">Sort By</label>
                <select name="sort" class="form-select">
                    <option value="created_at" {{ ($sort ?? '') === 'created_at' ? 'selected' : '' }}>Created</option>
                    <option value="full_name"  {{ ($sort ?? '') === 'full_name'  ? 'selected' : '' }}>Name</option>
                    <option value="email"      {{ ($sort ?? '') === 'email'      ? 'selected' : '' }}>Email</option>
                </select>
            </div>
            <div class="col-md-1">
                <label class="form-label">Dir</label>
                <select name="dir" class="form-select">
                    <option value="desc" {{ ($dir ?? '') === 'desc' ? 'selected' : '' }}>Desc</option>
                    <option value="asc"  {{ ($dir ?? '') === 'asc'  ? 'selected' : '' }}>Asc</option>
                </select>
            </div>

            {{-- Actions (smaller buttons) --}}
            <div class="col-md-5 text-end">
                <a href="{{ route('members.index') }}" class="btn btn-outline-secondary btn-sm">Reset</a>
                <button class="btn btn-primary btn-sm">Filter</button>
                <a href="{{ route('members.create') }}" class="btn btn-success btn-sm">+ Add Member</a>
            </div>
        </div>
    </form>

    {{-- Export toolbar (respects filters via query string) --}}
    <div class="d-flex justify-content-end gap-2 mb-3">
        {{-- Excel (keep commented if not ready) --}}
        {{-- <a class="btn btn-outline-secondary btn-sm" href="{{ route('members.export.excel', request()->query()) }}">
            <i class="bi bi-file-earmark-excel"></i> Export Excel
        </a> --}}
        <a class="btn btn-outline-danger btn-sm"
           href="{{ route('members.export.pdf', request()->query()) }}">
            <i class="bi bi-file-earmark-pdf me-1"></i> Export PDF
        </a>
    </div>

    {{-- Table --}}
    <div class="table-responsive">
        <table class="table table-bordered table-striped align-middle">
            <thead class="table-light">
                <tr>
                    <th>Name</th>
                    <th>Type</th>
                    <th>Phone</th>
                    <th>Email</th>
                    <th>Address</th>
                    <th>Service Unit</th>
                    <th>Homecell</th>
                    <th>Foundation Class</th>
                    <th style="width:140px;">Actions</th>
                </tr>
            </thead>
            <tbody>
                @forelse($members as $member)
                    <tr>
                        <td>{{ $member->full_name }}</td>
                        <td>{{ $member->type }}</td>
                        <td>{{ $member->phone ?: '—' }}</td>
                        <td>{{ $member->email ?: '—' }}</td>
                        <td>{{ $member->address ?: '—' }}</td>
                        <td>{{ $member->serviceUnit->name ?? 'N/A' }}</td>
                        <td>{{ $member->homecell->name ?? 'N/A' }}</td>
                        <td>
                            @if($member->foundation_class_completed)
                                <span class="text-success">✅ Completed</span>
                            @else
                                <span class="text-danger">❌ Pending</span>
                            @endif
                        </td>
                        <td>
                            <a href="{{ route('members.edit', $member->id) }}" class="btn btn-sm btn-warning">Edit</a>
                            <form action="{{ route('members.destroy', $member->id) }}" method="POST" class="d-inline">
                                @csrf @method('DELETE')
                                <button onclick="return confirm('Delete this member?')" class="btn btn-sm btn-danger">Delete</button>
                            </form>
                        </td>
                    </tr>
                @empty
                    <tr>
                        <td colspan="9" class="text-center text-muted">No members found.</td>
                    </tr>
                @endforelse
            </tbody>
        </table>
    </div>

    <div class="mt-3">
        {{ $members->links() }}
    </div>
</div>
@endsection
