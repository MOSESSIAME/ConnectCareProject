@extends('layouts.app')

@section('content')
<div class="container">
    <h3 class="mb-4 text-primary">Log New Follow-up</h3>

    <form action="{{ route('followups.store', $assignment->id) }}" method="POST" class="card p-4 shadow-sm">
        @csrf

        <div class="mb-3">
            <label class="form-label fw-semibold">Method</label>
            <select name="method" class="form-select" required>
                <option value="Call">Call</option>
                <option value="Visit">Visit</option>
                <option value="SMS">SMS</option>
                <option value="WhatsApp">WhatsApp</option>
                <option value="Email">Email</option>
                <option value="Meeting">Meeting</option>
            </select>
        </div>

        <div class="mb-3">
            <label class="form-label fw-semibold">Notes</label>
            <textarea name="notes" class="form-control" rows="3" placeholder="Write your follow-up notes..."></textarea>
        </div>

        <div class="mb-3">
            <label class="form-label fw-semibold">Outcome</label>
            <input type="text" name="outcome" class="form-control" placeholder="Outcome of the follow-up">
        </div>

        <div class="mb-3">
            <label class="form-label fw-semibold">Status</label>
            <select name="status" class="form-select">
                <option value="Pending">Pending</option>
                <option value="In Progress">In Progress</option>
                <option value="Completed">Completed</option>
            </select>
        </div>

        <div class="text-end">
            <button type="submit" class="btn btn-success">Save Follow-up</button>
            <a href="{{ route('followups.index', $assignment->id) }}" class="btn btn-secondary">Cancel</a>
        </div>
    </form>
</div>
@endsection
