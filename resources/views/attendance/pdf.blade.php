<!DOCTYPE html>
<html>
<head>
    <meta charset="utf-8">
    <title>Service Attendance - PDF</title>
    <style>
        body { font-family: DejaVu Sans, Arial, Helvetica, sans-serif; font-size: 12px; color:#222; }
        h2 { margin: 0 0 8px; }
        .muted { color: #666; font-size: 11px; }
        table { width:100%; border-collapse: collapse; margin-top: 10px; }
        th, td { border: 1px solid #ddd; padding: 6px 8px; }
        th { background: #f1f1f1; text-align: left; }
        .text-right { text-align: right; }
        .mb-8 { margin-bottom: 8px; }
        .summary { margin-top: 10px; }
        .summary td { border: none; padding: 3px 6px; }
    </style>
</head>
<body>
    <h2>Service Attendance</h2>
    <div class="muted mb-8">
        Generated: {{ now()->format('d M Y H:i') }}<br>
        @if($filters['service']) Service: {{ $filters['service'] }} | @endif
        @if($filters['from_date']) From: {{ $filters['from_date'] }} | @endif
        @if($filters['to_date']) To: {{ $filters['to_date'] }} @endif
    </div>

    <table>
        <thead>
            <tr>
                <th>#</th>
                <th>Service</th>
                <th>Date</th>
                <th class="text-right">Males</th>
                <th class="text-right">Females</th>
                <th class="text-right">Children</th>
                <th class="text-right">First-Timers</th>
                <th class="text-right">New Converts</th>
                <th class="text-right">Offering (ZMW)</th>
                <th>Notes</th>
            </tr>
        </thead>
        <tbody>
            @foreach($rows as $i => $r)
                <tr>
                    <td>{{ $i + 1 }}</td>
                    <td>{{ $r->service->name ?? 'N/A' }}</td>
                    <td>
                        @php
                            $d = optional($r->service)->service_date;
                        @endphp
                        {{ $d ? \Carbon\Carbon::parse($d)->format('d M Y') : 'N/A' }}
                    </td>
                    <td class="text-right">{{ $r->males }}</td>
                    <td class="text-right">{{ $r->females }}</td>
                    <td class="text-right">{{ $r->children }}</td>
                    <td class="text-right">{{ $r->first_timers }}</td>
                    <td class="text-right">{{ $r->new_converts }}</td>
                    <td class="text-right">{{ number_format($r->offering, 2) }}</td>
                    <td>{{ $r->notes ?? '–' }}</td>
                </tr>
            @endforeach
        </tbody>
    </table>

</body>
</html>
