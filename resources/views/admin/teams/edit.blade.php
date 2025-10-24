@extends('layouts.app')

@section('content')
<div class="container">
    <h2 class="fw-bold mb-4">Edit Team</h2>

    @if ($errors->any())
        <div class="alert alert-danger">
            <ul class="mb-0">
                @foreach ($errors->all() as $e)
                    <li>{{ $e }}</li>
                @endforeach
            </ul>
        </div>
    @endif

    <form action="{{ route('admin.teams.update', $item->id) }}" method="POST" class="card shadow-sm p-4 bg-white">
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

        {{-- If you pass $leaders to this view, you can uncomment this block to edit leader too --}}
        {{-- 
        <div class="mb-3">
            <label for="leader_id" class="form-label">Team Leader</label>
            <select name="leader_id" id="leader_id" class="form-select">
                <option value="">-- Select Leader --</option>
                @foreach($leaders as $leader)
                    <option value="{{ $leader->id }}" @selected(old('leader_id', $item->leader_id) == $leader->id)>
                        {{ $leader->name }}
                    </option>
                @endforeach
            </select>
            @error('leader_id') <small class="text-danger">{{ $message }}</small> @enderror
        </div>
        --}}

        <button type="submit" class="btn btn-primary">Update</button>
        <a href="{{ route('admin.teams.index') }}" class="btn btn-secondary">Cancel</a>
    </form>
</div>
@endsection
