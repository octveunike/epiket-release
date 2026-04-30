@extends('layouts.app')

@section('content')

<div class="page-header">
    <div>
        <div class="breadcrumb">Admin / <span class="breadcrumb-link">Laporan</span></div>
        <h2>Laporan Ketidakhadiran Siswa</h2>
    </div>
    @if(!empty($rows) && count($rows) > 0)
        <form method="GET" action="{{ route('Laporan.export') }}" style="display:inline;">
            <input type="hidden" name="dari"     value="{{ request('dari') }}">
            <input type="hidden" name="sampai"   value="{{ request('sampai') }}">
            <input type="hidden" name="kelas_id" value="{{ request('kelas_id') }}">
            <input type="hidden" name="kategori" value="{{ request('kategori') }}">
            <input type="hidden" name="nama"     value="{{ request('nama') }}">
            <button type="submit" class="btn btn-primary">
                <i class="ri-file-excel-line"></i> Export Excel
            </button>
        </form>
    @endif
</div>

{{-- Filter --}}
<div class="card" style="padding:14px 20px;margin-bottom:16px;">
    <form method="GET" action="{{ route('Laporan.index') }}" style="display:flex;align-items:flex-end;gap:12px;flex-wrap:wrap;">

        <div class="form-group" style="margin-bottom:0;min-width:160px;">
            <label class="form-label">Dari Tanggal</label>
            <input type="date" name="dari" class="form-control" value="{{ request('dari') }}">
        </div>

        <div class="form-group" style="margin-bottom:0;min-width:160px;">
            <label class="form-label">Sampai Tanggal</label>
            <input type="date" name="sampai" class="form-control" value="{{ request('sampai') }}">
        </div>

        <div class="form-group" style="margin-bottom:0;min-width:180px;">
            <label class="form-label">Kelas</label>
            @php
                $u = auth()->user();
                $lockedKelas = $u->hasRole('Ketua Kelas') ? $u->ketuaKelas()
                             : ($u->hasRole('Wali Kelas') ? $u->waliKelas() : null);
            @endphp
            @if ($lockedKelas)
                <input type="hidden" name="kelas_id" value="{{ $lockedKelas->id }}">
                <input type="text" class="form-control" value="{{ $lockedKelas->nama_kelas }}" readonly tabindex="-1">
            @else
                <select name="kelas_id" class="form-control">
                    <option value="">Semua Kelas</option>
                    @foreach($kelasList as $k)
                        <option value="{{ $k->id }}" {{ request('kelas_id') == $k->id ? 'selected' : '' }}>
                            {{ $k->nama_kelas }}
                        </option>
                    @endforeach
                </select>
            @endif
        </div>

        <div class="form-group" style="margin-bottom:0;min-width:160px;">
            <label class="form-label">Kategori</label>
            <select name="kategori" class="form-control">
                <option value="">Semua</option>
                <option value="absensi"       {{ request('kategori') === 'absensi'       ? 'selected' : '' }}>Absensi (Semua)</option>
                <option value="alpha"         {{ request('kategori') === 'alpha'         ? 'selected' : '' }}>Alpha</option>
                <option value="sakit"         {{ request('kategori') === 'sakit'         ? 'selected' : '' }}>Sakit</option>
                <option value="izin"          {{ request('kategori') === 'izin'          ? 'selected' : '' }}>Izin</option>
                <option value="keterlambatan" {{ request('kategori') === 'keterlambatan' ? 'selected' : '' }}>Keterlambatan</option>
                <option value="dispensasi"    {{ request('kategori') === 'dispensasi'    ? 'selected' : '' }}>Dispensasi</option>
            </select>
        </div>

        <div class="form-group" style="margin-bottom:0;min-width:180px;">
            <label class="form-label">Nama Siswa</label>
            <input type="text" name="nama" class="form-control" placeholder="Cari nama..."
                value="{{ request('nama') }}">
        </div>

        <button type="submit" class="btn btn-primary" style="margin-bottom:0;">
            <i class="ri-filter-line"></i> Tampilkan
        </button>
        <a href="{{ route('Laporan.index') }}" class="btn btn-secondary" style="margin-bottom:0;">
            <i class="ri-refresh-line"></i> Reset
        </a>
    </form>
</div>

{{-- Tabel hasil --}}
<div class="card">
    <div class="table-responsive">
        <table>
            <thead>
                <tr>
                    <th class="col-no">No</th>
                    <th>Hari</th>
                    <th>Tanggal</th>
                    <th>Nama Siswa</th>
                    <th>Kelas</th>
                    <th class="col-center">Kategori</th>
                    <th>Deskripsi</th>
                    <th>Keterangan</th>
                    <th>Penginput</th>
                </tr>
            </thead>
            <tbody>
                @forelse($rows as $i => $row)
                    <tr>
                        <td class="col-no">{{ $i + 1 }}</td>
                        <td class="text-muted-sm" >{{ $row['hari'] }}</td>
                        <td class="text-muted-sm">{{ $row['tanggal'] }}</td>
                        <td class="text-muted-sm">{{ $row['nama_siswa'] }}</td>
                        <td class="text-muted-sm">{{ $row['kelas'] }}</td>
                        <td class="col-center">
                            @php
                                $label = $row['kategori'];
                                $style = 'background:#fee2e2;color:#991b1b;'; // default (Absensi Alpha-ish)
                                if ($row['kategori'] === 'Keterlambatan') {
                                    $style = 'background:#fef3c7;color:#92400e;';
                                } elseif ($row['kategori'] === 'Dispensasi') {
                                    $style = 'background:#e0f2fe;color:#075985;';
                                } elseif ($row['kategori'] === 'Absensi') {
                                    $sub   = $row['deskripsi'] ?? '';
                                    $label = $sub ?: '—';
                                    if (stripos($sub, 'izin') !== false) {
                                        $style = 'background:#fef3c7;color:#92400e;';
                                    } elseif (stripos($sub, 'sakit') !== false) {
                                        $style = 'background:#dbeafe;color:#1e40af;';
                                    } elseif (stripos($sub, 'alpha') !== false || stripos($sub, 'alfa') !== false) {
                                        $style = 'background:#fee2e2;color:#991b1b;';
                                    } else {
                                        $style = 'background:#e5e7eb;color:#374151;';
                                    }
                                }
                            @endphp
                            <span style="font-size:12px;padding:2px 8px;border-radius:20px;font-weight:600;{{ $style }}">
                                {{ $label }}
                            </span>
                        </td>
                        <td class="text-muted-sm">{{ $row['deskripsi'] }}</td>
                        <td class="text-muted-sm">{{ $row['keterangan'] }}</td>
                        <td class="text-muted-sm">{{ $row['penginput'] }}</td>
                    </tr>
                @empty
                @endforelse
            </tbody>
        </table>
    </div>
</div>

@endsection