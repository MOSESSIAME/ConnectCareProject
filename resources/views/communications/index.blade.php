{{-- uploaded file: /mnt/data/35621db3-4dc0-4a15-92ff-769f15b25854.png --}}

@extends('layouts.app')

@section('content')
<div class="container py-4">
    <div class="d-flex justify-content-between align-items-center mb-3">
        <h3 class="fw-bold text-primary mb-0">
            <i class="bi bi-inbox me-2"></i> Communications
        </h3>
        <a href="{{ route('communications.create') }}" class="btn btn-primary">
            <i class="bi bi-send me-1"></i> Compose
        </a>
    </div>

    @if(session('success'))
        <div class="alert alert-success">{{ session('success') }}</div>
    @endif

    {{-- Filters --}}
    <form method="GET" action="{{ route('communications.index') }}" class="card mb-3 shadow-sm border-0">
        <div class="card-body">
            <div class="row g-2">
                {{-- Audience --}}
                <div class="col-12 col-md-3">
                    <label class="form-label mb-1">Audience</label>
                    @if(isset($audiences) && count($audiences))
                        <select name="audience" class="form-select">
                            <option value="">All</option>
                            @foreach($audiences as $a)
                                <option value="{{ $a }}" @selected(request('audience') === (string)$a)>{{ str_replace('_',' ', ucfirst($a)) }}</option>
                            @endforeach
                        </select>
                    @else
                        {{-- fallback audiences if controller didn't supply --}}
                        <select name="audience" class="form-select">
                            <option value="">All</option>
                            <option value="all" @selected(request('audience')==='all')>All</option>
                            <option value="members" @selected(request('audience')==='members')>Members</option>
                            <option value="teams" @selected(request('audience')==='teams')>Teams</option>
                            <option value="admins" @selected(request('audience')==='admins')>Admins</option>
                        </select>
                    @endif
                </div>

                {{-- Date range (Scheduled) --}}
                <div class="col-6 col-md-3">
                    <label class="form-label mb-1">Scheduled from</label>
                    <input type="date" name="from" value="{{ request('from') }}" class="form-control">
                </div>
                <div class="col-6 col-md-3">
                    <label class="form-label mb-1">to</label>
                    <input type="date" name="to" value="{{ request('to') }}" class="form-control">
                </div>

                {{-- Created by --}}
                <div class="col-12 col-md-3">
                    <label class="form-label mb-1">Created by</label>
                    @if(isset($creators) && count($creators))
                        <select name="creator_id" class="form-select">
                            <option value="">Any</option>
                            @foreach($creators as $u)
                                <option value="{{ $u->id }}" @selected((string)request('creator_id') === (string)$u->id)>{{ $u->name }}</option>
                            @endforeach
                        </select>
                    @elseif(isset($users) && count($users))
                        <select name="creator_id" class="form-select">
                            <option value="">Any</option>
                            @foreach($users as $u)
                                <option value="{{ $u->id }}" @selected((string)request('creator_id') === (string)$u->id)>{{ $u->name }}</option>
                            @endforeach
                        </select>
                    @else
                        <select name="creator_id" class="form-select">
                            <option value="">Any</option>
                        </select>
                    @endif
                </div>
            </div>

            <div class="mt-3 d-flex gap-2">
                <button type="submit" class="btn btn-outline-primary">Filter</button>
                <a href="{{ route('communications.index') }}" class="btn btn-outline-secondary">Reset</a>
            </div>
        </div>
    </form>

    <div class="card shadow-sm border-0">
        <div class="card-body p-0">
            @if($records->count())
                <div class="table-responsive">
                    <table class="table table-striped mb-0 align-middle">
                        <thead class="table-dark">
                            <tr>
                                <th>#</th>
                                <th>Title</th>
                                <th>Channel</th>
                                <th>Audience</th>
                                <th>Status</th>
                                <th>Scheduled</th>
                                <th>Created By</th>
                                <th>Created</th>
                            </tr>
                        </thead>
                        <tbody>
                            @foreach($records as $i => $c)
                                <tr>
                                    <td>{{ $records->firstItem() + $i }}</td>
                                    <td>{{ $c->title }}</td>
                                    <td class="text-uppercase">{{ $c->channel }}</td>
                                    <td>{{ str_replace('_',' ', $c->audience) }}</td>
                                    <td>
                                        <span class="badge {{ $c->status === 'sent' ? 'bg-success' : 'bg-secondary' }}">
                                            {{ ucfirst($c->status) }}
                                        </span>
                                    </td>
                                    <td>{{ optional($c->scheduled_at)->format('d M Y, H:i') ?? '-' }}</td>
                                    <td>{{ $c->creator->name ?? '—' }}</td>
                                    <td>{{ $c->created_at->format('d M Y, H:i') }}</td>
                                </tr>
                            @endforeach
                        </tbody>
                    </table>
                </div>

                <div class="p-3">
                    {{-- preserve query string on pagination --}}
                    {{ $records->appends(request()->query())->links() }}
                </div>
            @else
                <div class="p-4 text-center text-muted">No messages yet.</div>
            @endif
        </div>
    </div>
</div>
@endsection
