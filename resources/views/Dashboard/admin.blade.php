@extends('layouts.app')

@section('title', 'Dashboard Admin')

@section('content')

<div class="page-header">
    <div>
        <div class="breadcrumb">Dashboard</div>
        <h2>Selamat Datang, {{ auth()->user()->nama ?? auth()->user()->username }} 👋</h2>
        <div style="font-size:12px; color:var(--text-muted); margin-top:2px;">
            {{ \Carbon\Carbon::now()->translatedFormat('l, d F Y') }}
            @if($periodeAktif)
                &nbsp;·&nbsp; Periode: <strong>{{ $periodeAktif->nama_periode }}</strong>
            @endif
        </div>
    </div>
    {{-- View Switcher --}}
    <div style="display:flex; gap:6px; flex-wrap:wrap;">
        <a href="{{ route('admin.index') }}"
           class="btn btn-sm {{ request('view','admin')==='admin' ? 'btn-primary' : 'btn-secondary' }}">
            <i class="ri-layout-grid-line"></i> Admin
        </a>
        <a href="{{ route('admin.index', ['view'=>'piket']) }}"
           class="btn btn-sm {{ request('view')==='piket' ? 'btn-primary' : 'btn-secondary' }}">
            <i class="ri-user-2-line"></i> Piket
        </a>
        <a href="{{ route('admin.index', ['view'=>'wali','kelas_id'=>request('kelas_id')]) }}"
           class="btn btn-sm {{ request('view')==='wali' ? 'btn-primary' : 'btn-secondary' }}">
            <i class="ri-group-line"></i> Wali Kelas
        </a>
        <a href="{{ route('admin.index', ['view'=>'ketua','kelas_id'=>request('kelas_id')]) }}"
           class="btn btn-sm {{ request('view')==='ketua' ? 'btn-primary' : 'btn-secondary' }}">
            <i class="ri-user-star-line"></i> Ketua Kelas
        </a>
    </div>
</div>

{{-- ===== STAT GRID ===== --}}
<div class="stat-grid">
    <div class="stat-card">
        <div class="stat-icon indigo"><i class="ri-group-line"></i></div>
        <div class="stat-info">
            <div class="stat-title">Siswa Aktif</div>
            <div class="stat-number">{{ number_format($totalSiswa) }}</div>
        </div>
    </div>
    <div class="stat-card">
        <div class="stat-icon emerald"><i class="ri-building-4-line"></i></div>
        <div class="stat-info">
            <div class="stat-title">Total Kelas</div>
            <div class="stat-number">{{ $totalKelas }}</div>
        </div>
    </div>
    <div class="stat-card">
        <div class="stat-icon sky"><i class="ri-user-3-line"></i></div>
        <div class="stat-info">
            <div class="stat-title">Total Guru</div>
            <div class="stat-number">{{ $totalGuru }}</div>
        </div>
    </div>
    <div class="stat-card">
        <div class="stat-icon amber"><i class="ri-team-line"></i></div>
        <div class="stat-info">
            <div class="stat-title">Organisasi</div>
            <div class="stat-number">{{ $totalOrganisasi }}</div>
        </div>
    </div>
</div>

{{-- ===== REKAP ABSENSI ===== --}}
<div class="card">
    <div style="display:flex; justify-content:space-between; align-items:center; margin-bottom:14px;">
        <div class="card-title" style="margin-bottom:0;">
            <i class="ri-calendar-check-line" style="color:var(--primary);"></i>
            Rekap Absensi Hari Ini
        </div>
        <a href="{{ route('Absensi.index') }}" class="btn btn-sm btn-secondary">
            Lihat Semua <i class="ri-arrow-right-line"></i>
        </a>
    </div>

    {{-- Progress bar --}}
    @php $pct = $totalKelas > 0 ? round(($kelasHariIni / $totalKelas) * 100) : 0; @endphp
    <div style="margin-bottom:14px;">
        <div style="display:flex; justify-content:space-between; font-size:12px; color:var(--text-muted); margin-bottom:5px;">
            <span>Kelas sudah input absensi</span>
            <span><strong style="color:var(--primary);">{{ $kelasHariIni }}</strong> / {{ $totalKelas }} ({{ $pct }}%)</span>
        </div>
        <div style="background:var(--border); border-radius:10px; height:8px; overflow:hidden;">
            <div style="background:var(--primary); width:{{ $pct }}%; height:100%; border-radius:10px; transition:.4s;"></div>
        </div>
    </div>

    {{-- Stat absensi --}}
    <div class="ab-stat-grid">
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

    {{-- Kelas belum absen --}}
    @if($kelasBelumAbsen->isNotEmpty())
    <div class="alert alert-warning" style="margin-bottom:0; flex-wrap:wrap; gap:8px;">
        <i class="ri-error-warning-line"></i>
        <span><strong>{{ $kelasBelumIsi }} kelas</strong> belum input:</span>
        <div style="display:flex; flex-wrap:wrap; gap:5px;">
            @foreach($kelasBelumAbsen as $k)
                <span class="badge badge-warning">{{ $k->nama_kelas }}</span>
            @endforeach
        </div>
    </div>
    @else
    <div class="alert alert-success" style="margin-bottom:0;">
        <i class="ri-checkbox-circle-line"></i> Semua kelas sudah input absensi hari ini
    </div>
    @endif
