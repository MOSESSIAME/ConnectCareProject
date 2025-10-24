@extends('layouts.app')

@section('content')
<div class="container py-4">
    <h2 class="fw-bold text-primary mb-4">
        <i class="bi bi-pencil-square me-2"></i> Edit Service Attendance
    </h2>

    {{-- ✅ Error Messages --}}
    @if($errors->any())
        <div class="alert alert-danger">
            <ul class="mb-0">
                @foreach($errors->all() as $error)
                    <li>{{ $error }}</li>
                @endforeach
            </ul>
        </div>
    @endif

    {{-- ✅ Success Message --}}
    @if(session('success'))
        <div class="alert alert-success">{{ session('success') }}</div>
    @endif

    <form action="{{ route('attendance.update', $attendance->id) }}" method="POST" class="card shadow-sm p-4 bg-white rounded-4">
        @csrf
        @method('PUT')

        <div class="row g-3">
            <!-- Service -->
            <div class="col-md-6">
                <label for="service_id" class="form-label fw-semibold">Service</label>
                <select name="service_id" id="service_id" class="form-select" required>
                    <option value="">-- Select Service --</option>
                    @foreach($services as $service)
                        <option value="{{ $service->id }}"
                            {{ old('service_id', $attendance->service_id) == $service->id ? 'selected' : '' }}>
                            {{ $service->name }} — {{ optional($service->service_date)->format('d M Y') }}
                        </option>
                    @endforeach
                </select>
                @error('service_id') <small class="text-danger">{{ $message }}</small> @enderror
            </div>

            <!-- Males -->
            <div class="col-md-2">
                <label for="males" class="form-label">Males</label>
                <input type="number" name="males" id="males" class="form-control"
                    value="{{ old('males', $attendance->males) }}" min="0" required>
                @error('males') <small class="text-danger">{{ $message }}</small> @enderror
            </div>

            <!-- Females -->
            <div class="col-md-2">
                <label for="females" class="form-label">Females</label>
                <input type="number" name="females" id="females" class="form-control"
                    value="{{ old('females', $attendance->females) }}" min="0" required>
                @error('females') <small class="text-danger">{{ $message }}</small> @enderror
            </div>

            <!-- Children -->
            <div class="col-md-2">
                <label for="children" class="form-label">Children</label>
                <input type="number" name="children" id="children" class="form-control"
                    value="{{ old('children', $attendance->children) }}" min="0">
                @error('children') <small class="text-danger">{{ $message }}</small> @enderror
            </div>

            <!-- First Timers -->
            <div class="col-md-2">
                <label for="first_timers" class="form-label">First Timers</label>
                <input type="number" name="first_timers" id="first_timers" class="form-control"
                    value="{{ old('first_timers', $attendance->first_timers) }}" min="0">
                @error('first_timers') <small class="text-danger">{{ $message }}</small> @enderror
            </div>

            <!-- New Converts -->
            <div class="col-md-2">
                <label for="new_converts" class="form-label">New Converts</label>
                <input type="number" name="new_converts" id="new_converts" class="form-control"
                    value="{{ old('new_converts', $attendance->new_converts) }}" min="0">
                @error('new_converts') <small class="text-danger">{{ $message }}</small> @enderror
            </div>

            <!-- Offering -->
            <div class="col-md-3">
                <label for="offering" class="form-label">Offering (ZMW)</label>
                <input type="number" step="0.01" min="0" name="offering" id="offering" class="form-control"
                    value="{{ old('offering', $attendance->offering) }}">
                @error('offering') <small class="text-danger">{{ $message }}</small> @enderror
            </div>

            <!-- Notes -->
            <div class="col-md-9">
                <label for="notes" class="form-label">Notes</label>
                <textarea name="notes" id="notes" class="form-control" rows="3"
                    placeholder="Any remarks or details...">{{ old('notes', $attendance->notes) }}</textarea>
                @error('notes') <small class="text-danger">{{ $message }}</small> @enderror
            </div>

            <!-- Buttons -->
            <div class="col-12 text-end mt-3">
                <a href="{{ route('attendance.index') }}" class="btn btn-outline-secondary">Cancel</a>
                <button type="submit" class="btn btn-primary px-4">Update Record</button>
            </div>
        </div>
    </form>
</div>
@endsection
