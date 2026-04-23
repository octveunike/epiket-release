@extends('layouts.app')

@section('content')

<div class="page-header">
    <div>
        <div class="breadcrumb">Admin / <span class="breadcrumb-link">Data Absensi</span></div>
        <h2>Data Absensi</h2>
    </div>
    @if(auth()->user()->hasRole(['Admin','Petugas Piket']))
    <div style="display:flex; gap:12px;">
        <button type="button" class="btn btn-import" onclick="showGenerateModal()" style="margin: 0;">
            <i class="ri-refresh-line"></i> Generate Absensi Hari Ini
        </button>

        <a href="{{ route('Absensi.create') }}" class="btn btn-primary">
            <i class="ri-add-line"></i> Tambah Absensi
        </a>
    </div>
    @endif
</div>

@if (session('success'))
    <div class="alert alert-success" id="success-alert">
        <i class="ri-checkbox-circle-line"></i> {{ session('success') }}
    </div>
    <script>setTimeout(()=>document.getElementById('success-alert')?.remove(), 3000);</script>
@endif
@if (session('error'))
    <div class="alert alert-danger"><i class="ri-error-warning-line"></i> {{ session('error') }}</div>
@endif

<div class="card" style="padding:14px 20px;margin-bottom:16px;">
    <form method="GET" action="{{ route('Absensi.index') }}" style="display:flex;align-items:flex-end;gap:12px;flex-wrap:wrap;">
        <div class="form-group" style="margin-bottom:0;min-width:160px;">
            <label class="form-label">Tanggal</label>
            <input type="date" name="tanggal" class="form-control" value="{{ request('tanggal') }}">
        </div>
        <div class="form-group" style="margin-bottom:0;min-width:180px;">
            <label class="form-label">Kelas</label>
            @php $ketuaKelas = auth()->user()->hasRole('Ketua Kelas') ? auth()->user()->ketuaKelas() : null; @endphp
            @if ($ketuaKelas)
                <input type="hidden" name="kelas_id" value="{{ $ketuaKelas->id }}">
                <input type="text" class="form-control" value="{{ $ketuaKelas->nama_kelas }}" readonly tabindex="-1">
            @else
                <select name="kelas_id" class="form-control">
                    <option value="">Semua Kelas</option>
                    @foreach ($kelas as $k)
                        <option value="{{ $k->id }}" {{ request('kelas_id') == $k->id ? 'selected' : '' }}>
                            {{ $k->nama_kelas }}
                        </option>
                    @endforeach
                </select>
            @endif
        </div>
        <button type="submit" class="btn btn-primary" style="margin-bottom:0;">
            <i class="ri-filter-line"></i> Filter
        </button>
        <a href="{{ route('Absensi.index') }}" class="btn btn-secondary" style="margin-bottom:0;">
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
                    <th>Tanggal</th>
                    <th>Kelas</th>
                    <th>Periode Akademik</th>
                    <th class="col-center">Status</th>
                    <th class="col-center">Aksi</th>
                </tr>
            </thead>
            <tbody>
                @forelse ($absensiList as $absensi)
                    @php
                        $sudahDiisi = $absensi->status_verifikasi_id !== $statusMenungguPengisianId;
                        $disetujui  = in_array($absensi->status_verifikasi_id, [5]);
                    @endphp
                    <tr>
                        <td class="col-no">{{ $loop->iteration }}</td>
                        <td>
                            <strong>{{ \Carbon\Carbon::parse($absensi->tanggal)->translatedFormat('d F Y') }}</strong>
                            <div class="text-muted-sm">{{ \Carbon\Carbon::parse($absensi->tanggal)->translatedFormat('l') }}</div>
                        </td>
                        <td>{{ $absensi->kelas->nama_kelas ?? '—' }}</td>
                        <td>{{ $absensi->periodeAkademik->nama_periode ?? '—' }}</td>
                        <td class="col-center">
                            <span style="font-size:13px;color:var(--text-main);">
                                {{ $absensi->statusVerifikasi->nama_status ?? 'Menunggu Pengisian' }}
                            </span>
                        </td>
                        <td class="col-center">
                            @if (!$sudahDiisi)
                                <a href="{{ route('Absensi.isiAbsensi', $absensi->id) }}"
                                    class="btn btn-sm btn-primary">
                                    <i class="ri-edit-line"></i> Isi Absensi
                                </a>
                            @elseif (!$disetujui)
                                <a href="{{ route('Absensi.isiAbsensi', $absensi->id) }}"
                                    class="btn btn-sm btn-primary">
                                    <i class="ri-edit-line"></i> Edit
                                </a>
                                <button type="button" class="btn btn-sm btn-danger"
                                    onclick="showDeleteModal({{ $absensi->id }}, '{{ \Carbon\Carbon::parse($absensi->tanggal)->format('d/m/Y') }}', '{{ $absensi->kelas->nama_kelas ?? '' }}')">
                                    <i class="ri-delete-bin-line"></i> Hapus
                                </button>
                            @else
                                <a href="{{ route('Absensi.show', $absensi->id) }}"
                                    class="btn btn-sm btn-info">
                                    <i class="ri-eye-line"></i> Detail Absensi
                                </a>
                            @endif
                        </td>
                    </tr>
                @empty
                    <tr>
                        <td colspan="6" class="td-empty">
                            <i class="ri-calendar-check-line" style="font-size:32px;display:block;margin-bottom:8px;"></i>
                            Belum ada data absensi.
                        </td>
                    </tr>
                @endforelse
            </tbody>
        </table>
    </div>
</div>

<div class="confirm-overlay" id="generateModal">
    <div class="confirm-box">
        <div class="confirm-icon" style="background:#fef3c7; color:#d97706;">?</div>
        <h3>Generate Absensi</h3>
        <p>Sistem akan membuat absensi semua kelas hari ini.</p>
        <div class="confirm-actions">
            <form action="{{ route('Absensi.generate') }}" method="POST">
                @csrf
                <button type="submit" class="btn btn-primary">Generate</button>
            </form>
            <button onclick="closeGenerateModal()" class="btn btn-secondary">Batal</button>
        </div>
    </div>
</div>

<div class="confirm-overlay" id="deleteModal">
    <div class="confirm-box">
        <div class="confirm-icon">!</div>
        <h3>Hapus data absensi <strong id="deleteLabel"></strong>?</h3>
        <p>Semua detail absensi siswa di data ini akan ikut terhapus.</p>
        <div class="confirm-actions">
            <form id="delete-form" method="POST">
                @csrf 
                @method('DELETE')
                <button type="submit" class="btn btn-danger">Ya, Hapus</button>
            </form>
            <button onclick="closeDeleteModal()" class="btn btn-secondary">Batal</button>
        </div>
    </div>
</div>

@endsection

@push('scripts')
<script>
    function showGenerateModal() {
        document.getElementById('generateModal').classList.add('show');
    }
    function closeGenerateModal() {
        document.getElementById('generateModal').classList.remove('show');
    }
    document.getElementById('generateModal').addEventListener('click', function(e) {
        if (e.target === this) closeGenerateModal();
    });

    function showDeleteModal(id, tanggal, kelas) {
        document.getElementById('deleteLabel').textContent = tanggal + ' — ' + kelas;
        document.getElementById('delete-form').action = "{{ route('Absensi.destroy', '') }}/" + id;
        document.getElementById('deleteModal').classList.add('show');
    }
    function closeDeleteModal() {
        document.getElementById('deleteModal').classList.remove('show');
    }
    document.getElementById('deleteModal').addEventListener('click', function(e) {
        if (e.target === this) closeDeleteModal();
    });
</script>
@endpush