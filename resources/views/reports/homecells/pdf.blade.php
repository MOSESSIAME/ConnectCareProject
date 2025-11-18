<!DOCTYPE html>
<html>
<head>
    <meta charset="utf-8">
    <title>Homecell Reports</title>
    <style>
        body { font-family: DejaVu Sans, Arial, Helvetica, sans-serif; font-size: 12px; }
        h2   { margin: 0 0 6px 0; }
        .muted { color: #666; }
        table { width:100%; border-collapse: collapse; }
        th, td { border:1px solid #777; padding:6px 8px; }
        th { background:#efefef; }
        .right { text-align: right; }
        .nowrap { white-space: nowrap; }
        .filters { margin: 6px 0 12px 0; }
        .filters span { margin-right: 12px; }
    </style>
</head>
<body>
    <h2>Homecell Reports</h2>
    <div class="muted">Generated: {{ now()->format('d M Y H:i') }}</div>

    @if(!empty($filters['church']) || !empty($filters['district']) || !empty($filters['zone']) || !empty($filters['homecell']) || !empty($filters['from']) || !empty($filters['to']) || !empty($filters['q']))
        <div class="filters">
            @if(!empty($filters['church']))   <span><strong>Church:</strong> {{ $filters['church'] }}</span>@endif
            @if(!empty($filters['district'])) <span><strong>District:</strong> {{ $filters['district'] }}</span>@endif
            @if(!empty($filters['zone']))     <span><strong>Zone:</strong> {{ $filters['zone'] }}</span>@endif
            @if(!empty($filters['homecell'])) <span><strong>Homecell:</strong> {{ $filters['homecell'] }}</span>@endif
            @if(!empty($filters['from']))     <span><strong>From:</strong> {{ $filters['from'] }}</span>@endif
            @if(!empty($filters['to']))       <span><strong>To:</strong> {{ $filters['to'] }}</span>@endif
            @if(!empty($filters['q']))        <span><strong>Search:</strong> {{ $filters['q'] }}</span>@endif
        </div>
    @endif

    <table>
        <thead>
            <tr>
                <th class="nowrap">Date</th>
                <th>Church</th>
                <th>District</th>
                <th>Zone</th>
                <th>Homecell</th>
                <th class="right">Males</th>
                <th class="right">Females</th>
                <th class="right">First-timers</th>
                <th class="right">New Converts</th>
                <th>Testimonies</th>
                <th>Submitted By</th>
            </tr>
        </thead>
        <tbody>
            @forelse($reports as $r)
                @php
                    $churchName   = optional($r->church)->name
                                 ?? optional(optional(optional($r->homecell)->zone)->district->church)->name;

                    $districtName = optional($r->district)->name
                                 ?? optional(optional(optional($r->homecell)->zone)->district)->name;

                    $zoneName     = optional($r->zone)->name
                                 ?? optional(optional($r->homecell)->zone)->name;

                    $homecellName = optional($r->homecell)->name;

                    $submittedBy  = optional($r->submittedBy)->name ?? optional($r->user)->name;
                @endphp
                <tr>
                    <td class="nowrap">{{ $r->created_at?->format('d M Y') }}</td>
                    <td>{{ $churchName ?? '—' }}</td>
                    <td>{{ $districtName ?? '—' }}</td>
                    <td>{{ $zoneName ?? '—' }}</td>
                    <td>{{ $homecellName ?? '—' }}</td>
                    <td class="right">{{ $r->males }}</td>
                    <td class="right">{{ $r->females }}</td>
                    <td class="right">{{ $r->first_timers }}</td>
                    <td class="right">{{ $r->new_converts }}</td>
                    <td>{{ \Illuminate\Support\Str::limit($r->testimonies, 120) ?: '—' }}</td>
                    <td>{{ $submittedBy ?? '—' }}</td>
                </tr>
            @empty
                <tr>
                    <td colspan="11" class="right">No reports found for the selected filters.</td>
                </tr>
            @endforelse
        </tbody>
    </table>
</body>
</html>
