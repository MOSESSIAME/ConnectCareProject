<!DOCTYPE html>
<html>
<head>
    <meta charset="utf-8">
    <title>Members Export</title>
    <style>
        /* Landscape A4 */
        @page { size: A4 landscape; margin: 12mm 10mm; }
        body { font-family: DejaVu Sans, Arial, Helvetica, sans-serif; font-size: 10px; color: #222; }

        h2 { margin: 0 0 6px; font-size: 15px; }
        .meta { margin-bottom: 8px; color: #666; font-size: 9px; }

        /* Make columns stable and controllable */
        table { width: 100%; border-collapse: collapse; table-layout: fixed; }
        thead th {
            background: #f3f3f3;
            font-weight: 700;
            padding: 5px 6px;
            border: 1px solid #ddd;
            font-size: 9px;
            vertical-align: middle;
        }
        tbody td {
            border: 1px solid #ddd;
            padding: 5px 6px;
            font-size: 9px;
            vertical-align: middle;
            /* allow wrapping for long content */
            white-space: normal;
            word-wrap: break-word;
            overflow-wrap: break-word;
        }

        /* column widths (tune to taste) */
        th.col-index, td.col-index { width: 3%; text-align: center; }
        th.col-name, td.col-name { width: 18%; }
        th.col-gender, td.col-gender { width: 4%; text-align: center; }
        th.col-type, td.col-type { width: 12%; }
        th.col-phone, td.col-phone { width: 9%; }
        th.col-email, td.col-email { width: 16%; }
        th.col-address, td.col-address { width: 12%; }
        th.col-service, td.col-service { width: 6%; }
        th.col-homecell, td.col-homecell { width: 6%; }
        th.col-foundation, td.col-foundation { width: 6%; text-align: center; }
        th.col-created, td.col-created { width: 6%; text-align: center; }

        /* alternate row background (optional) */
        tbody tr:nth-child(even) { background: #fbfbfb; }

        .text-center { text-align: center; }
        .small { font-size: 8.5px; color:#666; }
    </style>
</head>
<body>
    <h2>Members Export</h2>

    <div class="meta small">
        Exported: {{ $exportedAt->format('d M Y H:i') }} &nbsp; &nbsp;
        Filters: {{ collect($filters)->filter()->map(fn($v,$k)=>"$k=$v")->implode(', ') ?: 'None' }}
    </div>

    <table>
        <thead>
            <tr>
                <th class="col-index">#</th>
                <th class="col-name">Name</th>
                <th class="col-gender">Gender</th>
                <th class="col-type">Type</th>
                <th class="col-phone">Phone</th>
                <th class="col-email">Email</th>
                <th class="col-address">Address</th>
                <th class="col-service">Service Unit</th>
                <th class="col-homecell">Homecell</th>
                <th class="col-foundation">Foundation</th>
                <th class="col-created">Created</th>
            </tr>
        </thead>
        <tbody>
            @foreach($rows as $i => $m)
                <tr>
                    <td class="col-index text-center">{{ $i + 1 }}</td>

                    <td class="col-name">{{ $m->full_name }}</td>

                    <td class="col-gender text-center">{{ $m->gender ?? '–' }}</td>

                    <td class="col-type">{{ $m->type }}</td>

                    <td class="col-phone">{{ $m->phone ?? '–' }}</td>

                    <td class="col-email">{{ $m->email ?? '–' }}</td>

                    <td class="col-address">{{ $m->address ?? 'N/A' }}</td>

                    <td class="col-service">{{ optional($m->serviceUnit)->name ?? 'N/A' }}</td>

                    <td class="col-homecell">{{ optional($m->homecell)->name ?? 'N/A' }}</td>

                    <td class="col-foundation text-center">{{ $m->foundation_class_completed ? 'Completed' : 'Pending' }}</td>

                    <td class="col-created text-center">{{ optional($m->created_at)?->format('Y-m-d') ?? '–' }}</td>
                </tr>
            @endforeach
        </tbody>
    </table>
</body>
</html>