</div>

{{-- ===== BARIS: TERLAMBAT / VERIF / DISPEN ===== --}}
<div class="stat-grid">
    <div class="stat-card">
        <div class="stat-icon rose"><i class="ri-time-line"></i></div>
        <div class="stat-info">
            <div class="stat-title">Terlambat Hari Ini</div>
            <div class="stat-number">{{ $totalTerlambat }}</div>
        </div>
    </div>
    <div class="stat-card">
        <div class="stat-icon indigo"><i class="ri-shield-check-line"></i></div>
        <div class="stat-info">
            <div class="stat-title">Menunggu Verif Wali</div>
            <div class="stat-number">{{ $absensiMenungguWali }}</div>
        </div>
    </div>
    <div class="stat-card">
        <div class="stat-icon sky"><i class="ri-file-paper-2-line"></i></div>
        <div class="stat-info">
            <div class="stat-title">Dispensasi Pending</div>
            <div class="stat-number">{{ $dispensasiPending }}</div>
        </div>
    </div>
    <div class="stat-card">
        <div class="stat-icon amber"><i class="ri-user-received-line"></i></div>
        <div class="stat-info">
            <div class="stat-title">Tamu Hari Ini</div>
            <div class="stat-number">{{ $tamuHariIni->count() }}</div>
        </div>
    </div>
</div>

{{-- ===== TABEL 2 KOLOM ===== --}}
<div style="display:grid; grid-template-columns:1fr 1fr; gap:16px; margin-bottom:20px;">

    {{-- Absensi Menunggu Verifikasi --}}
    <div class="card" style="margin-bottom:0;">
        <div style="display:flex; justify-content:space-between; align-items:center; margin-bottom:14px;">
            <div class="card-title" style="margin-bottom:0;">
                <i class="ri-file-check-line" style="color:var(--primary);"></i> Absensi Menunggu Verif
            </div>
            <a href="{{ route('Absensi.walikelas.index') }}" class="btn btn-sm btn-secondary">
                Semua <i class="ri-arrow-right-line"></i>
            </a>
        </div>
        @if($absensiMenungguVerif->isEmpty())
            <div class="empty-state">
                <i class="ri-checkbox-circle-line"></i>
                <p>Tidak ada yang menunggu verifikasi</p>
            </div>
        @else
            @foreach($absensiMenungguVerif as $ab)
            <div class="ab-infobar" style="margin-bottom:8px; padding:10px 14px;">
                <div class="ab-infobar-left" style="font-size:13px;">
                    <i class="ri-building-4-line"></i>
                    <strong>{{ $ab->nama_kelas }}</strong>
                    <span class="ab-infobar-sep">·</span>
                    <span>{{ \Carbon\Carbon::parse($ab->tanggal)->translatedFormat('d M Y') }}</span>
                </div>
                <a href="{{ route('Absensi.show', $ab->id) }}" class="btn btn-sm btn-primary">
                    <i class="ri-eye-line"></i>
                </a>
            </div>
            @endforeach
        @endif
    </div>

    {{-- Dispensasi Terbaru --}}
    <div class="card" style="margin-bottom:0;">
        <div style="display:flex; justify-content:space-between; align-items:center; margin-bottom:14px;">
            <div class="card-title" style="margin-bottom:0;">
                <i class="ri-file-paper-2-line" style="color:var(--primary);"></i> Dispensasi Terbaru
            </div>
            <a href="{{ route('Dispensasi.index') }}" class="btn btn-sm btn-secondary">
                Semua <i class="ri-arrow-right-line"></i>
            </a>
        </div>
        @if($dispensasiTerbaru->isEmpty())
            <div class="empty-state">
                <i class="ri-file-paper-2-line"></i>
                <p>Belum ada data dispensasi</p>
            </div>
        @else
            @foreach($dispensasiTerbaru as $d)
            <div class="ab-infobar" style="margin-bottom:8px; padding:10px 14px;">
                <div class="ab-infobar-left" style="font-size:13px; min-width:0;">
                    <i class="ri-team-line"></i>
                    <div style="min-width:0;">
                        <div style="font-weight:600; white-space:nowrap; overflow:hidden; text-overflow:ellipsis; max-width:180px;">{{ $d->kegiatan }}</div>
                        <div style="font-size:11px; color:var(--text-muted);">{{ $d->nama_organisasi }}</div>
                    </div>
                </div>
                @php $dc = match((int)$d->status_verifikasi_id){ 5=>'badge-success',1=>'badge-warning',default=>'badge-info'}; @endphp
                <span class="badge {{ $dc }}">{{ $d->nama_verifikasi }}</span>
            </div>
            @endforeach
        @endif
    </div>
