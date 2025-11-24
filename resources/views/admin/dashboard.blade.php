@extends('layouts.app')

@section('content')
<div class="container">
    <h2 class="mb-4 fw-bold">Homecell Reports Summary</h2>

    {{-- =======================
        TOTALS SUMMARY SECTION
    ======================== --}}
    <div class="row text-center mb-4">
        <div class="col-md-3">
            <div class="card bg-primary text-white shadow-sm">
                <div class="card-body">
                    <h5>Males</h5>
                    <h3>{{ $totals['males'] ?? 0 }}</h3>
                </div>
            </div>
        </div>

        <div class="col-md-3">
            <div class="card bg-danger text-white shadow-sm">
                <div class="card-body">
                    <h5>Females</h5>
                    <h3>{{ $totals['females'] ?? 0 }}</h3>
                </div>
            </div>
        </div>

        <div class="col-md-3">
            <div class="card bg-success text-white shadow-sm">
                <div class="card-body">
                    <h5>First-timers</h5>
                    <h3>{{ $totals['first_timers'] ?? 0 }}</h3>
                </div>
            </div>
        </div>

        <div class="col-md-3">
            <div class="card bg-info text-white shadow-sm">
                <div class="card-body">
                    <h5>New Converts</h5>
                    <h3>{{ $totals['new_converts'] ?? 0 }}</h3>
                </div>
            </div>
        </div>
    </div>

    {{-- =======================
        CHARTS SECTION
    ======================== --}}
    <div class="row">
        {{-- Chart 1: Attendance by Zone --}}
        <div class="col-md-6 mb-4">
            <div class="card shadow-sm">
                <div class="card-header bg-light fw-semibold">
                    Attendance by Zone
                </div>
                <div class="card-body">
                    <canvas id="zoneChart"></canvas>
                </div>
            </div>
        </div>

        {{-- Chart 2: Overall Attendance Distribution --}}
        <div class="col-md-6 mb-4">
            <div class="card shadow-sm">
                <div class="card-header bg-light fw-semibold">
                    Attendance Distribution
                </div>
                <div class="card-body">
                    <canvas id="attendanceChart"></canvas>
                </div>
            </div>
        </div>
    </div>
</div>

{{-- =======================
    CHART.JS CONFIGURATION
======================= --}}
<script src="https://cdn.jsdelivr.net/npm/chart.js"></script>

<script>
    // ✅ Data for zone-based bar chart
    const zoneLabels = {!! json_encode($attendanceByZone->pluck('zone_name')) !!};
    const zoneTotals = {!! json_encode($attendanceByZone->pluck('total')) !!};

    const ctxZone = document.getElementById('zoneChart').getContext('2d');
    new Chart(ctxZone, {
        type: 'bar',
        data: {
            labels: zoneLabels,
            datasets: [{
                label: 'Total Attendance',
                data: zoneTotals,
                backgroundColor: 'rgba(54, 162, 235, 0.7)',
                borderColor: 'rgba(54, 162, 235, 1)',
                borderWidth: 1
            }]
        },
        options: {
            responsive: true,
            scales: { 
                y: { beginAtZero: true }
            }
        }
    });

    // ✅ Pie chart for overall attendance distribution
    const ctxAttendance = document.getElementById('attendanceChart').getContext('2d');
    new Chart(ctxAttendance, {
        type: 'pie',
        data: {
            labels: ['Males', 'Females'],
            datasets: [{
                data: [
                    {{ $totals['males'] ?? 0 }},
                    {{ $totals['females'] ?? 0 }}
                ],
                backgroundColor: ['#007bff', '#dc3545']
            }]
        },
        options: {
            responsive: true,
        }
    });
</script>
@endsection
