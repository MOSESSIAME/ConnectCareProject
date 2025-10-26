@extends('layouts.app')

@section('content')
<div class="container py-4">
    <h3 class="fw-bold text-primary mb-3">
        <i class="bi bi-send me-2"></i> Compose Message
    </h3>

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

    <form action="{{ route('communications.store') }}" method="POST">
        @csrf

        <div class="card border-0 shadow-sm mb-4">
            <div class="card-body row g-3">
                <div class="col-md-6">
                    <label class="form-label">Title</label>
                    <input type="text" name="title" class="form-control" required value="{{ old('title') }}" placeholder="e.g. Appreciation">
                </div>

                <div class="col-md-6">
                    <label class="form-label">Channel</label>
                    <select name="channel" class="form-select" required>
                        <option value="">-- Select Channel --</option>
                        <option value="sms" {{ old('channel')==='sms'?'selected':'' }}>SMS</option>
                        <option value="whatsapp" {{ old('channel')==='whatsapp'?'selected':'' }}>WhatsApp</option>
                        <option value="email" {{ old('channel')==='email'?'selected':'' }}>Email</option>
                    </select>
                </div>

                <div class="col-md-6">
                    <label class="form-label">Audience</label>
                    <select name="audience" id="audienceSelect" class="form-select" required>
                        <option value="all"          {{ old('audience')==='all'?'selected':'' }}>All Members</option>
                        <option value="members"      {{ old('audience')==='members'?'selected':'' }}>Members Only</option>
                        <option value="first_timers" {{ old('audience')==='first_timers'?'selected':'' }}>First Timers</option>
                        <option value="new_converts" {{ old('audience')==='new_converts'?'selected':'' }}>New Converts</option>
                        <option value="single"       {{ old('audience')==='single'?'selected':'' }}>Specific Person</option>
                    </select>
                </div>

                <div class="col-md-6">
                    <label class="form-label">Scheduled At (Optional)</label>
                    <input type="datetime-local" name="scheduled_at" class="form-control" value="{{ old('scheduled_at') }}">
                </div>

                {{-- Specific person --}}
                <div class="col-md-12" id="specificPersonDiv" style="display:none;">
                    <label class="form-label">Send To (one person)</label>
                    <select name="member_id" class="form-select">
                        <option value="">-- Select Member --</option>
                        @foreach($members as $m)
                            <option value="{{ $m->id }}" {{ old('member_id')==$m->id?'selected':'' }}>
                                {{ $m->display_name }} ({{ $m->type }})
                            </option>
                        @endforeach
                    </select>
                </div>

                {{-- Optional date filters (by first-timer / new-convert capture date, etc.) --}}
                <div class="col-md-4">
                    <label class="form-label">From Date (optional)</label>
                    <input type="date" name="filters[from_date]" class="form-control" value="{{ old('filters.from_date') }}">
                </div>
                <div class="col-md-4">
                    <label class="form-label">To Date (optional)</label>
                    <input type="date" name="filters[to_date]" class="form-control" value="{{ old('filters.to_date') }}">
                </div>

                {{-- Optional: pick a template to prefill body --}}
                <div class="col-md-4">
                    <label class="form-label">Load From Template (optional)</label>
                    <select id="templateSelect" class="form-select">
                        <option value="">-- Select Template --</option>
                        @foreach(\App\Models\Template::orderBy('name')->get(['id','name','body']) as $t)
                            <option value="{{ $t->id }}" data-body="{{ htmlspecialchars($t->body) }}">{{ $t->name }}</option>
                        @endforeach
                    </select>
                    <small class="text-muted">Choosing a template will replace the message body.</small>
                </div>

                <div class="col-12">
                    <label class="form-label">Message Body</label>
                    <textarea name="body" id="messageBody" class="form-control" rows="6" required placeholder="Write your message...">{{ old('body') }}</textarea>
                </div>
            </div>
        </div>

        <div class="text-end">
            <a href="{{ route('communications.index') }}" class="btn btn-outline-secondary me-2">
                <i class="bi bi-arrow-left me-1"></i> Back
            </a>
            <button type="submit" class="btn btn-primary">
                <i class="bi bi-send me-1"></i> Queue / Send
            </button>
        </div>
    </form>
</div>

@push('scripts')
<script>
document.addEventListener('DOMContentLoaded', function () {
    const audienceSelect = document.getElementById('audienceSelect');
    const personDiv      = document.getElementById('specificPersonDiv');
    const templateSel    = document.getElementById('templateSelect');
    const bodyEl         = document.getElementById('messageBody');

    function togglePersonSelect() {
        personDiv.style.display = (audienceSelect.value === 'single') ? 'block' : 'none';
    }
    audienceSelect.addEventListener('change', togglePersonSelect);
    togglePersonSelect();

    templateSel?.addEventListener('change', function() {
        const opt = templateSel.options[templateSel.selectedIndex];
        const tpl = opt?.getAttribute('data-body') || '';
        if (tpl) bodyEl.value = tpl;
    });
});
</script>
@endpush
@endsection
