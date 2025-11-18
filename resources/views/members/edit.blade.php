@extends('layouts.app')

@section('content')
<div class="container">
    <h1 class="mb-4">Edit Member</h1>

    {{-- Validation + flash --}}
    @if ($errors->any())
        <div class="alert alert-danger">
            <strong>There were some problems with your input:</strong>
            <ul class="mb-0 mt-2">
                @foreach ($errors->all() as $e)
                    <li>{{ $e }}</li>
                @endforeach
            </ul>
        </div>
    @endif
    @if (session('success'))
        <div class="alert alert-success">{{ session('success') }}</div>
    @endif

    <form action="{{ route('members.update', $member->id) }}" method="POST" class="card p-4 shadow-sm border-0">
        @csrf @method('PUT')

        <div class="row g-3">
            {{-- Full name --}}
            <div class="col-md-6">
                <label for="full_name" class="form-label">Full Name</label>
                <input type="text" name="full_name" id="full_name" class="form-control"
                       value="{{ old('full_name', $member->full_name) }}" required>
            </div>

            {{-- Phone --}}
            <div class="col-md-3">
                <label for="phone" class="form-label">Phone</label>
                <input type="text" name="phone" id="phone" class="form-control"
                       value="{{ old('phone', $member->phone) }}">
            </div>

            {{-- Email --}}
            <div class="col-md-3">
                <label for="email" class="form-label">Email</label>
                <input type="email" name="email" id="email" class="form-control"
                       value="{{ old('email', $member->email) }}">
            </div>

            {{-- Address (NEW) --}}
            <div class="col-12">
                <label for="address" class="form-label">Address</label>
                <input type="text" name="address" id="address" class="form-control"
                       value="{{ old('address', $member->address) }}"
                       placeholder="e.g., Plot 10, Kabulonga, Lusaka">
            </div>

            {{-- Member Type --}}
            <div class="col-md-4">
                <label for="type" class="form-label">Member Type</label>
                <select name="type" id="type" class="form-control" required>
                    <option value="First-timer"     @selected(old('type', $member->type) === 'First-timer')>First-timer</option>
                    <option value="New Convert"     @selected(old('type', $member->type) === 'New Convert')>New Convert</option>
                    <option value="Existing Member" @selected(old('type', $member->type) === 'Existing Member')>Existing Member</option>
                </select>
            </div>

            {{-- From another church --}}
            <div class="col-md-4">
                <label for="from_other_church" class="form-label">From Another Church?</label>
                <select name="from_other_church" id="from_other_church" class="form-control">
                    <option value="0" @selected(old('from_other_church', (int)$member->from_other_church) == 0)>No</option>
                    <option value="1" @selected(old('from_other_church', (int)$member->from_other_church) == 1)>Yes</option>
                </select>
            </div>

            {{-- Foundation class (NEW) --}}
            <div class="col-md-4">
                <label for="foundation_class_completed" class="form-label">Foundation Class</label>
                <select name="foundation_class_completed" id="foundation_class_completed" class="form-control">
                    <option value="0" @selected(old('foundation_class_completed', (int)$member->foundation_class_completed) == 0)>Pending</option>
                    <option value="1" @selected(old('foundation_class_completed', (int)$member->foundation_class_completed) == 1)>Completed</option>
                </select>
            </div>

            {{-- Service Unit (NEW) --}}
            <div class="col-md-6">
                <label for="service_unit_id" class="form-label">Service Unit</label>
                <select name="service_unit_id" id="service_unit_id" class="form-select">
                    <option value="">-- None --</option>
                    @foreach($serviceUnits as $su)
                        <option value="{{ $su->id }}"
                            @selected((string) old('service_unit_id', (string) $member->service_unit_id) === (string) $su->id)>
                            {{ $su->name }}
                        </option>
                    @endforeach
                </select>
            </div>

            {{-- Homecell (NEW) --}}
            <div class="col-md-6">
                <label for="homecell_id" class="form-label">Homecell</label>
                <select name="homecell_id" id="homecell_id" class="form-select">
                    <option value="">-- None --</option>
                    @foreach($homecells as $hc)
                        <option value="{{ $hc->id }}"
                            @selected((string) old('homecell_id', (string) $member->homecell_id) === (string) $hc->id)>
                            {{ $hc->name }}
                        </option>
                    @endforeach
                </select>
            </div>

            {{-- Notes --}}
            <div class="col-12">
                <label for="note" class="form-label">Notes</label>
                <textarea name="note" id="note" class="form-control" rows="3">{{ old('note', $member->note) }}</textarea>
            </div>

            {{-- Actions --}}
            <div class="col-12 text-end">
                <a href="{{ route('members.index') }}" class="btn btn-outline-secondary">Cancel</a>
                <button type="submit" class="btn btn-primary">Update</button>
            </div>
        </div>
    </form>
</div>
@endsection
