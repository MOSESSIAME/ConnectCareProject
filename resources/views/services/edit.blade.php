@extends('layouts.app')

@section('content')
<div class="container">
    <h2 class="fw-bold mb-4">Edit Service</h2>

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

    <form action="{{ route('services.update', $service->id) }}" method="POST" class="card p-4 shadow-sm bg-white">
        @csrf
        @method('PUT')

        <div class="mb-3">
            <label class="form-label">Service Name</label>
            <input type="text" name="name" class="form-control" value="{{ old('name', $service->name) }}" required>
            @error('name') <small class="text-danger">{{ $message }}</small> @enderror
        </div>

        <div class="mb-3">
            <label class="form-label">Service Date</label>
            <input
                type="date"
                name="date"
                class="form-control"
                value="{{ old('date', optional($service->service_date)->format('Y-m-d')) }}"
                required
            >
            @error('date') <small class="text-danger">{{ $message }}</small> @enderror
        </div>

        <div class="d-flex gap-2">
            <a href="{{ route('services.index') }}" class="btn btn-secondary">Back</a>
            <button class="btn btn-primary">Update Service</button>
        </div>
    </form>
</div>
@endsection
