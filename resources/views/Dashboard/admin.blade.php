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
            <div class="stat-number">{{ $totalTamuHariIni }}</div>
        </div>
    </div>
</div>

{{-- ===== DISPENSASI TERBARU ===== --}}
<div style="margin-bottom:20px;">

    {{-- Dispensasi Terbaru --}}
    <div class="card" style="margin-bottom:0;">
        <div style="display:flex; justify-content:space-between; align-items:center; margin-bottom:14px;">
            <div class="card-title" style="margin-bottom:0;">
                <i class="ri-file-paper-2-line" style="color:var(--primary);"></i> Dispensasi Terbaru
            </div>
            <a href="{{ route('Dispensasi.index') }}" class="btn btn-sm btn-secondary">
                Lihat Selengkapnya <i class="ri-arrow-right-line"></i>
            </a>
        </div>
        @if($dispensasiTerbaru->isEmpty())
            <div class="empty-state">
                <i class="ri-file-paper-2-line"></i>
                <p>Belum ada data dispensasi</p>
            </div>
        @else
            <div class="table-responsive">
                <table>
                    <thead>
                        <tr>
                            <th>No</th>
                            <th>Kegiatan</th>
                            <th>Organisasi</th>
                            <th>Mulai</th>
                            <th>Selesai</th>
                            <th>Aksi</th>
                        </tr>
                    </thead>
                    <tbody>
                        @foreach($dispensasiTerbaru->take(3) as $i => $d)
                        <tr>
                            <td class="col-no">{{ $i + 1 }}</td>
                            <td>{{ $d->kegiatan }}</td>
                            <td>{{ $d->nama_organisasi ?? '—' }}</td>
                            <td class="col-center">{{ \Carbon\Carbon::parse($d->waktu_mulai)->format('d M Y') }}</td>
                            <td class="col-center">{{ \Carbon\Carbon::parse($d->waktu_selesai)->format('d M Y') }}</td>
                            <td class="col-center">
                                <a href="{{ route('Dispensasi.show', $d->id) }}" class="btn btn-sm btn-primary">
                                    <i class="ri-eye-line"></i> Lihat
                                </a>
                            </td>
                        </tr>
                        @endforeach
                    </tbody>
                </table>
            </div>
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
                Lihat Selengkapnya <i class="ri-arrow-right-line"></i>
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
                        @foreach($keterlambatanTerbaru->take(3) as $kt)
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
                <i class="ri-user-received-line" style="color:var(--primary);"></i> Daftar Tamu
                <span class="badge badge-primary" style="margin-left:6px;">{{ $daftarTamu->count() }}</span>
            </div>
            <div style="display:flex; gap:6px;">
                <a href="{{ route('DaftarTamu.index') }}" class="btn btn-sm btn-secondary">
                    Lihat Selengkapnya <i class="ri-arrow-right-line"></i>
                </a>
            </div>
        </div>
        @if($daftarTamu->isEmpty())
            <div class="empty-state">
                <i class="ri-user-received-line"></i>
                <p>Belum ada daftar tamu</p>
            </div>
        @else
            <div class="table-responsive">
                <table>
                    <thead>
                        <tr>
                            <th class="col-center">Tanggal</th>
                            <th>Nama</th>
                            <th>Lembaga</th>
                            <th>Dituju</th>
                        </tr>
                    </thead>
                    <tbody>
                        @foreach($daftarTamu->take(3) as $t)
                        <tr>
                            <td class="col-center text-muted-sm" style="white-space:nowrap;">{{ \Carbon\Carbon::parse($t->tanggal_kunjungan)->translatedFormat('d M Y') }}</td>
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



@endsection