@extends('layouts.app')

@section('content')
<div class="container">
    <h2 class="fw-bold mb-4">Add New Team</h2>

    <!-- Back Button -->
    <a href="{{ route('admin.teams.index') }}" class="btn btn-secondary mb-3">
        <i class="bi bi-arrow-left"></i> Back
    </a>

    <!-- Error Alert -->
    @if ($errors->any())
        <div class="alert alert-danger">
            <strong>Whoops!</strong> Please fix the following issues:
            <ul class="mb-0 mt-2">
                @foreach ($errors->all() as $error)
                    <li>{{ $error }}</li>
                @endforeach
            </ul>
        </div>
    @endif

    <!-- Create Form -->
    <div class="card shadow-sm border-0 rounded-4">
        <div class="card-body p-4">
            <form action="{{ route('admin.teams.store') }}" method="POST">
                @csrf

                <div class="mb-3">
                    <label for="name" class="form-label fw-semibold">Team Name</label>
                    <input type="text" name="name" id="name"
                           class="form-control @error('name') is-invalid @enderror"
                           value="{{ old('name') }}" placeholder="Enter team name" required>
                    @error('name')
                        <small class="text-danger">{{ $message }}</small>
                    @enderror
                </div>

                <div class="mb-3">
                    <label for="leader_id" class="form-label fw-semibold">Team Leader</label>
                    <select name="leader_id" id="leader_id"
                            class="form-select @error('leader_id') is-invalid @enderror" required>
                        <option value="">-- Select Leader --</option>
                        @foreach($leaders as $leader)
                            <option value="{{ $leader->id }}" {{ old('leader_id') == $leader->id ? 'selected' : '' }}>
                                {{ $leader->name }}
                            </option>
                        @endforeach
                    </select>
                    @error('leader_id')
                        <small class="text-danger">{{ $message }}</small>
                    @enderror
                </div>

                <button type="submit" class="btn btn-primary w-100 fw-semibold py-2">
                    <i class="bi bi-save me-1"></i> Save Team
                </button>
            </form>
        </div>
    </div>
</div>
@endsection
