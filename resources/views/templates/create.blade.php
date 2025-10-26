@extends('layouts.app')

@section('content')
<div class="container py-4">
    <h2 class="fw-bold text-primary mb-3">
        <i class="bi bi-file-earmark-plus me-2"></i> Create New Message Template
    </h2>

    {{-- Flash messages --}}
    @if(session('success'))
        <div class="alert alert-success">{{ session('success') }}</div>
    @endif

    {{-- Validation errors --}}
    @if($errors->any())
        <div class="alert alert-danger">
            <strong>There were some problems with your input:</strong>
            <ul class="mb-0 mt-2">
                @foreach($errors->all() as $e)
                    <li>{{ $e }}</li>
                @endforeach
            </ul>
        </div>
    @endif

    {{-- Form --}}
    <div class="card shadow-sm border-0">
        <div class="card-body">
            <form action="{{ route('templates.store') }}" method="POST">
                @csrf

                {{-- Template Title (was name) --}}
                <div class="mb-3">
                    <label class="form-label fw-semibold" for="title">Template Title</label>
                    <input
                        type="text"
                        id="name"
                        name="name"
                        class="form-control @error('title') is-invalid @enderror"
                        placeholder="e.g. Sunday Service Reminder"
                        value="{{ old('title') }}"
                        required
                    >
                    @error('name') <div class="invalid-feedback">{{ $message }}</div> @enderror
                </div>

                {{-- Channel --}}
                <div class="mb-3">
                    <label class="form-label fw-semibold" for="channel">Channel</label>
                    <select name="channel" id="channel" class="form-select @error('channel') is-invalid @enderror" required>
                        <option value="">-- Select Channel --</option>
                        <option value="sms" {{ old('channel') === 'sms' ? 'selected' : '' }}>SMS</option>
                        <option value="whatsapp" {{ old('channel') === 'whatsapp' ? 'selected' : '' }}>WhatsApp</option>
                        <option value="email" {{ old('channel') === 'email' ? 'selected' : '' }}>Email</option>
                    </select>
                    @error('channel') <div class="invalid-feedback">{{ $message }}</div> @enderror
                    <small class="text-muted d-block mt-1">The subject is ignored for SMS templates.</small>
                </div>

                {{-- Subject --}}
                <div class="mb-3" id="subjectWrap">
                    <label class="form-label fw-semibold" for="subject">Subject (for Email or WhatsApp)</label>
                    <input
                        type="text"
                        name="subject"
                        id="subject"
                        class="form-control @error('subject') is-invalid @enderror"
                        placeholder="e.g. Join us for Sunday Celebration Service"
                        value="{{ old('subject') }}"
                    >
                    @error('subject') <div class="invalid-feedback">{{ $message }}</div> @enderror
                </div>

                {{-- Body --}}
                <div class="mb-3">
                    <label class="form-label fw-semibold" for="body">Message Body</label>
                    <textarea
                        name="body"
                        id="body"
                        class="form-control @error('body') is-invalid @enderror"
                        rows="6"
                        placeholder="Write your message..."
                        required
                    >{{ old('body') }}</textarea>
                    @error('body') <div class="invalid-feedback">{{ $message }}</div> @enderror
                    <small class="text-muted">
                        You can use placeholders like <code>{first_name}</code>, <code>{service_date}</code>, etc.
                    </small>
                </div>

                {{-- Active toggle --}}
                <div class="form-check mb-3">
                    <input
                        type="checkbox"
                        class="form-check-input"
                        id="is_active"
                        name="is_active"
                        value="1"
                        {{ old('is_active', true) ? 'checked' : '' }}
                    >
                    <label for="is_active" class="form-check-label fw-semibold">Mark as Active</label>
                </div>

                {{-- Submit --}}
                <div class="d-flex justify-content-end gap-2">
                    <a href="{{ route('templates.index') }}" class="btn btn-outline-secondary">
                        <i class="bi bi-arrow-left me-1"></i> Back
                    </a>
                    <button type="submit" class="btn btn-primary">
                        <i class="bi bi-save me-1"></i> Save Template
                    </button>
                </div>
            </form>
        </div>
    </div>
</div>

@push('scripts')
<script>
    // Hide subject when SMS is selected
    const channel = document.getElementById('channel');
    const subjectWrap = document.getElementById('subjectWrap');
    function toggleSubject() {
        subjectWrap.style.display = (channel.value === 'sms') ? 'none' : '';
    }
    channel.addEventListener('change', toggleSubject);
    toggleSubject();
</script>
@endpush
@endsection
