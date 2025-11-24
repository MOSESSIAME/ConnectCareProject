@extends('layouts.app')

@section('content')
<div class="container py-4">
    <h2 class="mb-3">Edit Follow-up</h2>

    <form action="{{ route('followups.update', $followup->id) }}" method="POST" class="card shadow-sm">
        @csrf
        @method('PUT')

        <div class="card-body">
            <div class="mb-3">
                <label class="form-label">Member</label>
                <div class="form-control-plaintext">{{ $followup->assignment->member->full_name ?? 'N/A' }}</div>
            </div>

            <div class="mb-3">
                <label class="form-label">Method</label>
                <select name="method" class="form-select" required>
                    @foreach($methods as $m)
                        <option value="{{ $m }}" @selected($followup->method === $m)>{{ $m }}</option>
                    @endforeach
                </select>
            </div>

            <div class="mb-3">
                <label class="form-label">Outcome</label>
                <select name="outcome" class="form-select" required>
                    @foreach($outcomes as $o)
                        <option value="{{ $o }}" @selected($followup->outcome === $o)>{{ $o }}</option>
                    @endforeach
                </select>
            </div>

            <div class="mb-3">
                <label class="form-label">Status</label>
                <select name="status" class="form-select" required>
                    @foreach($statuses as $s)
                        <option value="{{ $s }}" @selected($followup->status === $s)>{{ $s }}</option>
                    @endforeach
                </select>
            </div>

            <div class="mb-3">
                <label class="form-label">Notes</label>
                <textarea name="notes" class="form-control" rows="4">{{ old('notes', $followup->notes) }}</textarea>
            </div>

            <div class="d-flex gap-2">
                <button class="btn btn-primary">Save changes</button>
                <a href="{{ route('followups.assignment', $followup->assignment_id) }}" class="btn btn-outline-secondary">Cancel</a>
            </div>
        </div>
    </form>
</div>
@endsection
