@extends('layouts.app')

@section('content')
<div class="container">
    <h2 class="mb-4 fw-bold">Homecell Reports</h2>

    {{-- Flash --}}
    @if(session('success'))
        <div class="alert alert-success">{{ session('success') }}</div>
    @endif

    {{-- Filters --}}
    <form method="GET" action="{{ route('reports.homecells.index') }}" class="mb-4">
        <div class="row align-items-end g-3">
            {{-- Church --}}
            <div class="col-md-3">
                <label for="church_id" class="form-label">Church</label>
                <select name="church_id" id="church_id" class="form-control">
                    <option value="">-- All Churches --</option>
                    @foreach($churches as $church)
                        <option value="{{ $church->id }}" {{ request('church_id') == $church->id ? 'selected' : '' }}>
                            {{ $church->name }}
                        </option>
                    @endforeach
                </select>
            </div>

            {{-- District --}}
            <div class="col-md-3">
                <label for="district_id" class="form-label">District</label>
                <select name="district_id" id="district_id" class="form-control">
                    <option value="">-- All Districts --</option>
                </select>
            </div>

            {{-- Zone --}}
            <div class="col-md-3">
                <label for="zone_id" class="form-label">Zone</label>
                <select name="zone_id" id="zone_id" class="form-control">
                    <option value="">-- All Zones --</option>
                </select>
            </div>

            {{-- Homecell --}}
            <div class="col-md-3">
                <label for="homecell_id" class="form-label">Homecell</label>
                <select name="homecell_id" id="homecell_id" class="form-control">
                    <option value="">-- All Homecells --</option>
                </select>
            </div>

            <div class="col-12 text-end">
                <button type="submit" class="btn btn-primary">Filter</button>
                <a href="{{ route('reports.homecells.index') }}" class="btn btn-secondary">Reset</a>
                <a href="{{ route('reports.homecells.create') }}" class="btn btn-success">+ Submit Report</a>
            </div>
        </div>
    </form>

    {{-- Table --}}
    <div class="table-responsive">
        <table class="table table-bordered table-striped align-middle bg-white">
            <thead class="table-light">
                <tr>
                    <th style="white-space:nowrap;">Date</th>
                    <th>Church</th>
                    <th>District</th>
                    <th>Zone</th>
                    <th>Homecell</th>
                    <th class="text-end">Males</th>
                    <th class="text-end">Females</th>
                    <th class="text-end">First-timers</th>
                    <th class="text-end">New Converts</th>
                    <th>Testimonies</th>
                    <th>Submitted By</th>
                </tr>
            </thead>
            <tbody>
                @forelse($reports as $r)
                    @php
                        // Robust labels: if direct FK is null, infer via homecell -> zone -> district -> church
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
                        <td style="white-space:nowrap;">{{ $r->created_at?->format('d M Y') }}</td>
                        <td>{{ $churchName ?? '—' }}</td>
                        <td>{{ $districtName ?? '—' }}</td>
                        <td>{{ $zoneName ?? '—' }}</td>
                        <td>{{ $homecellName ?? '—' }}</td>
                        <td class="text-end">{{ $r->males }}</td>
                        <td class="text-end">{{ $r->females }}</td>
                        <td class="text-end">{{ $r->first_timers }}</td>
                        <td class="text-end">{{ $r->new_converts }}</td>
                        <td>{{ \Illuminate\Support\Str::limit($r->testimonies, 60) ?: '—' }}</td>
                        <td>{{ $submittedBy ?? '—' }}</td>
                    </tr>
                @empty
                    <tr>
                        <td colspan="11" class="text-center text-muted">No reports found.</td>
                    </tr>
                @endforelse
            </tbody>
        </table>
    </div>

    <div class="mt-3">
        {{ $reports->links() }}
    </div>
</div>

{{-- jQuery --}}
<script src="https://code.jquery.com/jquery-3.6.0.min.js"></script>

{{-- Cascading dropdowns --}}
<script>
$(function () {
    const oldChurch   = '{{ request('church_id') }}';
    const oldDistrict = '{{ request('district_id') }}';
    const oldZone     = '{{ request('zone_id') }}';
    const oldHomecell = '{{ request('homecell_id') }}';

    function fillSelect($el, placeholder, items) {
        $el.html(`<option value="">${placeholder}</option>`);
        if (Array.isArray(items)) {
            items.forEach(it => $el.append(`<option value="${it.id}">${it.name}</option>`));
        }
    }

    // On Church change -> load districts
    $('#church_id').on('change', function () {
        const churchId = $(this).val();
        fillSelect($('#district_id'), '-- All Districts --');
        fillSelect($('#zone_id'),     '-- All Zones --');
        fillSelect($('#homecell_id'), '-- All Homecells --');

        if (!churchId) return;

        $.get(`{{ url('get-districts') }}/${churchId}`)
            .done(data => {
                fillSelect($('#district_id'), '-- All Districts --', data);
                if (oldDistrict) $('#district_id').val(oldDistrict).trigger('change');
            });
    }).trigger('change');

    // On District change -> load zones
    $('#district_id').on('change', function () {
        const districtId = $(this).val();
        fillSelect($('#zone_id'),     '-- All Zones --');
        fillSelect($('#homecell_id'), '-- All Homecells --');

        if (!districtId) return;

        $.get(`{{ url('get-zones') }}/${districtId}`)
            .done(data => {
                fillSelect($('#zone_id'), '-- All Zones --', data);
                if (oldZone) $('#zone_id').val(oldZone).trigger('change');
            });
    });

    // On Zone change -> load homecells
    $('#zone_id').on('change', function () {
        const zoneId = $(this).val();
        fillSelect($('#homecell_id'), '-- All Homecells --');

        if (!zoneId) return;

        $.get(`{{ url('get-homecells') }}/${zoneId}`)
            .done(data => {
                fillSelect($('#homecell_id'), '-- All Homecells --', data);
                if (oldHomecell) $('#homecell_id').val(oldHomecell);
            });
    });
});
</script>
@endsection
