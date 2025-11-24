@extends('layouts.app')

@section('content')
<div class="container">
    <h2 class="fw-bold mb-4">Record Service Attendance</h2>

    @if($errors->any())
        <div class="alert alert-danger">
            <strong>There were some problems with your input:</strong>
            <ul class="mb-0 mt-2">
                @foreach ($errors->all() as $e)
                    <li>{{ $e }}</li>
                @endforeach
            </ul>
        </div>
    @endif

    <form action="{{ route('attendance.store') }}" method="POST" class="card p-4 shadow-sm bg-white">
        @csrf

        <div class="mb-3">
            <label class="form-label">Service</label>
            <select name="service_id" class="form-select" required>
                <option value="">-- Select Service --</option>
                @foreach($services as $s)
                    <option value="{{ $s->id }}" {{ old('service_id') == $s->id ? 'selected' : '' }}>
                        {{ $s->name }}
                    </option>
                @endforeach
            </select>
            <div class="form-text">
                Select the service name (the attendance date will be the time you record this attendance).
            </div>
        </div>

        <div class="row g-3">
            <div class="col-md-4">
                <label class="form-label">Males</label>
                <input type="number" name="males" class="form-control" min="0" value="{{ old('males', 0) }}" required>
            </div>
            <div class="col-md-4">
                <label class="form-label">Females</label>
                <input type="number" name="females" class="form-control" min="0" value="{{ old('females', 0) }}" required>
            </div>
            <div class="col-md-4">
                <label class="form-label">Children (optional)</label>
                <input type="number" name="children" class="form-control" min="0" value="{{ old('children', 0) }}">
            </div>
            <div class="col-md-4">
                <label class="form-label">First-timers (optional)</label>
                <input type="number" name="first_timers" class="form-control" min="0" value="{{ old('first_timers', 0) }}">
            </div>
            <div class="col-md-4">
                <label class="form-label">New Converts (optional)</label>
                <input type="number" name="new_converts" class="form-control" min="0" value="{{ old('new_converts', 0) }}">
            </div>
            <div class="col-md-4">
                <label class="form-label">Offering (optional)</label>
                <div class="input-group">
                    <span class="input-group-text">ZMW</span>
                    <input type="number" step="0.01" min="0" name="offering" class="form-control" value="{{ old('offering', 0) }}">
                </div>
            </div>
        </div>

        <div class="mt-3 mb-3">
            <label class="form-label">Notes (optional)</label>
            <textarea name="notes" class="form-control" rows="3" placeholder="Any remarks...">{{ old('notes') }}</textarea>
        </div>

        <div class="d-flex gap-2">
            <a href="{{ route('attendance.index') }}" class="btn btn-secondary">Back</a>
            <button class="btn btn-primary">Save Attendance</button>
        </div>
    </form>
</div>
@endsection
