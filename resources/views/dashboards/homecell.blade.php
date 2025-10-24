@extends('layouts.app')

@section('content')
<div class="container">
    <h2 class="mb-4">Homecell Leader Dashboard</h2>

    @if(!empty($notAssigned) && $notAssigned)
        <div class="alert alert-warning">Your account isn’t attached to a homecell yet. Ask Admin to set <code>homecell_id</code> or make you a leader of a homecell.</div>
    @endif

    <div class="row g-3 mb-4">
        <div class="col-md-3"><div class="card text-white bg-primary"><div class="card-body"><h6>Males</h6><h3>{{ $totals['males'] }}</h3></div></div></div>
        <div class="col-md-3"><div class="card text-white bg-danger"><div class="card-body"><h6>Females</h6><h3>{{ $totals['females'] }}</h3></div></div></div>
        <div class="col-md-3"><div class="card text-white bg-success"><div class="card-body"><h6>First-timers</h6><h3>{{ $totals['first_timers'] }}</h3></div></div></div>
        <div class="col-md-3"><div class="card text-white bg-info"><div class="card-body"><h6>New Converts</h6><h3>{{ $totals['new_converts'] }}</h3></div></div></div>
    </div>

    <div class="row g-3">
        <div class="col-md-6">
            <div class="card"><div class="card-header">Recent Homecell Reports</div>
                <div class="card-body">
                    <ul class="list-group">
                        @forelse($recentReports as $r)
                            <li class="list-group-item">M: {{ $r->males }}, F: {{ $r->females }} — {{ $r->created_at->format('d M Y') }}</li>
                        @empty
                            <li class="list-group-item">No reports yet.</li>
                        @endforelse
                    </ul>
                </div>
            </div>
        </div>

        <div class="col-md-6">
            <div class="card"><div class="card-header">Members (latest)</div>
                <div class="card-body">
                    <ul class="list-group">
                        @forelse($members as $m)
                            <li class="list-group-item d-flex justify-content-between">
                                {{ $m->full_name }}
                                <span class="text-muted">{{ $m->type }}</span>
                            </li>
                        @empty
                            <li class="list-group-item">No members found.</li>
                        @endforelse
                    </ul>
                </div>
            </div>
        </div>
    </div>
</div>
@endsection
