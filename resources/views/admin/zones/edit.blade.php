@extends('layouts.app')

@section('content')
<div class="container">
    <h2 class="fw-bold mb-4">Edit Zone</h2>

    @if ($errors->any())
        <div class="alert alert-danger">
            <ul class="mb-0">
                @foreach ($errors->all() as $e)
                    <li>{{ $e }}</li>
                @endforeach
            </ul>
        </div>
    @endif

    <form action="{{ route('admin.zones.update', $item->id) }}" method="POST" class="card shadow-sm p-4 bg-white">
        @csrf
        @method('PUT')

        <div class="mb-3">
            <label for="name" class="form-label">Zone Name</label>
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

        <div class="mb-3">
            <label for="district_id" class="form-label">District</label>
            <select name="district_id" id="district_id"
                    class="form-select @error('district_id') is-invalid @enderror" required>
                <option value="">-- Choose District --</option>
                @foreach($districts as $d)
                    <option value="{{ $d->id }}"
                        {{ (int) old('district_id', $item->district_id) === (int) $d->id ? 'selected' : '' }}>
                        {{ $d->name }}
                    </option>
                @endforeach
            </select>
            @error('district_id') <small class="text-danger">{{ $message }}</small> @enderror
        </div>

        {{-- New: Zonal Leader --}}
        <div class="mb-3">
            <label for="leader_id" class="form-label">Zonal Leader (optional)</label>
            <select name="leader_id" id="leader_id"
                    class="form-select @error('leader_id') is-invalid @enderror">
                <option value="">-- No Leader Yet --</option>
                @foreach($leaders as $u)
                    <option value="{{ $u->id }}"
                        {{ (int) old('leader_id', $item->leader_id) === (int) $u->id ? 'selected' : '' }}>
                        {{ $u->name }}
                    </option>
                @endforeach
            </select>
            @error('leader_id') <small class="text-danger">{{ $message }}</small> @enderror
        </div>

        <button type="submit" class="btn btn-primary">Update</button>
        <a href="{{ route('admin.zones.index') }}" class="btn btn-secondary">Cancel</a>
    </form>
</div>
@endsection
