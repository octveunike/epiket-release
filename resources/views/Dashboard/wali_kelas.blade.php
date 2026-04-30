@extends('layouts.app')

@section('title', 'Dashboard Wali Kelas')

@section('content')

<div class="page-header">
    <div>
        <div class="breadcrumb">Dashboard</div>
        <h2>Dashboard Wali Kelas</h2>
        <div class="dash-breadcrumb-date">
            {{ \Carbon\Carbon::now()->translatedFormat('l, d F Y') }}
        </div>
    </div>
    @if($viewMode === 'wali' || !isset($kelas) || !$kelas)
    <form method="GET" action="{{ route('admin.index') }}" class="dash-filter-form">
        <input type="hidden" name="view" value="wali">
        <select name="kelas_id" class="form-control">
            <option value="">-- Pilih Kelas --</option>
            @foreach($daftarKelas as $k)
                <option value="{{ $k->id }}" {{ request('kelas_id') == $k->id ? 'selected' : '' }}>
                    {{ $k->nama_kelas }}
                </option>
            @endforeach
        </select>
        <button type="submit" class="btn btn-primary btn-sm">
            <i class="ri-search-line"></i> Tampilkan
        </button>
    </form>
    @endif
</div>

@if(!isset($kelas) || !$kelas)
    <div class="empty-state dash-empty-large">
        <i class="ri-building-4-line"></i>
        <p>
            @if($viewMode === 'wali') Pilih kelas di atas untuk melihat dashboard
            @else Kelas Anda belum terdaftar. Hubungi Admin.
            @endif
        </p>
    </div>
@else

{{-- Info Bar --}}
<div class="ab-infobar">
    <div class="ab-infobar-left">
        <i class="ri-building-4-line"></i>
        <strong>{{ $kelas->nama_kelas }}</strong>
        <span class="ab-infobar-sep">|</span>
        <span>{{ $totalSiswa }} Siswa</span>
        @if(isset($periodeAktif) && $periodeAktif)
            <span class="ab-infobar-sep">|</span>
            <span>{{ $periodeAktif->nama_periode }}</span>
        @endif
    </div>
    <a href="{{ route('Absensi.walikelas.index') }}" class="btn btn-sm btn-secondary">
        <i class="ri-history-line"></i> Riwayat Absensi
    </a>
</div>

{{-- ===== STAT HARI INI ===== --}}
<div class="dash-stat-grid-5">
    <div class="ab-stat-item ab-stat-hadir">
        <div class="ab-stat-num">{{ $totalHadir }}</div>
        <div class="ab-stat-lbl">Hadir</div>
    </div>
    <div class="ab-stat-item ab-stat-izin">
        <div class="ab-stat-num">{{ $totalIzin }}</div>
        <div class="ab-stat-lbl">Izin</div>
    </div>
    <div class="ab-stat-item ab-stat-sakit">
        <div class="ab-stat-num">{{ $totalSakit }}</div>
        <div class="ab-stat-lbl">Sakit</div>
    </div>
    <div class="ab-stat-item ab-stat-alpha">
        <div class="ab-stat-num">{{ $totalAlpha }}</div>
        <div class="ab-stat-lbl">Alpha</div>
    </div>
    <div class="ab-stat-item ab-stat-hadir">
        <div class="ab-stat-num">{{ $totalDispen }}</div>
        <div class="ab-stat-lbl">Dispen</div>
    </div>
</div>

{{-- ===== FOKUS UTAMA: ABSENSI MENUNGGU VALIDASI ===== --}}
<div class="card">
    <div class="dash-section-header">
        <div>
            <div class="dash-section-title">
                <i class="ri-shield-check-line"></i> Absensi Menunggu Validasi Anda
            </div>
            <div class="dash-section-subtitle">
                Setujui absensi yang sudah diisi Ketua Kelas
            </div>
        </div>
        @if($absensiMenungguValidasi->isNotEmpty())
            <span class="badge badge-warning dash-badge-big">
                {{ $absensiMenungguValidasi->count() }} Menunggu
            </span>
        @endif
    </div>

    @if($absensiMenungguValidasi->isEmpty())
        <div class="empty-state">
            <i class="ri-checkbox-circle-line"></i>
            <p>Tidak ada absensi yang menunggu validasi</p>
        </div>
    @else
        <div class="table-responsive">
            <table>
                <thead>
                    <tr>
                        <th>No</th>
                        <th>Tanggal</th>
                        <th>Diinput oleh</th>
                        <th>Status</th>
                        <th>Aksi</th>
                    </tr>
                </thead>
                <tbody>
                    @foreach($absensiMenungguValidasi as $i => $ab)
                    <tr>
                        <td class="col-no">{{ $i + 1 }}</td>
                        <td>
                            <strong>{{ \Carbon\Carbon::parse($ab->tanggal)->translatedFormat('l, d M Y') }}</strong>
                            @if(\Carbon\Carbon::parse($ab->tanggal)->isToday())
                                <span class="badge badge-success dash-badge-today">Hari Ini</span>
                            @endif
                        </td>
                        <td>{{ $ab->user_input ?? '-' }}</td>
                        <td class="col-center">
                            <span class="badge badge-warning">{{ $ab->nama_verifikasi }}</span>
                        </td>
                        <td class="col-center">
                            <a href="{{ route('Absensi.show', $ab->id) }}" class="btn btn-sm btn-primary">
                                <i class="ri-eye-line"></i> Lihat & Validasi
                            </a>
                        </td>
                    </tr>
                    @endforeach
                </tbody>
            </table>
        </div>
    @endif
