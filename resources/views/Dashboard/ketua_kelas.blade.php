@extends('layouts.app')

@section('title', 'Dashboard Ketua Kelas')

@section('content')

<div class="page-header">
    <div>
        <div class="breadcrumb">Dashboard</div>
        <h2>Dashboard Ketua Kelas</h2>
        <div style="font-size:12px; color:var(--text-muted); margin-top:2px;">
            {{ \Carbon\Carbon::now()->translatedFormat('l, d F Y') }}
        </div>
    </div>
    @if($viewMode === 'ketua' || !isset($kelas) || !$kelas)
    <form method="GET" action="{{ route('admin.index') }}" style="display:flex; gap:8px; align-items:center;">
        <input type="hidden" name="view" value="ketua">
        <select name="kelas_id" class="form-control" style="width:180px;">
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
    <div class="empty-state" style="padding:60px 24px;">
        <i class="ri-user-star-line"></i>
        <p>
            @if($viewMode === 'ketua') Pilih kelas di atas untuk melihat dashboard
            @else Anda belum terdaftar sebagai Ketua Kelas. Hubungi Admin.
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
        @if($absensiHariIni)
            <span class="ab-infobar-sep">|</span>
            @php
                $cls = match((int)$absensiHariIni->status_verifikasi_id) {
                    5=>'badge-success', 3=>'badge-warning', 1=>'badge-info', default=>'badge-info'
                };
            @endphp
            <span class="badge {{ $cls }}">{{ $statusVerifHariIni->nama_status ?? '-' }}</span>
        @endif
    </div>
    @if(!$viewMode)
        @if(!$absensiHariIni)
            <a href="{{ route('Absensi.create') }}" class="btn btn-primary">
                <i class="ri-add-line"></i> Input Absensi Hari Ini
            </a>
        @else
            <a href="{{ route('Absensi.show', $absensiHariIni->id) }}" class="btn btn-secondary">
                <i class="ri-eye-line"></i> Lihat Absensi Hari Ini
            </a>
        @endif
    @endif
</div>

{{-- ===== STAT HARI INI ===== --}}
<div class="ab-stat-grid" style="grid-template-columns:repeat(5,1fr); margin-bottom:20px;">
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

{{-- ===== FOKUS UTAMA: ABSENSI PERLU DIISI ===== --}}
<div class="card">
    <div style="display:flex; justify-content:space-between; align-items:center; margin-bottom:14px;">
        <div>
            <div class="card-title" style="margin-bottom:2px;">
                <i class="ri-edit-2-line" style="color:var(--primary);"></i> Absensi yang Perlu Diisi
            </div>
            <div style="font-size:12px; color:var(--text-muted);">
                Daftar absensi yang belum diisi untuk kelas ini
            </div>
        </div>
        @if($absensiPerluDiisi->isNotEmpty())
            <span class="badge badge-warning" style="padding:6px 14px; font-size:12px;">
                {{ $absensiPerluDiisi->count() }} Belum Diisi
            </span>
        @endif
    </div>

    @if($absensiPerluDiisi->isEmpty())
        <div class="empty-state">
            <i class="ri-checkbox-circle-line"></i>
            <p>Tidak ada absensi yang perlu diisi saat ini</p>
        </div>
    @else
        <div class="table-responsive">
            <table>
                <thead>
                    <tr>
                        <th>No</th>
                        <th>Tanggal</th>
                        <th>Status</th>
                        <th>Aksi</th>
                    </tr>
                </thead>
                <tbody>
                    @foreach($absensiPerluDiisi as $i => $ab)
                    <tr>
                        <td class="col-no">{{ $i + 1 }}</td>
                        <td>
                            <strong>{{ \Carbon\Carbon::parse($ab->tanggal)->translatedFormat('l, d M Y') }}</strong>
                            @if(\Carbon\Carbon::parse($ab->tanggal)->isToday())
                                <span class="badge badge-success" style="margin-left:4px; font-size:10px;">Hari Ini</span>
                            @endif
                        </td>
                        <td class="col-center">
                            <span class="badge badge-info">{{ $ab->nama_verifikasi }}</span>
                        </td>
                        <td class="col-center">
                            <a href="{{ route('Absensi.isiAbsensi', $ab->id) }}" class="btn btn-sm btn-primary">
                                <i class="ri-edit-2-line"></i> Isi Absensi
                            </a>
                        </td>
                    </tr>
                    @endforeach
                </tbody>
            </table>
        </div>
    @endif
