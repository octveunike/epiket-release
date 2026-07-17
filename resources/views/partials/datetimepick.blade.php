{{--
    Date + custom time picker, digabung menjadi satu field datetime (Y-m-dTH:i).
    Params:
      - name     : nama field hidden hasil gabungan (mis "waktu_mulai")
      - date     : nilai tanggal awal "Y-m-d"
      - time     : nilai jam awal "H:i"
      - max      : batas maksimum jam "HH:MM" (opsional)
      - default  : jam default bila kosong, mis "06:30" (opsional)
      - required : tanggal wajib diisi (default true)
    Logika gabungan ada di /public/js/app.js (.datetimepick).
--}}
@php
    $dtName     = $name     ?? '';
    $dtDate     = $date     ?? '';
    $dtTime     = $time     ?? '';
    $dtMax      = $max      ?? null;
    $dtDefault  = $default  ?? null;
    $dtRequired = $required ?? true;
@endphp
<div class="datetimepick">
    <div class="datetimepick-row">
        <input type="date" class="form-control dtp-date" value="{{ $dtDate }}" @if($dtRequired) required @endif>
        @include('partials.timepick', ['name' => '', 'value' => $dtTime, 'max' => $dtMax, 'default' => $dtDefault])
    </div>
    <input type="hidden" name="{{ $dtName }}" class="dtp-out"
        value="{{ ($dtDate && $dtTime) ? $dtDate.'T'.$dtTime : '' }}">
</div>
