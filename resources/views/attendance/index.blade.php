@extends('layouts.app')

@section('content')
<div class="container py-4">

    <h2 class="fw-bold text-primary mb-3">
        <i class="bi bi-bar-chart-line me-2"></i> Service Attendance Records
    </h2>

    @if(session('success'))
        <div class="alert alert-success">{{ session('success') }}</div>
    @endif

    {{-- Filter Section --}}
    <div class="card mb-4 shadow-sm border-0">
        <div class="card-header bg-light fw-semibold">Filter Records</div>
        <div class="card-body">
            <form method="GET" action="{{ route('attendance.index') }}" class="row g-3">
                <div class="col-md-4">
                    <label class="form-label">Service</label>
                    <select name="service_id" class="form-select">
                        <option value="">-- All Services --</option>
                        @foreach($services as $service)
                            <option value="{{ $service->id }}" {{ request('service_id') == $service->id ? 'selected' : '' }}>
                                {{ $service->name }}
                            </option>
                        @endforeach
                    </select>
                </div>

                <div class="col-md-3">
                    <label class="form-label">From Date</label>
                    <input type="date" name="from_date" class="form-control" value="{{ request('from_date') }}">
                </div>

                <div class="col-md-3">
                    <label class="form-label">To Date</label>
                    <input type="date" name="to_date" class="form-control" value="{{ request('to_date') }}">
                </div>

                <div class="col-md-2 d-flex align-items-end gap-2">
                    <button type="submit" class="btn btn-primary w-100">
                        <i class="bi bi-funnel me-1"></i> Filter
                    </button>
                </div>
            </form>
        </div>
    </div>

    {{-- Monthly totals (2 cards) --}}
    <style>
        .hc-mini-card {
            border: 1px solid rgba(0, 0, 0, 0.06);
            border-radius: 12px;
            background: hsl(228, 9%, 89%);
            box-shadow: 0 6px 18px rgba(0,0,0,.06);
            transition: transform .15s ease, box-shadow .15s ease;
        }
        .hc-mini-card:hover { transform: translateY(-2px); box-shadow: 0 10px 24px rgba(0,0,0,.08); }
        .hc-badge {
            font-size: .75rem;
            padding: .15rem .5rem;
            border-radius: 999px;
            background: #eef2ff;
            color: #3b5bdb;
        }
    </style>

    <div class="row g-3 mb-4">
        <div class="col-12 col-md-6">
            <div class="hc-mini-card p-3 h-100 d-flex justify-content-between align-items-center">
                <div>
                    <div class="hc-badge mb-1">{{ $monthLabel }}</div>
                    <div class="fw-semibold text-muted">First Timers (Month)</div>
                    <div class="display-6 fw-bold text-info">{{ $monthlyTotals['first_timers'] }}</div>
                </div>
                <i class="bi bi-person-plus display-6 text-info"></i>
            </div>
        </div>

        <div class="col-12 col-md-6">
            <div class="hc-mini-card p-3 h-100 d-flex justify-content-between align-items-center">
                <div>
                    <div class="hc-badge mb-1">{{ $monthLabel }}</div>
                    <div class="fw-semibold text-muted">New Converts (Month)</div>
                    <div class="display-6 fw-bold text-danger">{{ $monthlyTotals['new_converts'] }}</div>
                </div>
                <i class="bi bi-stars display-6 text-danger"></i>
            </div>
        </div>
    </div>

    {{-- Attendance Table --}}
    <div class="card shadow-sm border-0">
        <div class="card-header bg-light d-flex justify-content-between align-items-center">
            <span class="fw-semibold">Attendance Records</span>

            <div class="d-flex gap-2">
                <a href="{{ route('attendance.export.pdf', request()->query()) }}" class="btn btn-danger btn-sm">
                    <i class="bi bi-filetype-pdf me-1"></i> Export PDF
                </a>

                <a href="{{ route('attendance.create') }}" class="btn btn-success btn-sm">
                    <i class="bi bi-plus-circle me-1"></i> Record Attendance
                </a>
            </div>
        </div>
        <div class="card-body">
            @if($records->count())
                <div class="table-responsive">
                    <table class="table table-striped align-middle text-nowrap">
                        <thead class="table-dark">
                            <tr>
                                <th>#</th>
                                <th>Service</th>
                                <th>Date</th>
                                <th>Males</th>
                                <th>Females</th>
                                <th>Children</th>
                                <th>First Timers</th>
                                <th>New Converts</th>
                                <th>Offering (ZMW)</th>
                                <th>Notes</th>
                                <th class="text-end" style="width: 130px;">Actions</th>
                            </tr>
                        </thead>
                        <tbody>
                            @foreach($records as $i => $record)
                                <tr>
                                    <td>{{ $records->firstItem() + $i }}</td>
                                    <td>{{ $record->service->name ?? 'N/A' }}</td>
                                    <td>{{ optional($record->service)->service_date ? \Carbon\Carbon::parse($record->service->service_date)->format('d M Y') : 'N/A' }}</td>
                                    <td>{{ $record->males }}</td>
                                    <td>{{ $record->females }}</td>
                                    <td>{{ $record->children }}</td>
                                    <td>{{ $record->first_timers }}</td>
                                    <td>{{ $record->new_converts }}</td>
                                    <td>{{ number_format($record->offering, 2) }}</td>
                                    <td>{{ $record->notes ?? '–' }}</td>
                                    <td class="text-end">
                                        <a href="{{ route('attendance.edit', $record) }}" class="btn btn-sm btn-warning me-1">
                                            <i class="bi bi-pencil-square"></i>
                                        </a>
                                        <form action="{{ route('attendance.destroy', $record) }}"
                                              method="POST" class="d-inline"
                                              onsubmit="return confirm('Delete this attendance record?');">
                                            @csrf
                                            @method('DELETE')
                                            <button class="btn btn-sm btn-danger">
                                                <i class="bi bi-trash"></i>
                                            </button>
                                        </form>
                                    </td>
                                </tr>
                            @endforeach
                        </tbody>
                    </table>
                </div>

                <div class="mt-3">
                    {{ $records->links() }}
                </div>
            @else
                <div class="alert alert-light border text-center mb-0">
                    <i class="bi bi-info-circle text-primary me-1"></i>
                    No records found for the selected filters.
                </div>
            @endif
        </div>
    </div>
</div>
@endsection
