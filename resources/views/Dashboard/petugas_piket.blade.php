@extends('layouts.app')

@section('title', 'Dashboard Petugas Piket')

@section('content')

<div class="page-header">
    <div>
        <div class="breadcrumb">Dashboard</div>
        <h2>Dashboard Petugas Piket</h2>
        <div style="font-size:12px; color:var(--text-muted); margin-top:2px;">
            {{ \Carbon\Carbon::now()->translatedFormat('l, d F Y') }}
            @if($periodeAktif) &nbsp;·&nbsp; <strong>{{ $periodeAktif->nama_periode }}</strong> @endif
        </div>
    </div>
    <div style="display:flex; gap:8px;">
        <a href="{{ route('DaftarTamu.create') }}" class="btn btn-primary">
            <i class="ri-user-add-line"></i> Tambah Tamu
        </a>
        <a href="{{ route('Keterlambatan.create') }}" class="btn btn-primary">
            <i class="ri-time-line"></i> Catat Terlambat
        </a>
    </div>
</div>

{{-- ===== STAT ===== --}}
<div class="stat-grid">
    <div class="stat-card">
        <div class="stat-icon emerald"><i class="ri-building-4-line"></i></div>
        <div class="stat-info">
            <div class="stat-title">Kelas Sudah Absen</div>
            <div class="stat-number">
                {{ $kelasUdahAbsen }}<span style="font-size:14px; font-weight:500; color:var(--text-muted);"> / {{ $totalKelas }}</span>
            </div>
        </div>
    </div>
    <div class="stat-card">
        <div class="stat-icon rose"><i class="ri-close-circle-line"></i></div>
        <div class="stat-info">
            <div class="stat-title">Alpha Hari Ini</div>
            <div class="stat-number">{{ $totalAlpha }}</div>
        </div>
    </div>
    <div class="stat-card">
        <div class="stat-icon amber"><i class="ri-time-line"></i></div>
        <div class="stat-info">
            <div class="stat-title">Terlambat</div>
            <div class="stat-number">{{ $totalTerlambat }}</div>
        </div>
    </div>
    <div class="stat-card">
        <div class="stat-icon indigo"><i class="ri-file-paper-2-line"></i></div>
        <div class="stat-info">
            <div class="stat-title">Dispensasi Pending</div>
            <div class="stat-number">{{ $dispensasiMenunggu->count() }}</div>
        </div>
    </div>
</div>

{{-- ===== STATUS ABSENSI ===== --}}
<div class="card" style="margin-bottom:20px;">
    <div class="card-title">
        <i class="ri-calendar-check-line" style="color:var(--primary);"></i> Status Absensi Hari Ini
    </div>

        {{-- Progress --}}
        @php $pct = $totalKelas > 0 ? round(($kelasUdahAbsen/$totalKelas)*100) : 0; @endphp
        <div style="margin-bottom:14px;">
            <div style="display:flex; justify-content:space-between; font-size:12px; color:var(--text-muted); margin-bottom:5px;">
                <span>Progress pengisian</span>
                <span><strong style="color:var(--primary);">{{ $pct }}%</strong></span>
            </div>
            <div style="background:var(--border); border-radius:10px; height:8px; overflow:hidden;">
                <div style="background:var(--primary); width:{{ $pct }}%; height:100%; border-radius:10px;"></div>
            </div>
        </div>

        {{-- Rekap --}}
        <div class="ab-stat-grid" style="margin-bottom:14px;">
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
        <div class="alert alert-warning" style="margin-bottom:0; flex-wrap:wrap; gap:6px;">
            <i class="ri-error-warning-line"></i>
            <span>Belum isi ({{ $kelasBelumAbsen->count() }}):</span>
            <div style="display:flex; flex-wrap:wrap; gap:5px;">
                @foreach($kelasBelumAbsen as $k)
                    <span class="badge badge-warning">{{ $k->nama_kelas }}</span>
                @endforeach
            </div>
        </div>
        @else
        <div class="alert alert-success" style="margin-bottom:0;">
            <i class="ri-checkbox-circle-line"></i> Semua kelas sudah input absensi
        </div>
        @endif
</div>

