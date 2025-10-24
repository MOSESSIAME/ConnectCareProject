@extends('layouts.app')

@section('content')
<div class="container">
    <div class="d-flex justify-content-between align-items-center mb-3">
        <h2 class="fw-bold text-primary">Edit Homecell</h2>
        <a href="{{ route('admin.homecells.index') }}" class="btn btn-outline-secondary">&larr; Back</a>
    </div>

    @if($errors->any())
        <div class="alert alert-danger">
            <strong>Whoops!</strong> Please fix the following errors:
            <ul class="mb-0">
                @foreach($errors->all() as $e)
                    <li>{{ $e }}</li>
                @endforeach
            </ul>
        </div>
    @endif

    <div class="card shadow-sm">
        <div class="card-header bg-dark text-white">Update Homecell Information</div>
        <div class="card-body">
            <form action="{{ route('admin.homecells.update', $homecell) }}" method="POST">
                @csrf
                @method('PUT')

                <div class="mb-3">
                    <label for="name" class="form-label">Homecell Name</label>
                    <input type="text" name="name" id="name" class="form-control"
                           value="{{ old('name', $homecell->name) }}" required>
                </div>

                <div class="mb-3">
                    <label for="zone_id" class="form-label">Zone</label>
                    <select name="zone_id" id="zone_id" class="form-select" required>
                        <option value="">-- Select Zone --</option>
                        @foreach($zones as $z)
                            <option value="{{ $z->id }}" @selected(old('zone_id', $homecell->zone_id)==$z->id)>
                                {{ $z->name }}
                            </option>
                        @endforeach
                    </select>
                </div>

                <div class="mb-3">
                    <label for="leader_id" class="form-label">Leader (optional)</label>
                    <select name="leader_id" id="leader_id" class="form-select">
                        <option value="">-- None --</option>
                        @foreach($leaders as $u)
                            <option value="{{ $u->id }}" @selected(old('leader_id', $homecell->leader_id)==$u->id)>
                                {{ $u->name }}
                            </option>
                        @endforeach
                    </select>
                </div>

                <div class="d-flex justify-content-between">
                    <a href="{{ route('admin.homecells.index') }}" class="btn btn-secondary">Back</a>
                    <button type="submit" class="btn btn-primary">Update Homecell</button>
                </div>
            </form>
        </div>
    </div>
</div>
@endsection
