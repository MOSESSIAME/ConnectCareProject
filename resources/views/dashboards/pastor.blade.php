@extends('layouts.app')

@section('content')
<div class="container">
    <h2 class="mb-4">Pastor Dashboard</h2>

    <div class="row g-3 mb-4">
        <div class="col-md-2"><div class="card text-white bg-primary"><div class="card-body"><h6>Males</h6><h3>{{ $totals['males'] }}</h3></div></div></div>
        <div class="col-md-2"><div class="card text-white bg-danger"><div class="card-body"><h6>Females</h6><h3>{{ $totals['females'] }}</h3></div></div></div>
        <div class="col-md-3"><div class="card text-white bg-success"><div class="card-body"><h6>First-timers</h6><h3>{{ $totals['first_timers'] }}</h3></div></div></div>
        <div class="col-md-3"><div class="card text-white bg-info"><div class="card-body"><h6>New Converts</h6><h3>{{ $totals['new_converts'] }}</h3></div></div></div>
        <div class="col-md-2"><div class="card text-white bg-dark"><div class="card-body"><h6>Total Members</h6><h3>{{ $totals['members_total'] }}</h3></div></div></div>
    </div>

    <div class="row g-3">
        <div class="col-md-6">
            <div class="card"><div class="card-header">Attendance by Zone</div>
                <div class="card-body">
                    <ul class="list-group">
                        @forelse($attendanceByZone as $z)
                            <li class="list-group-item d-flex justify-content-between"><span>{{ $z->zone_name }}</span><strong>{{ $z->total }}</strong></li>
                        @empty
                            <li class="list-group-item">No data yet.</li>
                        @endforelse
                    </ul>
                </div>
            </div>
        </div>

        <div class="col-md-6">
            <div class="card"><div class="card-header">Recent Homecell Reports</div>
                <div class="card-body">
                    <ul class="list-group">
                        @forelse($recentReports as $r)
                            <li class="list-group-item">
                                {{ optional($r->homecell)->name ?? 'Unknown homecell' }}
                                <span class="text-muted">— {{ $r->created_at->diffForHumans() }}</span>
                            </li>
                        @empty
                            <li class="list-group-item">Nothing submitted yet.</li>
                        @endforelse
                    </ul>
                </div>
            </div>
        </div>
    </div>
</div>
@endsection
