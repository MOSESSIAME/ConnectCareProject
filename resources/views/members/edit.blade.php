@extends('layouts.app')

@section('content')
<div class="container">
    <h1 class="mb-4">Edit Member</h1>

    <form action="{{ route('members.update', $member->id) }}" method="POST">
        @csrf @method('PUT')

        <div class="mb-3">
            <label for="full_name" class="form-label">Full Name</label>
            <input type="text" name="full_name" class="form-control" value="{{ $member->full_name }}" required>
        </div>

        <div class="mb-3">
            <label for="phone" class="form-label">Phone</label>
            <input type="text" name="phone" class="form-control" value="{{ $member->phone }}">
        </div>

        <div class="mb-3">
            <label for="email" class="form-label">Email</label>
            <input type="email" name="email" class="form-control" value="{{ $member->email }}">
        </div>

        <div class="mb-3">
            <label for="type" class="form-label">Member Type</label>
            <select name="type" class="form-control" required>
                <option value="First-timer" {{ $member->type == 'First-timer' ? 'selected' : '' }}>First-timer</option>
                <option value="New Convert" {{ $member->type == 'New Convert' ? 'selected' : '' }}>New Convert</option>
                <option value="Existing Member" {{ $member->type == 'Existing Member' ? 'selected' : '' }}>Existing Member</option>
            </select>
        </div>

        <div class="mb-3">
            <label class="form-label">From Another Church?</label>
            <select name="from_other_church" class="form-control">
                <option value="0" {{ !$member->from_other_church ? 'selected' : '' }}>No</option>
                <option value="1" {{ $member->from_other_church ? 'selected' : '' }}>Yes</option>
            </select>
        </div>

        <div class="mb-3">
            <label for="note" class="form-label">Notes</label>
            <textarea name="note" class="form-control">{{ $member->note }}</textarea>
        </div>

        <button type="submit" class="btn btn-primary">Update</button>
        <a href="{{ route('members.index') }}" class="btn btn-secondary">Cancel</a>
    </form>
</div>
@endsection
