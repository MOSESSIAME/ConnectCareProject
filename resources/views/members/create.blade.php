@extends('layouts.app')

@section('content')
<div class="container">
    <h1 class="mb-4">Register New Member</h1>

    <form action="{{ route('members.store') }}" method="POST">
        @csrf

        <div class="mb-3">
            <label for="full_name" class="form-label">Full Name</label>
            <input type="text" name="full_name" class="form-control" required>
        </div>

        <div class="mb-3">
            <label for="phone" class="form-label">Phone</label>
            <input type="text" name="phone" class="form-control">
        </div>

        <div class="mb-3">
            <label for="email" class="form-label">Email</label>
            <input type="email" name="email" class="form-control">
        </div>

        <div class="mb-3">
            <label for="type" class="form-label">Member Type</label>
            <select name="type" class="form-control" required>
                <option value="First-timer">First-timer</option>
                <option value="New Convert">New Convert</option>
                <option value="Existing Member">Existing Member</option>
            </select>
        </div>

        <div class="mb-3">
            <label class="form-label">From Another Church?</label>
            <select name="from_other_church" class="form-control">
                <option value="0">No</option>
                <option value="1">Yes</option>
            </select>
        </div>

        <div class="mb-3">
            <label for="note" class="form-label">Notes</label>
            <textarea name="note" class="form-control"></textarea>
        </div>

        <button type="submit" class="btn btn-success">Save</button>
        <a href="{{ route('members.index') }}" class="btn btn-secondary">Cancel</a>
    </form>
</div>
@endsection
