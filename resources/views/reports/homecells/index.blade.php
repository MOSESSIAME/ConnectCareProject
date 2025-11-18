@extends('layouts.app')

@section('content')
<div class="container">
    <h2 class="mb-4 fw-bold">Homecell Reports</h2>

    {{-- Flash --}}
    @if(session('success'))
        <div class="alert alert-success">{{ session('success') }}</div>
    @endif

    {{-- Filters --}}
    <form method="GET" action="{{ route('reports.homecells.index') }}" class="card p-3 mb-3 shadow-sm border-0">
        <div class="row g-3 align-items-end">

            {{-- Church --}}
            <div class="col-md-3">
                <label for="church_id" class="form-label">Church</label>
                <select name="church_id" id="church_id" class="form-control" data-old="{{ request('church_id') }}">
                    <option value="">-- All Churches --</option>
                    @foreach($churches as $church)
                        <option value="{{ $church->id }}" {{ (string)request('church_id')===(string)$church->id ? 'selected' : '' }}>
                            {{ $church->name }}
                        </option>
                    @endforeach
                </select>
            </div>

            {{-- District --}}
            <div class="col-md-3">
                <label for="district_id" class="form-label">District</label>
                <select name="district_id" id="district_id" class="form-control" data-old="{{ request('district_id') }}">
                    <option value="">-- All Districts --</option>
                </select>
            </div>

            {{-- Zone --}}
            <div class="col-md-3">
                <label for="zone_id" class="form-label">Zone</label>
                <select name="zone_id" id="zone_id" class="form-control" data-old="{{ request('zone_id') }}">
                    <option value="">-- All Zones --</option>
                </select>
            </div>

            {{-- Homecell --}}
            <div class="col-md-3">
                <label for="homecell_id" class="form-label">Homecell</label>
                <select name="homecell_id" id="homecell_id" class="form-control" data-old="{{ request('homecell_id') }}">
                    <option value="">-- All Homecells --</option>
                </select>
            </div>

            {{-- From/To --}}
            <div class="col-md-2">
                <label class="form-label">From</label>
                <input type="date" name="from" value="{{ request('from') }}" class="form-control">
            </div>
            <div class="col-md-2">
                <label class="form-label">To</label>
                <input type="date" name="to" value="{{ request('to') }}" class="form-control">
            </div>

            {{-- Search --}}
            <div class="col-md-4">
                <label class="form-label">Search (testimonies or submitted by)</label>
                <input type="text" name="q" value="{{ request('q') }}" class="form-control" placeholder="e.g. healing, John ...">
            </div>

            {{-- Actions (smaller buttons) --}}
            <div class="col-md-4 text-end">
                <a href="{{ route('reports.homecells.index') }}" class="btn btn-outline-secondary btn-sm">Reset</a>
                <button class="btn btn-primary btn-sm">Filter</button>
                <a href="{{ route('reports.homecells.create') }}" class="btn btn-success btn-sm">+ Submit Report</a>
                <a href="{{ route('reports.homecells.export.pdf', request()->query()) }}"
                   class="btn btn-outline-danger btn-sm">
                    <i class="bi bi-filetype-pdf me-1"></i> Export PDF
                </a>
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
  const $church   = $('#church_id');
  const $district = $('#district_id');
  const $zone     = $('#zone_id');
  const $homecell = $('#homecell_id');

  const ENDPOINTS = {
    districts: '{{ url('/get-districts') }}/',
    zones:     '{{ url('/get-zones') }}/',
    homecells: '{{ url('/get-homecells') }}/'
  };

  function fill($el, placeholder, items) {
    $el.html(`<option value="">${placeholder}</option>`);
    (items || []).forEach(it => $el.append(`<option value="${it.id}">${it.name}</option>`));
    if (!items || items.length === 0) {
      $el.append('<option value="" disabled>(No records found)</option>');
    }
  }

  function ajaxJSON(url, ok) {
    $.ajax({
      url,
      method: 'GET',
      headers: { 'Accept': 'application/json' },
      dataType: 'json'
    })
    .done(resp => {
      const data = Array.isArray(resp) ? resp : (resp && Array.isArray(resp.data) ? resp.data : []);
      ok(data);
    })
    .fail(xhr => { console.error('[Filter] Error', xhr.status, xhr.responseText); ok([]); });
  }

  // Church → Districts
  $church.on('change', function () {
    const id = $(this).val();
    fill($district, '-- All Districts --', []);
    fill($zone,     '-- All Zones --', []);
    fill($homecell, '-- All Homecells --', []);
    if (!id) return;

    ajaxJSON(ENDPOINTS.districts + encodeURIComponent(id), (data) => {
      fill($district, '-- All Districts --', data);
      const old = $district.data('old');
      if (old) { $district.val(old).trigger('change'); $district.data('old',''); }
    });
  });

  // District → Zones
  $district.on('change', function () {
    const id = $(this).val();
    fill($zone,     '-- All Zones --', []);
    fill($homecell, '-- All Homecells --', []);
    if (!id) return;

    ajaxJSON(ENDPOINTS.zones + encodeURIComponent(id), (data) => {
      fill($zone, '-- All Zones --', data);
      const old = $zone.data('old');
      if (old) { $zone.val(old).trigger('change'); $zone.data('old',''); }
    });
  });

  // Zone → Homecells
  $zone.on('change', function () {
    const id = $(this).val();
    fill($homecell, '-- All Homecells --', []);
    if (!id) return;

    ajaxJSON(ENDPOINTS.homecells + encodeURIComponent(id), (data) => {
      fill($homecell, '-- All Homecells --', data);
      const old = $homecell.data('old');
      if (old) { $homecell.val(old); $homecell.data('old',''); }
    });
  });

  // Rehydrate chain
  const oldChurch = $church.data('old');
  if (oldChurch) { $church.val(oldChurch); }
  $church.trigger('change');
});
</script>
@endsection
