{{-- Panel switcher: hanya tampil bila user punya >1 peran/dashboard --}}
@if(isset($panels) && count($panels) > 1)
@php
    $panelIcons = [
        'admin' => 'ri-layout-grid-line',
        'piket' => 'ri-user-2-line',
        'wali'  => 'ri-group-line',
        'ketua' => 'ri-user-star-line',
    ];
@endphp
<div class="panel-switcher" style="display:flex; align-items:center; gap:6px; flex-wrap:wrap; margin-bottom:16px;">
    <span style="font-size:12px; color:var(--text-muted); margin-right:2px;">
        <i class="ri-repeat-line"></i> Ganti tampilan:
    </span>
    @foreach($panels as $key => $label)
        <a href="{{ route('admin.index', ['panel' => $key]) }}"
           class="btn btn-sm {{ ($activePanel ?? '') === $key ? 'btn-primary' : 'btn-secondary' }}">
            <i class="{{ $panelIcons[$key] ?? 'ri-dashboard-line' }}"></i> {{ $label }}
        </a>
    @endforeach
</div>
@endif
