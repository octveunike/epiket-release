@extends('layouts.app')

@section('content')

@php
    $details    = $absensi->details;
    $izin       = $details->where('status_absensi_id', 1)->count();
    $sakit      = $details->where('status_absensi_id', 2)->count();
    $alpha      = $details->where('status_absensi_id', 3)->count();
    $dispen     = $details->where('status_absensi_id', 4)->count();
    $terlambat  = $details->where('status_absensi_id', 5)->count();
    $totalSiswa = $absensi->kelas->siswa()->where('status', 1)->count();
    $hadir      = $totalSiswa - $izin - $sakit - $alpha;

    // Ambil rentang jam seharian dari tabel jam_absensi
    $jamPertama = \App\Models\Reference\JamAbsensi::where('status', 1)->orderBy('jam_ke')->first();
    $jamTerakhir = \App\Models\Reference\JamAbsensi::where('status', 1)->orderByDesc('jam_ke')->first();
    $waktuSeharian = $jamPertama && $jamTerakhir
        ? \Carbon\Carbon::parse($jamPertama->waktu_mulai)->format('H:i') . ' – ' . \Carbon\Carbon::parse($jamTerakhir->waktu_selesai)->format('H:i')
        : 'Seharian';
@endphp

<div class="page-header">
    <div>
        <div class="breadcrumb">
            Admin /
            <a href="{{ route('Absensi.index') }}" class="breadcrumb-link">Data Absensi</a>
            / Detail
        </div>
        <h2>Detail Absensi</h2>
    </div>
    <a href="{{ route('Absensi.index') }}" class="btn btn-secondary">
        <i class="ri-arrow-left-line"></i> Kembali
    </a>
</div>

@if (session('success'))
    <div class="alert alert-success" id="s-alert">
        <i class="ri-checkbox-circle-line"></i> {{ session('success') }}
    </div>
    <script>setTimeout(()=>document.getElementById('s-alert')?.remove(),3000);</script>
@endif

{{-- Info Header --}}
<div class="card" style="margin-bottom:16px;">
    <div class="ab-info-grid">
        <div>
            <div class="ab-info-label">Kelas</div>
            <div class="ab-info-value">{{ $absensi->kelas->nama_kelas ?? '—' }}</div>
        </div>
        <div>
            <div class="ab-info-label">Tanggal</div>
            <div class="ab-info-value">
                {{ \Carbon\Carbon::parse($absensi->tanggal)->translatedFormat('l, d F Y') }}
            </div>
        </div>
        <div>
            <div class="ab-info-label">Periode</div>
            <div class="ab-info-value">{{ $absensi->periodeAkademik->nama_periode ?? '—' }}</div>
        </div>
        <div>
            <div class="ab-info-label">Status</div>
            <div>
                @if ($absensi->statusValidasi)
                    <span class="badge {{ in_array($absensi->status_validasi_id, [5]) ? 'badge-success' : 'badge-warning' }}">
                        {{ $absensi->statusValidasi->nama_status }}
                    </span>
                @else
                    <span style="color:#94a3b8;">—</span>
                @endif
            </div>
        </div>
        <div>
            <div class="ab-info-label">Diinput Oleh</div>
            <div class="ab-info-value-sm">{{ $absensi->userInput->nama ?? '—' }}</div>
        </div>
        <div>
            <div class="ab-info-label">Tanggal Input</div>
            <div class="ab-info-value-sm">
                {{ $absensi->tanggal_input ? \Carbon\Carbon::parse($absensi->tanggal_input)->format('d/m/Y H:i') : '—' }}
            </div>
        </div>
    </div>
</div>

