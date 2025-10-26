@extends('layouts.app')

@section('content')
<div class="container py-4">
    <h2 class="fw-bold text-primary mb-4">
        <i class="bi bi-pencil-square me-2"></i> Edit Template
    </h2>

    <div class="card shadow-sm border-0">
        <div class="card-body">
            <form action="{{ route('templates.update', $template) }}" method="POST">
                @csrf
                @method('PUT')

                <div class="mb-3">
                    <label class="form-label fw-semibold">Title <span class="text-danger">*</span></label>
                    <input type="text" name="name" class="form-control @error('title') is-invalid @enderror"
                           value="{{ old('title', $template->title) }}" required>
                    @error('name') <div class="invalid-feedback">{{ $message }}</div> @enderror
                </div>

                <div class="row">
                    <div class="col-md-6 mb-3">
                        <label class="form-label fw-semibold">Channel <span class="text-danger">*</span></label>
                        <select name="channel" class="form-select @error('channel') is-invalid @enderror" required>
                            <option value="">-- Select Channel --</option>
                            <option value="sms" {{ old('channel', $template->channel) == 'sms' ? 'selected' : '' }}>SMS</option>
                            <option value="email" {{ old('channel', $template->channel) == 'email' ? 'selected' : '' }}>Email</option>
                            <option value="whatsapp" {{ old('channel', $template->channel) == 'whatsapp' ? 'selected' : '' }}>WhatsApp</option>
                        </select>
                        @error('channel') <div class="invalid-feedback">{{ $message }}</div> @enderror
                    </div>

                    <div class="col-md-6 mb-3">
                        <label class="form-label fw-semibold">Category</label>
                        <input type="text" name="category" class="form-control @error('category') is-invalid @enderror"
                               value="{{ old('category', $template->category) }}" placeholder="e.g. Follow-up, Reminder">
                        @error('category') <div class="invalid-feedback">{{ $message }}</div> @enderror
                    </div>
                </div>

                <div class="mb-3">
                    <label class="form-label fw-semibold">Message Body <span class="text-danger">*</span></label>
                    <textarea name="body" rows="6" class="form-control @error('body') is-invalid @enderror" required>{{ old('body', $template->body) }}</textarea>
                    @error('body') <div class="invalid-feedback">{{ $message }}</div> @enderror
                </div>

                <div class="d-flex justify-content-between align-items-center">
                    <a href="{{ route('templates.index') }}" class="btn btn-secondary">
                        <i class="bi bi-arrow-left me-1"></i> Back
                    </a>
                    <button type="submit" class="btn btn-primary">
                        <i class="bi bi-save me-1"></i> Update Template
                    </button>
                </div>
            </form>
        </div>
    </div>
</div>
@endsection
