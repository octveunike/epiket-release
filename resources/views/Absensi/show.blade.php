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
                    <th>Keterangan</th>
                    <th>Lampiran</th>
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
                        <td class="text-muted-sm">{{ $d->keterangan ?: '—' }}</td>
                        <td>
                            @if ($d->lampiran_absensi)
                                <a href="{{ asset('storage/' . $d->lampiran_absensi) }}"
                                    target="_blank" class="link-lampiran">
                                    <i class="ri-attachment-2"></i> Lihat
                                </a>
                            @else
                                <span class="text-muted-sm">—</span>
                            @endif
                        </td>
                    </tr>
                @empty
                    <tr>
                        <td colspan="6" class="td-empty">Belum ada data detail ketidakhadiran.</td>
                    </tr>
                @endforelse
            </tbody>
        </table>
    </div>
</div>

@endsection