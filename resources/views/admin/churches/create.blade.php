@extends('layouts.app')

@section('content')
<div class="container">
    <h2 class="fw-bold mb-4">Add Church</h2>

    <a href="{{ route('admin.churches.index') }}" class="btn btn-secondary mb-3">
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

    <form action="{{ route('admin.churches.store') }}" method="POST" class="card shadow-sm p-4 bg-white">
        @csrf

        <div class="mb-3">
            <label for="name" class="form-label">Church Name</label>
            <input
                type="text"
                name="name"
                id="name"
                class="form-control @error('name') is-invalid @enderror"
                value="{{ old('name') }}"
                placeholder="e.g. Faith Tabernacle"
                required
            >
            @error('name') <small class="text-danger">{{ $message }}</small> @enderror
        </div>

        <button type="submit" class="btn btn-success">Save</button>
        <a href="{{ route('admin.churches.index') }}" class="btn btn-secondary">Cancel</a>
    </form>
</div>
@endsection