</div>

{{-- ===== TERLAMBAT + TAMU ===== --}}
<div style="display:grid; grid-template-columns:1fr 1fr; gap:16px; margin-bottom:20px;">

    {{-- Keterlambatan --}}
    <div class="card" style="margin-bottom:0;">
        <div style="display:flex; justify-content:space-between; align-items:center; margin-bottom:14px;">
            <div class="card-title" style="margin-bottom:0;">
                <i class="ri-time-line" style="color:var(--primary);"></i> Keterlambatan Hari Ini
                <span class="badge badge-warning" style="margin-left:6px;">{{ $totalTerlambat }}</span>
            </div>
            <a href="{{ route('Keterlambatan.index') }}" class="btn btn-sm btn-secondary">
                Semua <i class="ri-arrow-right-line"></i>
            </a>
        </div>
        @if($keterlambatanTerbaru->isEmpty())
            <div class="empty-state">
                <i class="ri-time-line"></i>
                <p>Tidak ada keterlambatan hari ini</p>
            </div>
        @else
            <div class="table-responsive">
                <table>
                    <thead>
                        <tr>
                            <th>Nama</th>
                            <th>Kelas</th>
                            <th>Waktu</th>
                        </tr>
                    </thead>
                    <tbody>
                        @foreach($keterlambatanTerbaru as $kt)
                        <tr>
                            <td>{{ $kt->nama_siswa }}</td>
                            <td class="col-center"><span class="badge badge-info">{{ $kt->nama_kelas }}</span></td>
                            <td class="col-center" style="font-weight:600; color:var(--primary);">
                                {{ \Carbon\Carbon::parse($kt->waktu_masuk)->format('H:i') }}
                            </td>
                        </tr>
                        @endforeach
                    </tbody>
                </table>
            </div>
        @endif
    </div>

    {{-- Daftar Tamu --}}
    <div class="card" style="margin-bottom:0;">
        <div style="display:flex; justify-content:space-between; align-items:center; margin-bottom:14px;">
            <div class="card-title" style="margin-bottom:0;">
                <i class="ri-user-received-line" style="color:var(--primary);"></i> Daftar Tamu Hari Ini
                <span class="badge badge-primary" style="margin-left:6px;">{{ $tamuHariIni->count() }}</span>
            </div>
            <div style="display:flex; gap:6px;">
                <a href="{{ route('DaftarTamu.create') }}" class="btn btn-sm btn-primary">
                    <i class="ri-add-line"></i>
                </a>
                <a href="{{ route('DaftarTamu.index') }}" class="btn btn-sm btn-secondary">
                    Semua <i class="ri-arrow-right-line"></i>
                </a>
            </div>
        </div>
        @if($tamuHariIni->isEmpty())
            <div class="empty-state">
                <i class="ri-user-received-line"></i>
                <p>Belum ada tamu hari ini</p>
            </div>
        @else
            <div class="table-responsive">
                <table>
                    <thead>
                        <tr>
                            <th>Nama</th>
                            <th>Lembaga</th>
                            <th>Dituju</th>
                        </tr>
                    </thead>
                    <tbody>
                        @foreach($tamuHariIni as $t)
                        <tr>
                            <td><strong>{{ $t->nama }}</strong></td>
                            <td>{{ $t->lembaga_organisasi ?? '-' }}</td>
                            <td>{{ $t->orang_yang_dituju ?? '-' }}</td>
                        </tr>
                        @endforeach
                    </tbody>
                </table>
            </div>
        @endif
    </div>
</div>

{{-- ===== AKSI CEPAT ===== --}}
<div class="card">
    <div class="card-title"><i class="ri-flashlight-line" style="color:var(--primary);"></i> Aksi Cepat</div>
    <div style="display:flex; gap:10px; flex-wrap:wrap;">
        <a href="{{ route('DaftarTamu.create') }}" class="btn btn-secondary">
            <i class="ri-user-add-line"></i> Tambah Tamu
        </a>
        <a href="{{ route('Keterlambatan.create') }}" class="btn btn-secondary">
            <i class="ri-time-line"></i> Catat Keterlambatan
        </a>
        <a href="{{ route('Dispensasi.index') }}" class="btn btn-secondary">
            <i class="ri-file-paper-2-line"></i> Kelola Dispensasi
        </a>
        <a href="{{ route('Laporan.index') }}" class="btn btn-secondary">
            <i class="ri-bar-chart-2-line"></i> Laporan
        </a>
        <a href="{{ route('Absensi.index') }}" class="btn btn-secondary">
            <i class="ri-calendar-check-line"></i> Data Absensi
        </a>
    </div>
</div>

@endsection