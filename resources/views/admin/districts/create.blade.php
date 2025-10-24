@extends('layouts.app')

@section('content')
<div class="container">
    <h2 class="fw-bold mb-4">Add New District</h2>

    <a href="{{ route('admin.districts.index') }}" class="btn btn-secondary mb-3">
        <i class="bi bi-arrow-left"></i> Back
    </a>

    @if ($errors->any())
        <div class="alert alert-danger">
            <strong>Whoops!</strong> Please fix the following errors:
            <ul class="mb-0 mt-2">
                @foreach ($errors->all() as $error)
                    <li>{{ $error }}</li>
                @endforeach
            </ul>
        </div>
    @endif

    <div class="card shadow-sm border-0 rounded-4">
        <div class="card-body p-4">
            <form action="{{ route('admin.districts.store') }}" method="POST">
                @csrf

                <div class="mb-3">
                    <label for="name" class="form-label fw-semibold">District Name</label>
                    <input type="text" name="name" id="name"
                           class="form-control @error('name') is-invalid @enderror"
                           value="{{ old('name') }}" placeholder="Enter district name" required>
                    @error('name')
                        <small class="text-danger">{{ $message }}</small>
                    @enderror
                </div>

                <div class="mb-3">
                    <label for="church_id" class="form-label fw-semibold">Select Church</label>
                    <select name="church_id" id="church_id"
                            class="form-select @error('church_id') is-invalid @enderror" required>
                        <option value="">-- Choose Church --</option>
                        @foreach($churches as $church)
                            <option value="{{ $church->id }}" {{ old('church_id') == $church->id ? 'selected' : '' }}>
                                {{ $church->name }}
                            </option>
                        @endforeach
                    </select>
                    @error('church_id')
                        <small class="text-danger">{{ $message }}</small>
                    @enderror
                </div>

                <button type="submit" class="btn btn-primary w-100 fw-semibold py-2">
                    <i class="bi bi-save me-1"></i> Save District
                </button>
            </form>
        </div>
    </div>
</div>
@endsection
