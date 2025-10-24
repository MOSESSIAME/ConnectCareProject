@extends('layouts.app')

@section('content')
<div class="container">
    <h2 class="fw-bold mb-4">{{ $item->title }}</h2>

    <div class="card shadow-sm">
        <div class="card-body">
            <p><strong>Channel:</strong> <span class="text-uppercase">{{ $item->channel }}</span></p>
            <p><strong>Audience:</strong> {{ Str::headline($item->audience) }}</p>
            @if($item->filters)
                <p><strong>Filters:</strong> <code>{{ json_encode($item->filters) }}</code></p>
            @endif
            <p><strong>Status:</strong> {{ $item->status }} @if($item->sent_at) ({{ $item->sent_at->format('d M Y H:i') }}) @endif</p>
            <hr>
            <pre class="mb-0">{{ $item->body }}</pre>
        </div>
    </div>

    <div class="mt-3">
        <a href="{{ route('communications.index') }}" class="btn btn-secondary">Back</a>
    </div>
</div>
@endsection