</div>

{{-- ===== LAPORAN ALL ===== --}}
<div class="card">
    <div class="dash-section-header">
        <div class="dash-section-title">
            <i class="ri-file-list-3-line"></i> Laporan All
        </div>
        <a href="{{ route('Laporan.index') }}" class="btn btn-sm btn-secondary">
            Lihat Selengkapnya <i class="ri-arrow-right-line"></i>
        </a>
    </div>
    @if($laporanAll->isEmpty())
        <div class="empty-state">
            <i class="ri-file-list-3-line"></i>
            <p>Belum ada rekaman laporan absensi/keterlambatan</p>
        </div>
    @else
        @foreach($laporanAll as $item)
        <div class="ab-infobar dash-kt-item">
            <div class="ab-infobar-left">
                <i class="ri-user-line"></i>
                <div>
                    <strong>{{ $item->nama_siswa }}</strong>
                    <div class="dash-kt-reason">{{ rtrim($item->keterangan, '.') ?: '-' }}</div>
                </div>
            </div>
            <div style="text-align:right;">
                @php
                    $badgeColor = match(strtolower($item->jenis)) {
                        'terlambat' => 'badge-warning',
                        'sakit' => 'badge-info',
                        'izin' => 'badge-primary',
                        'alpha' => 'badge-danger',
                        'dispen', 'dispensasi' => 'badge-success',
                        default => 'badge-secondary'
                    };
                @endphp
                <span class="badge {{ $badgeColor }}" style="margin-bottom:4px; display:inline-block;">{{ $item->jenis }}</span>
                <div style="font-size:11px; color:var(--text-muted);">
                    {{ \Carbon\Carbon::parse($item->waktu)->format('d M Y') }}
                    @if($item->jenis === 'Terlambat')
                        {{ \Carbon\Carbon::parse($item->waktu)->format('H:i') }}
                    @endif
                </div>
            </div>
        </div>
        @endforeach
    @endif
</div>

{{-- ===== DISPENSASI AKTIF ===== --}}
@if(isset($dispensasiAktif) && $dispensasiAktif->isNotEmpty())
<div class="card">
    <div class="card-title">
        <i class="ri-file-paper-2-line"></i> Dispensasi Aktif Kelas Ini
        <span class="badge badge-info dash-badge-count">{{ $dispensasiAktif->count() }}</span>
    </div>
    <div class="table-responsive">
        <table>
            <thead>
                <tr><th>Kegiatan</th><th>Mulai</th><th>Selesai</th><th>Status</th></tr>
            </thead>
            <tbody>
                @foreach($dispensasiAktif as $d)
                <tr>
                    <td>{{ $d->kegiatan }}</td>
                    <td class="col-center">{{ \Carbon\Carbon::parse($d->waktu_mulai)->format('d M Y H:i') }}</td>
                    <td class="col-center">{{ \Carbon\Carbon::parse($d->waktu_selesai)->format('d M Y H:i') }}</td>
                    <td class="col-center">
                        @php $dc = match((int)$d->status_validasi_id){ 5=>'badge-success',1=>'badge-warning',default=>'badge-info'}; @endphp
                        <span class="badge {{ $dc }}">
                            {{ $d->status_validasi_id==5?'Disetujui':($d->status_validasi_id==1?'Menunggu':'Proses') }}
                        </span>
                    </td>
                </tr>
                @endforeach
            </tbody>
        </table>
    </div>
</div>
@endif

@endif {{-- end if kelas --}}
@endsection