{{-- ===== KETERLAMBATAN ===== --}}
<div class="card">
    <div style="display:flex; justify-content:space-between; align-items:center; margin-bottom:14px;">
        <div class="card-title" style="margin-bottom:0;">
            <i class="ri-time-line" style="color:var(--primary);"></i> Keterlambatan Hari Ini
            @if($totalTerlambat > 0)
                <span class="badge badge-warning" style="margin-left:6px;">{{ $totalTerlambat }}</span>
            @endif
        </div>
        <a href="{{ route('Keterlambatan.index') }}" class="btn btn-sm btn-secondary">
            Semua <i class="ri-arrow-right-line"></i>
        </a>
    </div>
    @if($keterlambatanHariIni->isEmpty())
        <div class="empty-state">
            <i class="ri-time-line"></i>
            <p>Tidak ada keterlambatan hari ini</p>
        </div>
    @else
        <div class="table-responsive">
            <table>
                <thead>
                    <tr>
                        <th>No</th>
                        <th>Nama Siswa</th>
                        <th>Kelas</th>
                        <th>Waktu Masuk</th>
                        <th>Alasan</th>
                    </tr>
                </thead>
                <tbody>
                    @foreach($keterlambatanHariIni as $i => $kt)
                    <tr>
                        <td class="col-no">{{ $i + 1 }}</td>
                        <td><strong>{{ $kt->nama_siswa }}</strong></td>
                        <td class="col-center"><span class="badge badge-info">{{ $kt->nama_kelas }}</span></td>
                        <td class="col-center" style="font-weight:600; color:var(--primary);">
                            {{ \Carbon\Carbon::parse($kt->waktu_masuk)->format('H:i') }} WIB
                        </td>
                        <td>{{ $kt->alasan ?? '-' }}</td>
                    </tr>
                    @endforeach
                </tbody>
            </table>
        </div>
    @endif
</div>

{{-- ===== DISPENSASI MENUNGGU ===== --}}
<div class="card">
    <div style="display:flex; justify-content:space-between; align-items:center; margin-bottom:14px;">
        <div class="card-title" style="margin-bottom:0;">
            <i class="ri-file-paper-2-line" style="color:var(--primary);"></i> Dispensasi Perlu Diproses
            @if($dispensasiMenunggu->isNotEmpty())
                <span class="badge badge-warning" style="margin-left:6px;">{{ $dispensasiMenunggu->count() }}</span>
            @endif
        </div>
        <a href="{{ route('Dispensasi.index') }}" class="btn btn-sm btn-secondary">
            Semua <i class="ri-arrow-right-line"></i>
        </a>
    </div>
    @if($dispensasiMenunggu->isEmpty())
        <div class="empty-state">
            <i class="ri-file-paper-2-line"></i>
            <p>Tidak ada dispensasi yang menunggu</p>
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
                    @foreach($dispensasiMenunggu as $i => $d)
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

{{-- ===== DAFTAR TAMU ===== --}}
<div class="card">
    <div style="display:flex; justify-content:space-between; align-items:center; margin-bottom:14px;">
        <div class="card-title" style="margin-bottom:0;">
            <i class="ri-user-received-line" style="color:var(--primary);"></i> Daftar Tamu
            <span class="badge badge-primary" style="margin-left:6px;">{{ $daftarTamu->count() }}</span>
        </div>
        <a href="{{ route('DaftarTamu.index') }}" class="btn btn-sm btn-secondary">
            Lihat Selengkapnya <i class="ri-arrow-right-line"></i>
        </a>
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
                        <th>No</th>
                        <th>Tanggal</th>
                        <th>Nama</th>
                        <th>Lembaga / Organisasi</th>
                        <th>Orang Dituju</th>
                        <th>Tujuan</th>
                    </tr>
                </thead>
                <tbody>
                    @foreach($daftarTamu as $i => $t)
                    <tr>
                        <td class="col-no">{{ $i + 1 }}</td>
                        <td class="col-center" style="white-space:nowrap;">{{ \Carbon\Carbon::parse($t->tanggal_kunjungan)->translatedFormat('d M Y') }}</td>
                        <td><strong>{{ $t->nama }}</strong></td>
                        <td>{{ $t->lembaga_organisasi ?? '-' }}</td>
                        <td>{{ $t->orang_yang_dituju ?? '-' }}</td>
                        <td>{{ $t->tujuan_kunjungan ?? '-' }}</td>
                    </tr>
                    @endforeach
                </tbody>
            </table>
        </div>
    @endif
</div>

@endsection