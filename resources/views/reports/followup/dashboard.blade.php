@extends('layouts.app')

@section('content')
<style>
    /* --- Palette / helpers --- */
    :root{
        --c-primary:#2563eb;      /* blue */
        --c-warning:#f59e0b;      /* amber */
        --c-danger:#ef4444;       /* red  */
        --c-success:#16a34a;      /* green*/
        --c-slate:#0f172a;        /* slate-900 */
        --card-radius: 16px;
        --shadow: 0 10px 25px rgba(2,6,23,.08);
        --shadow-strong: 0 20px 40px rgba(2,6,23,.12);
    }
    .page-title{
        font-weight: 800;
        letter-spacing: .2px;
        color: var(--c-slate);
    }
    .kpi{
        border: none;
        border-radius: var(--card-radius);
        box-shadow: var(--shadow);
        overflow: hidden;
        transition: transform .15s ease, box-shadow .15s ease;
        color:#fff;
    }
    .kpi:hover{ transform: translateY(-2px); box-shadow: var(--shadow-strong); }
    .kpi .icon{
        width: 44px; height: 44px;
        display:grid; place-items:center;
        border-radius: 12px;
        background: rgba(255,255,255,.18);
        backdrop-filter: blur(2px);
        margin-right: .75rem;
        font-size: 1.25rem;
    }
    .kpi .label{ opacity:.95; font-weight:600; }
    .kpi .value{ font-size: 2rem; line-height: 1; font-weight: 800; letter-spacing:.3px;}

    .kpi.blue{    background: linear-gradient(135deg,#1d4ed8 0%,#60a5fa 100%); }
    .kpi.cyan{    background: linear-gradient(135deg,#0891b2 0%,#67e8f9 100%); }
    .kpi.amber{   background: linear-gradient(135deg,#b45309 0%,#fbbf24 100%); }
    .kpi.green{   background: linear-gradient(135deg,#15803d 0%,#34d399 100%); }
    .kpi.red{     background: linear-gradient(135deg,#b91c1c 0%,#f87171 100%); }

    .metric-card{
        border: 0;
        border-radius: var(--card-radius);
        box-shadow: var(--shadow);
        overflow: hidden;
    }
    .metric-card .card-header{
        background: #fff;
        font-weight: 700;
        color: var(--c-slate);
        border-bottom: 1px solid #eef2f7;
    }
    .big-number{
        font-size: 56px;
        font-weight: 800;
        letter-spacing: .5px;
    }
    .muted{
        color:#6b7280;
    }
    .chart-card{
        border: 0;
        border-radius: var(--card-radius);
        box-shadow: var(--shadow);
    }
</style>

<div class="container">

    <h1 class="page-title mb-4">Follow-Up Reports & Analytics</h1>

    {{-- KPIs row --}}
    <div class="row g-3 mb-4">
        <div class="col-12 col-md-3">
            <div class="kpi blue p-3 d-flex align-items-center">
                <div class="icon"><i class="bi bi-list-task"></i></div>
                <div>
                    <div class="label">Total Assignments</div>
                    <div class="value">{{ $totalAssignments }}</div>
                </div>
            </div>
        </div>

        <div class="col-12 col-md-3">
            <div class="kpi cyan p-3 d-flex align-items-center">
                <div class="icon"><i class="bi bi-lightning-charge"></i></div>
                <div>
                    <div class="label">Active</div>
                    <div class="value">{{ $activeAssignments }}</div>
                </div>
            </div>
        </div>

        <div class="col-12 col-md-3">
            <div class="kpi amber p-3 d-flex align-items-center">
                <div class="icon"><i class="bi bi-arrow-repeat"></i></div>
                <div>
                    <div class="label">Reassigned</div>
                    <div class="value">{{ $reassignedAssignments }}</div>
                </div>
            </div>
        </div>

        <div class="col-12 col-md-3">
            <div class="kpi green p-3 d-flex align-items-center">
                <div class="icon"><i class="bi bi-check2-circle"></i></div>
                <div>
                    <div class="label">Completed</div>
                    <div class="value">{{ $completedAssignments }}</div>
                </div>
            </div>
        </div>
    </div>

    {{-- Pending + rates --}}
    <div class="row g-3 mb-4">
        <div class="col-12 col-lg-6">
            <div class="metric-card card">
                <div class="card-header d-flex align-items-center">
                    <i class="bi bi-hourglass-split me-2 text-danger"></i> Pending (Active + Reassigned)
                </div>
                <div class="card-body text-center py-4">
                    <div class="big-number text-danger">{{ $pendingAssignments }}</div>
                    <div class="muted">assignments awaiting completion</div>
                </div>
            </div>
        </div>

        <div class="col-12 col-lg-3">
            <div class="metric-card card">
                <div class="card-header d-flex align-items-center">
                    <i class="bi bi-graph-up-arrow me-2 text-info"></i> Follow-Up Success Rate
                </div>
                <div class="card-body text-center py-4">
                    <div class="big-number text-info">{{ $successRate }}%</div>
                    <div class="muted">of assignments completed</div>
                </div>
            </div>
        </div>

        <div class="col-12 col-lg-3">
            <div class="metric-card card">
                <div class="card-header d-flex align-items-center">
                    <i class="bi bi-award me-2 text-success"></i> Conversion Rate
                </div>
                <div class="card-body text-center py-4">
                    <div class="big-number text-success">{{ $conversionRate }}%</div>
                    <div class="muted">members completed foundation class</div>
                </div>
            </div>
        </div>
    </div>

    {{-- Charts --}}
    <div class="row g-3">
        <div class="col-12 col-lg-6">
            <div class="chart-card card">
                <div class="card-header fw-semibold">
                    <i class="bi bi-chat-dots me-2"></i> Follow-Up Methods Usage
                </div>
                <div class="card-body">
                    <canvas id="methodChart" height="230"></canvas>
                </div>
            </div>
        </div>

        <div class="col-12 col-lg-6">
            <div class="chart-card card">
                <div class="card-header fw-semibold">
                    <i class="bi bi-people me-2"></i> Team Performance
                </div>
                <div class="card-body">
                    <canvas id="teamChart" height="230"></canvas>
                </div>
            </div>
        </div>
    </div>

</div>

{{-- Charts --}}
@push('scripts')
<script src="https://cdn.jsdelivr.net/npm/chart.js"></script>
<script>
    // ------- Method chart data -------
    const methodLabels = {!! json_encode(($methodStats ?? collect())->pluck('method')) !!};
    const methodCounts = {!! json_encode(($methodStats ?? collect())->pluck('count')) !!};

    const methodCtx = document.getElementById('methodChart').getContext('2d');
    new Chart(methodCtx, {
        type: 'doughnut',
        data: {
            labels: methodLabels,
            datasets: [{
                data: methodCounts,
                borderWidth: 0,
                hoverOffset: 6,
                backgroundColor: [
                    '#60a5fa','#34d399','#fbbf24','#f87171','#a78bfa','#22d3ee'
                ]
            }]
        },
        options: {
            responsive: true,
            plugins: {
                legend: { position: 'bottom' }
            }
        }
    });

    // ------- Team performance data -------
    const teamLabels   = {!! json_encode(($teamPerformance ?? collect())->pluck('team_member')) !!};
    const completedSet = {!! json_encode(($teamPerformance ?? collect())->pluck('completed')) !!};

    const teamCtx = document.getElementById('teamChart').getContext('2d');
    new Chart(teamCtx, {
        type: 'bar',
        data: {
            labels: teamLabels,
            datasets: [{
                label: 'Completed Follow-Ups',
                data: completedSet,
                borderWidth: 0,
                borderRadius: 8,
                backgroundColor: '#34d399'
            }]
        },
        options: {
            responsive: true,
            scales: {
                y: { beginAtZero: true, ticks: { stepSize: 1 } }
            },
            plugins: {
                legend: { display: false }
            }
        }
    });
</script>
@endpush
@endsection
