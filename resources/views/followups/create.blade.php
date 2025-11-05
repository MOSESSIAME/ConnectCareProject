@extends('layouts.app')

@section('content')
<div class="container">
    <h3 class="mb-4 text-primary">Log New Follow-up</h3>

    @if ($errors->any())
        <div class="alert alert-danger">
            <strong>There were some problems with your input:</strong>
            <ul class="mt-2 mb-0">
                @foreach ($errors->all() as $error)
                    <li>{{ $error }}</li>
                @endforeach
            </ul>
        </div>
    @endif

    <form action="{{ route('followups.store', $assignment->id) }}" method="POST" class="card p-4 shadow-sm">
        @csrf

        {{-- Method --}}
        <div class="mb-3">
            <label class="form-label fw-semibold">Method</label>
            <select name="method" class="form-select" required>
                <option value="">-- Select Method --</option>
                @foreach($methods as $method)
                    <option value="{{ $method }}" {{ old('method') === $method ? 'selected' : '' }}>
                        {{ $method }}
                    </option>
                @endforeach
            </select>
        </div>

        {{-- Notes --}}
        <div class="mb-3">
            <label class="form-label fw-semibold">Notes</label>
            <textarea name="notes" class="form-control" rows="3" placeholder="Write your follow-up notes...">{{ old('notes') }}</textarea>
        </div>

        {{-- Outcome --}}
        <div class="mb-3">
            <label class="form-label fw-semibold">Outcome</label>
            <select name="outcome" class="form-select" required>
                <option value="">-- Select Outcome --</option>
                @foreach($outcomes as $opt)
                    <option value="{{ $opt }}" {{ old('outcome') === $opt ? 'selected' : '' }}>
                        {{ $opt }}
                    </option>
                @endforeach
            </select>
        </div>

        {{-- Status --}}
        <div class="mb-3">
            <label class="form-label fw-semibold">Status</label>
            <select name="status" class="form-select" required>
                @foreach($statuses as $s)
                    <option value="{{ $s }}" {{ old('status') === $s ? 'selected' : '' }}>
                        {{ $s }}
                    </option>
                @endforeach
            </select>
        </div>

        {{-- Buttons --}}
        <div class="text-end">
            <button type="submit" class="btn btn-success">
                <i class="bi bi-check-circle me-1"></i> Save Follow-up
            </button>
            <a href="{{ route('followups.index') }}" class="btn btn-secondary">
                <i class="bi bi-x-circle me-1"></i> Cancel
            </a>
        </div>
    </form>
</div>
@endsection