</div>

{{-- ===== SISWA TIDAK HADIR + RIWAYAT ===== --}}
<div style="display:grid; grid-template-columns:1fr 1fr; gap:16px;">

    {{-- Siswa Tidak Hadir Hari Ini --}}
    <div class="card" style="margin-bottom:0;">
        <div style="display:flex; justify-content:space-between; align-items:center; margin-bottom:14px;">
            <div class="card-title" style="margin-bottom:0;">
                <i class="ri-user-unfollow-line" style="color:var(--primary);"></i> Tidak Hadir Hari Ini
            </div>
            @if($siswaAbsen->isNotEmpty())
                <span class="badge badge-danger">{{ $siswaAbsen->count() }}</span>
            @endif
        </div>
        @if(!$absensiHariIni)
            <div class="empty-state">
                <i class="ri-calendar-line"></i>
                <p>Absensi hari ini belum diisi</p>
            </div>
        @elseif($siswaAbsen->isEmpty())
            <div class="empty-state">
                <i class="ri-checkbox-circle-line"></i>
                <p>Semua siswa hadir hari ini 🎉</p>
            </div>
        @else
            @foreach($siswaAbsen as $s)
            @php
                $tipe = strtolower(substr($s->status_label,0,1));
                $entryClass = match($tipe) { 'i'=>'izin', 's'=>'sakit', default=>'alpha' };
                $avaClass   = match($tipe) { 'i'=>'ea-I', 's'=>'ea-S', default=>'ea-A' };
                $badgeClass = match($tipe) { 'i'=>'eb-I', 's'=>'eb-S', default=>'eb-A' };
            @endphp
            <div class="entry-item {{ $entryClass }}">
                <div class="entry-ava {{ $avaClass }}">{{ strtoupper(substr($s->status_label,0,1)) }}</div>
                <div class="entry-body">
                    <div class="entry-name">{{ $s->nama_siswa }}</div>
                    <div class="entry-meta">
                        <span class="entry-badge {{ $badgeClass }}">{{ $s->status_label }}</span>
                        @if($s->keterangan) <span>{{ $s->keterangan }}</span> @endif
                    </div>
                </div>
            </div>
            @endforeach
        @endif
    </div>

    {{-- Riwayat 7 Hari --}}
    <div class="card" style="margin-bottom:0;">
        <div style="display:flex; justify-content:space-between; align-items:center; margin-bottom:14px;">
            <div class="card-title" style="margin-bottom:0;">
                <i class="ri-history-line" style="color:var(--primary);"></i> Riwayat 7 Hari Terakhir
            </div>
            <a href="{{ route('Absensi.index') }}" class="btn btn-sm btn-secondary">
                Semua <i class="ri-arrow-right-line"></i>
            </a>
        </div>
        @if($riwayatAbsensi->isEmpty())
            <div class="empty-state">
                <i class="ri-file-list-line"></i>
                <p>Belum ada riwayat absensi</p>
            </div>
        @else
            @foreach($riwayatAbsensi as $ab)
            <div class="ab-infobar" style="margin-bottom:8px; padding:10px 14px;">
                <div class="ab-infobar-left" style="font-size:13px;">
                    <i class="ri-calendar-line"></i>
                    <strong>{{ \Carbon\Carbon::parse($ab->tanggal)->translatedFormat('l, d M') }}</strong>
                    <span class="ab-infobar-sep">·</span>
                    <span>{{ $ab->user_input ?? '-' }}</span>
                </div>
                <div style="display:flex; align-items:center; gap:6px;">
                    @php
                        $cls = match((int)$ab->status_verifikasi_id) {
                            5=>'badge-success', 3=>'badge-warning', 1=>'badge-info', default=>'badge-info'
                        };
                    @endphp
                    <span class="badge {{ $cls }}" style="font-size:10.5px;">{{ $ab->nama_verifikasi }}</span>
                    <a href="{{ route('Absensi.show', $ab->id) }}" class="btn btn-sm btn-secondary" style="padding:4px 8px;">
                        <i class="ri-eye-line"></i>
                    </a>
                </div>
            </div>
            @endforeach
        @endif
    </div>
</div>

@endif {{-- end if kelas --}}
@endsection