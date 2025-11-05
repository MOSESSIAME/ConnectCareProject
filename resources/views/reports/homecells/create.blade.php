@extends('layouts.app')

@section('content')
<div class="container">
    <h2 class="mb-4 fw-bold text-primary">Submit Homecell Report</h2>

    @if(session('success'))
        <div class="alert alert-success">{{ session('success') }}</div>
    @endif

    @if($errors->any())
        <div class="alert alert-danger">
            <ul class="mb-0">
                @foreach($errors->all() as $error) <li>{{ $error }}</li> @endforeach
            </ul>
        </div>
    @endif

    <form action="{{ route('reports.homecells.store') }}" method="POST" class="card shadow-sm p-4 bg-white rounded-4">
        @csrf

        <div class="row g-3">
            {{-- Church --}}
            <div class="col-md-6">
                <label for="church_id" class="form-label fw-semibold">Church</label>
                <select name="church_id" id="church_id" class="form-select" required
                        data-old="{{ old('church_id') }}">
                    <option value="">-- Select Church --</option>
                    @foreach($churches as $church)
                        <option value="{{ $church->id }}" @selected(old('church_id') == $church->id)>{{ $church->name }}</option>
                    @endforeach
                </select>
            </div>

            {{-- District (depends on church) --}}
            <div class="col-md-6">
                <label for="district_id" class="form-label fw-semibold">District</label>
                <select name="district_id" id="district_id" class="form-select" required
                        data-old="{{ old('district_id') }}" disabled>
                    <option value="">-- Select District --</option>
                </select>
            </div>

            {{-- Zone (depends on district) --}}
            <div class="col-md-6">
                <label for="zone_id" class="form-label fw-semibold">Zone</label>
                <select name="zone_id" id="zone_id" class="form-select" required
                        data-old="{{ old('zone_id') }}" disabled>
                    <option value="">-- Select Zone --</option>
                </select>
            </div>

            {{-- Homecell (depends on zone) --}}
            <div class="col-md-6">
                <label for="homecell_id" class="form-label fw-semibold">Homecell</label>
                <select name="homecell_id" id="homecell_id" class="form-select" required
                        data-old="{{ old('homecell_id') }}" disabled>
                    <option value="">-- Select Homecell --</option>
                </select>
            </div>

            {{-- Attendance --}}
            <div class="col-md-3">
                <label for="males" class="form-label">Males</label>
                <input type="number" min="0" name="males" id="males" class="form-control" value="{{ old('males', 0) }}" required>
            </div>
            <div class="col-md-3">
                <label for="females" class="form-label">Females</label>
                <input type="number" min="0" name="females" id="females" class="form-control" value="{{ old('females', 0) }}" required>
            </div>
            <div class="col-md-3">
                <label for="first_timers" class="form-label">First-timers</label>
                <input type="number" min="0" name="first_timers" id="first_timers" class="form-control" value="{{ old('first_timers', 0) }}" required>
            </div>
            <div class="col-md-3">
                <label for="new_converts" class="form-label">New Converts</label>
                <input type="number" min="0" name="new_converts" id="new_converts" class="form-control" value="{{ old('new_converts', 0) }}" required>
            </div>

            <div class="col-12">
                <label for="testimonies" class="form-label">Testimonies</label>
                <textarea name="testimonies" id="testimonies" rows="3" class="form-control"
                          placeholder="Write any testimonies shared...">{{ old('testimonies') }}</textarea>
            </div>

            <div class="col-12 text-end mt-3">
                <button type="submit" class="btn btn-success px-4">Submit Report</button>
                <a href="{{ route('reports.homecells.index') }}" class="btn btn-outline-secondary px-4">Cancel</a>
            </div>
        </div>
    </form>
</div>

{{-- jQuery --}}
<script src="https://code.jquery.com/jquery-3.6.0.min.js"></script>

<script>
$(function () {
  const $church   = $('#church_id');
  const $district = $('#district_id');
  const $zone     = $('#zone_id');
  const $homecell = $('#homecell_id');

  const ENDPOINTS = {
    districts: '{{ url('/get-districts') }}/',   // + {church_id}
    zones:     '{{ url('/get-zones') }}/',       // + {district_id}
    homecells: '{{ url('/get-homecells') }}/'    // + {zone_id}
  };

  function resetSelect($el, placeholder, disable = true) {
    $el.html(`<option value="">${placeholder}</option>`).prop('disabled', !!disable);
  }
  function fillSelect($el, items, placeholder) {
    resetSelect($el, placeholder, false);
    (items || []).forEach(i => $el.append(`<option value="${i.id}">${i.name}</option>`));
    if (!items || items.length === 0) {
      $el.append('<option value="" disabled>(No records found)</option>');
    }
  }
  function resetFrom(level) {
    if (level === 'church') {
      resetSelect($district, '-- Select District --');
      resetSelect($zone, '-- Select Zone --');
      resetSelect($homecell, '-- Select Homecell --');
    } else if (level === 'district') {
      resetSelect($zone, '-- Select Zone --');
      resetSelect($homecell, '-- Select Homecell --');
    } else if (level === 'zone') {
      resetSelect($homecell, '-- Select Homecell --');
    }
  }

  // 🔁 Normalize JSON: accept array OR {data:[...]}
  function ajaxJSON(url, onOk) {
    $.ajax({
      url,
      method: 'GET',
      headers: { 'Accept': 'application/json' },
      dataType: 'json'
    })
    .done(resp => {
      console.log('[DD]', url, '→', resp);
      const data = Array.isArray(resp) ? resp
                : (resp && Array.isArray(resp.data) ? resp.data : []);
      onOk(data);
    })
    .fail(xhr => {
      console.error('[DD] Error', xhr.status, xhr.responseText);
      onOk([]);
    });
  }

  // 1) Church → Districts
  $church.on('change', function () {
    const id = $(this).val();
    resetFrom('church');
    if (!id) return;
    ajaxJSON(ENDPOINTS.districts + encodeURIComponent(id), (data) => {
      fillSelect($district, data, '-- Select District --');
      const old = $district.data('old');
      if (old) { $district.val(String(old)).trigger('change'); $district.data('old',''); }
    });
  });

  // 2) District → Zones
  $district.on('change', function () {
    const id = $(this).val();
    resetFrom('district');
    if (!id) return;
    ajaxJSON(ENDPOINTS.zones + encodeURIComponent(id), (data) => {
      fillSelect($zone, data, '-- Select Zone --');
      const old = $zone.data('old');
      if (old) { $zone.val(String(old)).trigger('change'); $zone.data('old',''); }
    });
  });

  // 3) Zone → Homecells
  $zone.on('change', function () {
    const id = $(this).val();
    resetFrom('zone');
    if (!id) return;
    ajaxJSON(ENDPOINTS.homecells + encodeURIComponent(id), (data) => {
      fillSelect($homecell, data, '-- Select Homecell --');
      const old = $homecell.data('old');
      if (old) { $homecell.val(String(old)); $homecell.data('old',''); }
    });
  });

  // Rehydrate chain if user returned from validation errors
  const oldChurch = $church.data('old');
  if (oldChurch) { $church.val(String(oldChurch)).trigger('change'); }
});
</script>
@endsection
