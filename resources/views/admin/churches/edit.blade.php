@extends('layouts.app')

@section('content')
<div class="container">
    <h2 class="fw-bold mb-4">Edit Church</h2>

    @if ($errors->any())
        <div class="alert alert-danger">
            <ul class="mb-0">
                @foreach ($errors->all() as $e)
                    <li>{{ $e }}</li>
                @endforeach
            </ul>
        </div>
    @endif

    <form action="{{ route('admin.churches.update', $item->id) }}" method="POST" class="card shadow-sm p-4 bg-white">
        @csrf
        @method('PUT')

        <div class="mb-3">
            <label for="name" class="form-label">Church Name</label>
            <input
                type="text"
                name="name"
                id="name"
                value="{{ old('name', $item->name) }}"
                class="form-control @error('name') is-invalid @enderror"
                required
            >
            @error('name') <small class="text-danger">{{ $message }}</small> @enderror
        </div>

        <button type="submit" class="btn btn-primary">Update</button>
        <a href="{{ route('admin.churches.index') }}" class="btn btn-secondary">Cancel</a>
    </form>
</div>
@endsection
