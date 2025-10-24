@extends('layouts.app')

@section('content')
<div class="container py-4">
    <h3 class="fw-bold text-primary mb-3">
        <i class="bi bi-chat-dots me-2"></i> New Communication
    </h3>

    <form action="{{ route('communications.store') }}" method="POST">
        @csrf

        {{-- Basic Details --}}
        <div class="card border-0 shadow-sm mb-4">
            <div class="card-body row g-3">
                <div class="col-md-6">
                    <label class="form-label">Title</label>
                    <input type="text" name="title" class="form-control" required value="{{ old('title') }}">
                </div>

                <div class="col-md-6">
                    <label class="form-label">Channel</label>
                    <select name="channel" class="form-select" required>
                        <option value="sms" {{ old('channel')==='sms' ? 'selected' : '' }}>SMS</option>
                        <option value="whatsapp" {{ old('channel')==='whatsapp' ? 'selected' : '' }}>WhatsApp</option>
                        <option value="email" {{ old('channel')==='email' ? 'selected' : '' }}>Email</option>
                    </select>
                </div>

                <div class="col-md-12">
                    <label class="form-label">Message Body</label>
                    <textarea name="body" class="form-control" rows="4" required>{{ old('body') }}</textarea>
                </div>

                <div class="col-md-6">
                    <label class="form-label">Audience</label>
                    <select name="audience" class="form-select" required id="audienceSelect">
                        <option value="all"          {{ old('audience')==='all' ? 'selected' : '' }}>All Members</option>
                        <option value="members"      {{ old('audience')==='members' ? 'selected' : '' }}>Members Only</option>
                        <option value="first_timers" {{ old('audience')==='first_timers' ? 'selected' : '' }}>First Timers</option>
                        <option value="new_converts" {{ old('audience')==='new_converts' ? 'selected' : '' }}>New Converts</option>
                        <option value="single"       {{ old('audience')==='single' ? 'selected' : '' }}>Specific Person</option>
                    </select>
                </div>

                <div class="col-md-6">
                    <label class="form-label">Scheduled At (Optional)</label>
                    <input type="datetime-local" name="scheduled_at" class="form-control" value="{{ old('scheduled_at') }}">
                </div>
            </div>
        </div>

        {{-- Filter Section --}}
        <div class="card border-0 shadow-sm mb-4">
            <div class="card-header bg-light fw-semibold">Filter Recipients (Optional)</div>
            <div class="card-body row g-3">
                <div class="col-md-4">
                    <label class="form-label">From Date</label>
                    <input type="date" name="filters[from_date]" class="form-control" value="{{ old('filters.from_date') }}">
                </div>
                <div class="col-md-4">
                    <label class="form-label">To Date</label>
                    <input type="date" name="filters[to_date]" class="form-control" value="{{ old('filters.to_date') }}">
                </div>

                {{-- Only visible when "Specific Person" is chosen --}}
                <div class="col-md-4" id="specificPersonDiv" style="display: none;">
                    <label class="form-label">Select Person</label>
                    <select name="member_id" class="form-select">
                        <option value="">-- Select Member --</option>
                        @foreach($members as $m)
                            <option value="{{ $m->id }}" {{ (string)old('member_id') === (string)$m->id ? 'selected' : '' }}>
                                {{ $m->display_name }} @if(!empty($m->type)) ({{ ucfirst($m->type) }}) @endif
                            </option>
                        @endforeach
                    </select>
                    <small class="text-muted">Required when Audience = Specific Person</small>
                </div>
            </div>
        </div>

        <div class="text-end">
            <button type="submit" class="btn btn-primary">
                <i class="bi bi-send me-1"></i> Send / Queue Message
            </button>
        </div>
    </form>
</div>

{{-- Show/Hide person selector when Audience is "single" --}}
<script>
    document.addEventListener('DOMContentLoaded', function () {
        const audienceSelect = document.getElementById('audienceSelect');
        const personDiv = document.getElementById('specificPersonDiv');

        function togglePersonSelect() {
            personDiv.style.display = (audienceSelect.value === 'single') ? 'block' : 'none';
        }

        audienceSelect.addEventListener('change', togglePersonSelect);
        togglePersonSelect();
    });
</script>
@endsection
