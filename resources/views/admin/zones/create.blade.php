@extends('layouts.app')

@section('content')
<div class="container">
    <h2 class="fw-bold mb-4">Add Zone</h2>

    <a href="{{ route('admin.zones.index') }}" class="btn btn-secondary mb-3">
        <i class="bi bi-arrow-left"></i> Back
    </a>

    @if ($errors->any())
        <div class="alert alert-danger">
            <strong>Whoops!</strong> Please fix the following:
            <ul class="mb-0 mt-2">
                @foreach ($errors->all() as $e)
                    <li>{{ $e }}</li>
                @endforeach
            </ul>
        </div>
    @endif

    <form action="{{ route('admin.zones.store') }}" method="POST" class="card shadow-sm p-4 bg-white">
        @csrf

        <div class="mb-3">
            <label for="name" class="form-label">Zone Name</label>
            <input
                type="text"
                name="name"
                id="name"
                value="{{ old('name') }}"
                class="form-control @error('name') is-invalid @enderror"
                placeholder="e.g. Zone A"
                required
            >
            @error('name') <small class="text-danger">{{ $message }}</small> @enderror
        </div>

        <div class="mb-3">
            <label for="district_id" class="form-label">District</label>
            <select name="district_id" id="district_id"
                    class="form-select @error('district_id') is-invalid @enderror" required>
                <option value="">-- Choose District --</option>
                @foreach($districts as $d)
                    <option value="{{ $d->id }}" {{ old('district_id') == $d->id ? 'selected' : '' }}>
                        {{ $d->name }}
                    </option>
                @endforeach
            </select>
            @error('district_id') <small class="text-danger">{{ $message }}</small> @enderror
        </div>

        <button type="submit" class="btn btn-success">Save</button>
        <a href="{{ route('admin.zones.index') }}" class="btn btn-secondary">Cancel</a>
    </form>
</div>
@endsection
