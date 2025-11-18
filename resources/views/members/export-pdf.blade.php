<!DOCTYPE html>
<html>
<head>
    <meta charset="utf-8">
    <style>
        body { font-family: DejaVu Sans, Arial, sans-serif; font-size: 12px; }
        h2 { margin: 0 0 10px; }
        table { width: 100%; border-collapse: collapse; }
        th, td { border: 1px solid #bbb; padding: 6px; text-align: left; }
        thead { background: #eee; }
        .meta { margin-bottom: 8px; color: #666; font-size: 11px; }
    </style>
</head>
<body>
    <h2>Members Export</h2>
    <div class="meta">
        Exported: {{ $exportedAt->format('d M Y H:i') }}<br>
        Filters: {{ collect($filters)->filter()->map(fn($v,$k)=>"$k=$v")->implode(', ') ?: 'None' }}
    </div>

    <table>
        <thead>
            <tr>
                <th>#</th>
                <th>Name</th>
                <th>Type</th>
                <th>Phone</th>
                <th>Email</th>
                <th>Address</th>
                <th>Service Unit</th>
                <th>Homecell</th>
                <th>Foundation</th>
                <th>Created</th>
            </tr>
        </thead>
        <tbody>
            @foreach($rows as $i => $m)
                <tr>
                    <td>{{ $i+1 }}</td>
                    <td>{{ $m->full_name }}</td>
                    <td>{{ $m->type }}</td>
                    <td>{{ $m->phone }}</td>
                    <td>{{ $m->email }}</td>
                    <td>{{ $m->address ?? 'N/A' }}</td>
                    <td>{{ optional($m->serviceUnit)->name ?? 'N/A' }}</td>
                    <td>{{ optional($m->homecell)->name ?? 'N/A' }}</td>
                    <td>{{ $m->foundation_class_completed ? 'Completed' : 'Pending' }}</td>
                    <td>{{ optional($m->created_at)?->format('Y-m-d') }}</td>
                </tr>
            @endforeach
        </tbody>
    </table>
</body>
</html>
