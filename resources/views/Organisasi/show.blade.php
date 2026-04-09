@extends('layouts.app')

@section('content')

    <div class="page-header">
        <div>
            <div class="breadcrumb">Admin / <a href="{{ route('Organisasi.index') }}" style="color:var(--primary);">Data Organisasi</a> / Detail</div>
            <h2>{{ $Organisasi->nama_organisasi }}</h2>
        </div>
        <a href="{{ route('Organisasi.edit', $Organisasi->id) }}" class="btn btn-primary">
            <i class="ri-edit-2-line"></i> Edit Organisasi
        </a>
    </div>

    {{-- Info card --}}
    <div class="card" style="margin-bottom:16px;">
        <div style="display:grid; grid-template-columns:repeat(auto-fill,minmax(200px,1fr)); gap:16px;">
            <div class="info-item">
                <div class="info-label"><i class="ri-team-line"></i> Nama Organisasi</div>
                <div class="info-value">{{ $Organisasi->nama_organisasi }}</div>
            </div>
            <div class="info-item">
                <div class="info-label"><i class="ri-user-star-line"></i> Pembina</div>
                <div class="info-value">{{ $Organisasi->pembina->nama_guru ?? '—' }}</div>
            </div>
            <div class="info-item">
                <div class="info-label"><i class="ri-group-line"></i> Jumlah Anggota</div>
                <div class="info-value">
                    <span class="anggota-count-badge">{{ $Organisasi->siswaOrganisasi->count() }} siswa</span>
                </div>
            </div>
            @if ($Organisasi->keterangan)
            <div class="info-item" style="grid-column: 1 / -1;">
                <div class="info-label"><i class="ri-file-text-line"></i> Keterangan</div>
                <div class="info-value">{{ $Organisasi->keterangan }}</div>
            </div>
            @endif
        </div>
    </div>

    {{-- Daftar anggota --}}
    <div class="card">
        <div style="display:flex; align-items:center; justify-content:space-between; margin-bottom:16px;">
            <h3 style="font-size:14px; font-weight:700; color:var(--text-main); display:flex; align-items:center; gap:7px; margin:0;">
                <i class="ri-group-line" style="color:var(--primary);"></i> Daftar Anggota
            </h3>
            <input type="text" id="searchAnggota" class="form-control"
                placeholder="&#xEE06;  Cari nama…" style="max-width:260px;">
        </div>

        <div class="table-responsive">
            <table>
                <thead>
                    <tr>
                        <th style="width:5%; text-align:center;">No</th>
                        <th>Nama Siswa</th>
                        <th>Kelas</th>
                        <th style="width:15%; text-align:center;">Tanggal Bergabung</th>
                    </tr>
                </thead>
                <tbody id="anggotaBody">
                    @forelse ($Organisasi->siswaOrganisasi as $so)
                        <tr data-nama="{{ strtolower($so->siswa->nama_siswa ?? '') }}">
                            <td style="text-align:center;">{{ $loop->iteration }}</td>
                            <td>
                                <div style="display:flex; align-items:center; gap:10px;">
                                    <div class="siswa-mini-ava">
                                        {{ strtoupper(substr($so->siswa->nama_siswa ?? '?', 0, 2)) }}
                                    </div>
                                    <span style="font-weight:600;">{{ $so->siswa->nama_siswa ?? '—' }}</span>
                                </div>
                            </td>
                            <td>{{ $so->siswa->kelas->nama_kelas ?? '—' }}</td>
                            <td style="text-align:center; color:var(--text-muted); font-size:13px;">
                                {{ $so->tanggal_input ? \Carbon\Carbon::parse($so->tanggal_input)->format('d M Y') : '—' }}
                            </td>
                        </tr>
                    @empty
                        <tr>
                            <td colspan="4" style="text-align:center; color:var(--text-muted); padding:32px;">
                                <i class="ri-user-unfollow-line" style="font-size:32px; display:block; margin-bottom:8px;"></i>
                                Belum ada anggota di organisasi ini
                            </td>
                        </tr>
                    @endforelse
                </tbody>
            </table>
        </div>
    </div>

    <div style="margin-top:16px;">
        <a href="{{ route('Organisasi.index') }}" class="btn btn-secondary">
            <i class="ri-arrow-left-line"></i> Kembali
        </a>
    </div>

@endsection

@push('styles')
<style>
    .info-item { display:flex; flex-direction:column; gap:4px; }
    .info-label { font-size:11.5px; font-weight:600; color:var(--text-muted);
        display:flex; align-items:center; gap:5px; text-transform:uppercase; letter-spacing:.05em; }
    .info-label i { color:var(--primary); }
    .info-value { font-size:14px; font-weight:600; color:var(--text-main); }

    .anggota-count-badge {
        display: inline-flex; align-items:center; gap:5px;
        padding: 4px 12px; border-radius:20px;
        background:#e8f5e9; color:#2e7d32; font-size:12px; font-weight:600;
    }

    .siswa-mini-ava {
        width:32px; height:32px; border-radius:50%;
        background:#e8f5e9; color:#2e7d32;
        display:flex; align-items:center; justify-content:center;
        font-size:11px; font-weight:700; flex-shrink:0;
    }
</style>
@endpush

@push('scripts')
<script>
    document.getElementById('searchAnggota').addEventListener('input', function() {
        const q = this.value.toLowerCase();
        document.querySelectorAll('#anggotaBody tr[data-nama]').forEach(row => {
            row.style.display = row.dataset.nama.includes(q) ? '' : 'none';
        });
    });
</script>
@endpush