{{-- Ringkasan --}}
<div style="display:flex;gap:10px;margin-bottom:20px;flex-wrap:wrap;">
    <span style="background:#d1fae5;color:#065f46;padding:6px 16px;border-radius:20px;font-size:13px;font-weight:600;">
        {{ $hadir }} Hadir
    </span>
    <span style="background:#e0f2fe;color:#075985;padding:6px 16px;border-radius:20px;font-size:13px;font-weight:600;">
        {{ $izin }} Izin
    </span>
    <span style="background:#fef3c7;color:#92400e;padding:6px 16px;border-radius:20px;font-size:13px;font-weight:600;">
        {{ $sakit }} Sakit
    </span>
    <span style="background:#fee2e2;color:#991b1b;padding:6px 16px;border-radius:20px;font-size:13px;font-weight:600;">
        {{ $alpha }} Alpha
    </span>
    <span style="background:#f1f5f9;color:#475569;padding:6px 16px;border-radius:20px;font-size:13px;font-weight:600;">
        {{ $totalSiswa }} Total
    </span>
</div>

{{-- Tabel Detail Siswa --}}
<div class="card">
    <div class="card-title"><i class="ri-list-check-2"></i> Detail ketidakhadiran Siswa</div>
    <div class="table-responsive">
        <table>
            <thead>
                <tr>
                    <th class="col-no">No</th>
                    <th>Nama Siswa</th>
                    <th class="col-center">Status</th>
                    <th class="col-center">Waktu</th>
                    <th class="col-center">Keterangan</th>
                </tr>
            </thead>
            <tbody>
                @forelse ($details as $d)
                    @php $sid = $d->status_absensi_id; @endphp
                    <tr>
                        <td class="col-no">{{ $loop->iteration }}</td>
                        <td><strong>{{ $d->siswa->nama_siswa ?? '—' }}</strong></td>
                        <td class="col-center">
                            @if ($sid == 1)
                                @if(!$d->is_full_day)
                                    <span class="badge badge-success">Hadir</span>
                                    <span class="badge badge-info" style="margin-left:4px;">Izin</span>
                                @else
                                    <span class="badge badge-info">Izin</span>
                                @endif
                            @elseif ($sid == 2)
                                @if(!$d->is_full_day)
                                    <span class="badge badge-success">Hadir</span>
                                    <span class="badge badge-warning" style="margin-left:4px;">Sakit</span>
                                @else
                                    <span class="badge badge-warning">Sakit</span>
                                @endif
                            @elseif ($sid == 3)
                                @if(!$d->is_full_day)
                                    <span class="badge badge-success">Hadir</span>
                                    <span class="badge badge-danger" style="margin-left:4px;">Alpha</span>
                                @else
                                    <span class="badge badge-danger">Alpha</span>
                                @endif
                            @elseif ($sid == 4)
                                @if(!$d->is_full_day)
                                    <span class="badge badge-success">Hadir</span>
                                    <span class="badge badge-primary" style="margin-left:4px;">Dispen</span>
                                @else
                                    <span class="badge badge-primary">Dispen</span>
                                @endif
                            @elseif ($sid == 5)
                                <span class="badge badge-success">Hadir</span>
                                <span class="badge badge-warning" style="margin-left:4px;">Terlambat</span>
                            @else
                                <span class="badge badge-success">Hadir</span>
                            @endif
                        </td>
                        <td class="col-center">
                            @if ($d->is_full_day)
                                {{-- Seharian: tampilkan rentang dari jam_absensi --}}
                                <span class="text-muted-sm">{{ $waktuSeharian }}</span>
                            @elseif ($d->jams->count())
                                {{-- Per jam: tampilkan rentang dari jam yang dipilih --}}
                                @php
                                    $jamIds   = $d->jams->pluck('jam')->filter()->sortBy('jam_ke');
                                    $jamAwal  = $jamIds->first();
                                    $jamAkhir = $jamIds->last();
                                @endphp
                                <span style="font-size:13px;font-weight:600;color:#f59e0b;">
                                    {{ $jamAwal ? \Carbon\Carbon::parse($jamAwal->waktu_mulai)->format('H:i') : '?' }}
                                    –
                                    {{ $jamAkhir ? \Carbon\Carbon::parse($jamAkhir->waktu_selesai)->format('H:i') : '?' }}
                                </span>
                                <div class="text-muted-sm">
                                    Jam {{ $jamIds->pluck('jam_ke')->implode(', ') }}
                                </div>
                            @else
                                <span class="text-muted-sm">—</span>
                            @endif
                        </td>
                        <td class="col-center">
                            @php
                                $sumber     = $sumberBySiswa[(int) $d->siswa_id] ?? ['kind' => 'manual', 'user' => '—'];
                                $sumberText = $sumber['kind'] === 'dispensasi' ? ('Approval by ' . $sumber['user']) : $sumber['user'];
                            @endphp
                            @if (in_array((int) $sid, [1, 2, 3, 4, 5]) || $d->keterangan || $d->lampiran_absensi)
                                <button type="button" class="btn btn-sm btn-secondary"
                                    onclick="showKetDetail(this)"
                                    data-nama="{{ $d->siswa->nama_siswa ?? '—' }}"
                                    data-keterangan="{{ $d->keterangan ?: '—' }}"
                                    data-sumber="{{ $sumberText }}"
                                    data-lampiran="{{ $d->lampiran_absensi ? asset('storage/' . $d->lampiran_absensi) : '' }}">
                                    <i class="ri-eye-line"></i> Detail
                                </button>
                            @else
                                <span class="text-muted-sm">—</span>
                            @endif
                        </td>
                    </tr>
                @empty
                    <tr>
                        <td colspan="5" class="td-empty">Belum ada data detail ketidakhadiran.</td>
                    </tr>
                @endforelse
            </tbody>
        </table>
    </div>
