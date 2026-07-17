{{--
    Custom time picker (scrollable Jam/Menit columns).
    Params:
      - name    : nama field hidden (kosongkan jika dipakai di dalam datetimepick)
      - value   : nilai awal "HH:MM"
      - max     : batas maksimum "HH:MM" (opsional)
      - default : nilai default bila kosong, mis "06:30" (opsional)
    Logika ada di /public/js/app.js (.timepick).
--}}
@php
    $tpName    = $name    ?? '';
    $tpValue   = $value   ?? '';
    $tpMax     = $max     ?? null;
    $tpDefault = $default ?? null;
@endphp
<div class="timepick"
     @if($tpMax) data-max="{{ $tpMax }}" @endif
     @if($tpDefault) data-default="{{ $tpDefault }}" @endif>
    <button type="button" class="form-control timepick-trigger">
        <i class="ri-time-line"></i>
        <span class="timepick-label">--:--</span>
    </button>
    <div class="timepick-pop">
        <div>
            <div class="timepick-col-title">Jam</div>
            <div class="timepick-grid timepick-jam"></div>
        </div>
        <div>
            <div class="timepick-col-title">Menit</div>
            <div class="timepick-grid timepick-menit"></div>
        </div>
    </div>
    <input type="hidden" @if($tpName !== '') name="{{ $tpName }}" @endif class="timepick-input" value="{{ $tpValue }}">
</div>
