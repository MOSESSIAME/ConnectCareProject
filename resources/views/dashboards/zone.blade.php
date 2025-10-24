@extends('layouts.app')

@section('content')
<div class="container">
    <h2 class="fw-bold mb-4 text-primary">Zonal Dashboard</h2>

    @if(isset($error))
        <div class="alert alert-warning">{{ $error }}</div>
    @else
        <div class="row mb-4 text-center">
            <div class="col-md-3">
                <div class="card bg-primary text-white shadow-sm">
                    <div class="card-body">
                        <h6>Total Homecells</h6>
                        <h3>{{ $homecellCount }}</h3>
                    </div>
                </div>
            </div>

            <div class="col-md-3">
                <div class="card bg-success text-white shadow-sm">
                    <div class="card-body">
                        <h6>Total Members</h6>
                        <h3>{{ $memberCount }}</h3>
                    </div>
                </div>
            </div>

            <div class="col-md-3">
                <div class="card bg-warning text-dark shadow-sm">
                    <div class="card-body">
                        <h6>First Timers</h6>
                        <h3>{{ $summary['first_timers'] ?? 0 }}</h3>
                    </div>
                </div>
            </div>

            <div class="col-md-3">
                <div class="card bg-info text-white shadow-sm">
                    <div class="card-body">
                        <h6>New Converts</h6>
                        <h3>{{ $summary['new_converts'] ?? 0 }}</h3>
                    </div>
                </div>
            </div>
        </div>

        {{-- Recent Reports Section --}}
        <div class="card shadow-sm mt-4">
            <div class="card-header bg-light fw-semibold">Recent Homecell Reports ({{ $zone->name }})</div>
            <div class="card-body">
                @if($recentReports->isEmpty())
                    <p class="text-muted text-center">No reports submitted yet.</p>
                @else
                    <table class="table table-bordered table-striped align-middle">
                        <thead class="table-secondary">
                            <tr>
                                <th>Date</th>
                                <th>Homecell</th>
                                <th>Males</th>
                                <th>Females</th>
                                <th>First Timers</th>
                                <th>New Converts</th>
                            </tr>
                        </thead>
                        <tbody>
                            @foreach($recentReports as $report)
                                <tr>
                                    <td>{{ \Carbon\Carbon::parse($report->created_at)->format('d M Y') }}</td>
                                    <td>{{ $report->homecell->name ?? 'N/A' }}</td>
                                    <td>{{ $report->males }}</td>
                                    <td>{{ $report->females }}</td>
                                    <td>{{ $report->first_timers }}</td>
                                    <td>{{ $report->new_converts }}</td>
                                </tr>
                            @endforeach
                        </tbody>
                    </table>
                @endif
            </div>
        </div>
    @endif
</div>
@endsection