</div>

{{-- Popup detail keterangan --}}
<div class="confirm-overlay" id="ketDetailModal">
    <div class="confirm-box" style="max-width:460px; text-align:left;">
        <div style="display:flex; align-items:center; gap:8px; margin-bottom:14px;">
            <i class="ri-file-list-3-line" style="color:var(--primary); font-size:20px;"></i>
            <h3 style="margin:0;">Detail Keterangan</h3>
        </div>
        <div style="font-size:13px; line-height:1.6;">
            <div style="color:var(--text-muted); font-size:12px;">Nama Siswa</div>
            <div style="font-weight:600; margin-bottom:10px;" id="kdNama">—</div>

            <div style="color:var(--text-muted); font-size:12px;">Keterangan</div>
            <div style="margin-bottom:10px;" id="kdKeterangan">—</div>

            <div style="color:var(--text-muted); font-size:12px;">Update Terakhir</div>
            <div id="kdSumber">—</div>
        </div>
        <div class="confirm-actions" style="margin-top:18px; justify-content:flex-start;">
            <a href="#" id="kdLampiran" target="_blank" class="btn btn-sm btn-primary" style="display:none;">
                <i class="ri-attachment-2"></i> Lihat Lampiran
            </a>
            <button type="button" class="btn btn-sm btn-secondary" onclick="closeKetDetail()">Tutup</button>
        </div>
    </div>
</div>

<script>
    function showKetDetail(btn) {
        document.getElementById('kdNama').textContent       = btn.getAttribute('data-nama') || '—';
        document.getElementById('kdKeterangan').textContent = btn.getAttribute('data-keterangan') || '—';
        document.getElementById('kdSumber').textContent     = btn.getAttribute('data-sumber') || '—';
        var lamp = document.getElementById('kdLampiran');
        var url  = btn.getAttribute('data-lampiran');
        if (url) { lamp.href = url; lamp.style.display = 'inline-flex'; }
        else     { lamp.style.display = 'none'; }
        document.getElementById('ketDetailModal').classList.add('show');
    }
    function closeKetDetail() {
        document.getElementById('ketDetailModal').classList.remove('show');
    }
    document.getElementById('ketDetailModal').addEventListener('click', function (e) {
        if (e.target === this) closeKetDetail();
    });
</script>

@endsection