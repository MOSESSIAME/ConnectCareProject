@extends('layouts.app')

@section('content')
<div class="container">
    <h2 class="fw-bold mb-4 text-primary">📊 Homecell Reports Dashboard</h2>

    {{-- Summary Cards --}}
    <div class="row text-center mb-4">
        <div class="col-md-3">
            <div class="card bg-primary text-white shadow-sm">
                <div class="card-body">
                    <h6>Total Attendance</h6>
                    <h3>{{ $totals['attendance'] ?? 0 }}</h3>
                </div>
            </div>
        </div>
        <div class="col-md-3">
            <div class="card bg-success text-white shadow-sm">
                <div class="card-body">
                    <h6>First Timers</h6>
                    <h3>{{ $totals['first_timers'] ?? 0 }}</h3>
                </div>
            </div>
        </div>
        <div class="col-md-3">
            <div class="card bg-warning text-dark shadow-sm">
                <div class="card-body">
                    <h6>New Converts</h6>
                    <h3>{{ $totals['new_converts'] ?? 0 }}</h3>
                </div>
            </div>
        </div>
        <div class="col-md-3">
            <div class="card bg-info text-white shadow-sm">
                <div class="card-body">
                    <h6>Zones Reported</h6>
                    <h3>{{ count($attendanceByZone ?? []) }}</h3>
                </div>
            </div>
        </div>
    </div>

    {{-- Attendance Table --}}
    <div class="card shadow-sm">
        <div class="card-header fw-semibold bg-light">
            Attendance Breakdown by Zone
        </div>
        <div class="card-body">
            @if(isset($attendanceByZone) && count($attendanceByZone))
                <table class="table table-bordered align-middle">
                    <thead class="table-dark">
                        <tr>
                            <th>#</th>
                            <th>Zone</th>
                            <th>Total Attendance</th>
                            <th>First Timers</th>
                            <th>New Converts</th>
                        </tr>
                    </thead>
                    <tbody>
                        @foreach($attendanceByZone as $index => $zone)
                            <tr>
                                <td>{{ $index + 1 }}</td>
                                <td>{{ $zone->name ?? 'N/A' }}</td>
                                <td>{{ $zone->attendance ?? 0 }}</td>
                                <td>{{ $zone->first_timers ?? 0 }}</td>
                                <td>{{ $zone->new_converts ?? 0 }}</td>
                            </tr>
                        @endforeach
                    </tbody>
                </table>
            @else
                <p class="text-muted mb-0">No report data found yet.</p>
            @endif
        </div>
    </div>
</div>
@endsection
