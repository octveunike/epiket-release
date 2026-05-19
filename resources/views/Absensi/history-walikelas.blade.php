@extends('layouts.app')

@section('content')

<div class="page-header">
    <div>
        @php
            $u = auth()->user();
            $breadcrumbRole = $u->hasRole('Admin')
                ? 'Admin'
                : ($u->hasRole('Petugas Piket') ? 'Petugas Piket' : 'Wali Kelas');
        @endphp
        <div class="breadcrumb">{{ $breadcrumbRole }} / <span class="breadcrumb-link">History Validasi</span></div>
        <h2>History Validasi Absensi</h2>
    </div>
    <a href="{{ route('Absensi.validasi.index') }}" class="btn btn-secondary">
        <i class="ri-arrow-left-line"></i> Kembali
    </a>
</div>

{{-- Filter --}}
<div class="card" style="padding:14px 20px;margin-bottom:16px;">
    <form method="GET" action="{{ route('Absensi.validasi.history') }}" style="display:flex;align-items:flex-end;gap:12px;flex-wrap:wrap;">
        <div class="form-group" style="margin-bottom:0;min-width:160px;">
            <label class="form-label">Dari Tanggal</label>
            <input type="date" name="dari" class="form-control" value="{{ request('dari') }}">
        </div>
        <div class="form-group" style="margin-bottom:0;min-width:160px;">
            <label class="form-label">Sampai Tanggal</label>
            <input type="date" name="sampai" class="form-control" value="{{ request('sampai') }}">
        </div>
        <button type="submit" class="btn btn-primary" style="margin-bottom:0;">
            <i class="ri-filter-line"></i> Filter
        </button>
        <a href="{{ route('Absensi.validasi.history') }}" class="btn btn-secondary" style="margin-bottom:0;">
            <i class="ri-refresh-line"></i> Reset
        </a>
    </form>
</div>

<div class="card">
    <div class="table-responsive">
        <table>
            <thead>
                <tr>
                    <th class="col-no">No</th>
                    <th>Tanggal Absensi</th>
                    <th>Kelas</th>
                    <th>Periode Akademik</th>
                    <th>Diupdate Oleh</th>
                    <th>Tanggal Validasi</th>
                    <th class="col-center">Aksi</th>
                </tr>
            </thead>
            <tbody>
                @forelse ($historyList as $absensi)
                    <tr>
                        <td class="col-no">{{ $loop->iteration }}</td>
                        <td>
                            <strong>{{ \Carbon\Carbon::parse($absensi->tanggal)->translatedFormat('d F Y') }}</strong>
                            <div class="text-muted-sm">{{ \Carbon\Carbon::parse($absensi->tanggal)->translatedFormat('l') }}</div>
                        </td>
                        <td>{{ $absensi->kelas->nama_kelas ?? '—' }}</td>
                        <td>{{ $absensi->periodeAkademik->nama_periode ?? '—' }}</td>
                        <td>{{ $absensi->user_update ?? '—' }}</td>
                        <td>
                            @if ($absensi->tanggal_update)
                                {{ \Carbon\Carbon::parse($absensi->tanggal_update)->translatedFormat('d F Y, H:i') }}
                            @else
                                —
                            @endif
                        </td>
                        <td class="col-center">
                            <a href="{{ route('Absensi.show', $absensi->id) }}"
                                class="btn btn-sm btn-secondary">
                                <i class="ri-eye-line"></i> Detail
                            </a>
                        </td>
                    </tr>
                @empty
                    <tr>
                        <td colspan="7" class="td-empty">
                            <i class="ri-history-line" style="font-size:32px;display:block;margin-bottom:8px;"></i>
                            Belum ada history validasi.
                        </td>
                    </tr>
                @endforelse
            </tbody>
        </table>
    </div>
</div>

@endsection