@extends('layouts.app')

@section('content')
<div class="container">
    <h2 class="fw-bold mb-4">Edit District</h2>

    @if ($errors->any())
        <div class="alert alert-danger">
            <ul class="mb-0">
                @foreach ($errors->all() as $e)
                    <li>{{ $e }}</li>
                @endforeach
            </ul>
        </div>
    @endif

    <form action="{{ route('admin.districts.update', $item->id) }}" method="POST" class="card shadow-sm p-4 bg-white">
        @csrf
        @method('PUT')

        <div class="mb-3">
            <label for="name" class="form-label">Name</label>
            <input
                type="text"
                name="name"
                id="name"
                value="{{ old('name', $item->name) }}"
                class="form-control"
                required
            >
            @error('name') <small class="text-danger">{{ $message }}</small> @enderror
        </div>

        {{-- If you also allow editing the church, add a select here (and pass $churches) --}}

        <button type="submit" class="btn btn-primary">Update</button>
        <a href="{{ route('admin.districts.index') }}" class="btn btn-secondary">Cancel</a>
    </form>
</div>
@endsection
