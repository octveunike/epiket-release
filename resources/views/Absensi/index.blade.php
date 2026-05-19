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
            <label class="form-label">Dari Tanggal</label>
            <input type="date" name="dari" class="form-control" value="{{ request('dari') }}">
        </div>
        <div class="form-group" style="margin-bottom:0;min-width:160px;">
            <label class="form-label">Sampai Tanggal</label>
            <input type="date" name="sampai" class="form-control" value="{{ request('sampai') }}">
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
        <div class="form-group" style="margin-bottom:0;min-width:180px;">
            <label class="form-label">Periode Akademik</label>
            <select name="periode_akademik_id" class="form-control">
                @foreach ($periodeList as $p)
                    <option value="{{ $p->id }}" {{ $periodeId == $p->id ? 'selected' : '' }}>
                        {{ $p->nama_periode }}
                    </option>
                @endforeach
            </select>
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
        <table id="tableAbsensi" class="dt-table">
            <thead>
                <tr>
                    <th class="col-no">No</th>
                    <th>Tanggal</th>
                    <th>Kelas</th>
                    <th class="col-center">Status</th>
                    <th class="col-center">Aksi</th>
                    <th>Update Terakhir</th>
                </tr>
            </thead>
            <tbody>
                @forelse ($absensiList as $absensi)
                    @php
                        // Hanya status Menunggu Pengisian (1) dan Perlu Revisi (6) yang masih bisa di-edit/hapus.
                        // Status sudah-submit (Menunggu Wali, Menunggu Piket, Menunggu Pembina, Disetujui) → Detail saja.
                        $perluRevisi = $absensi->status_validasi_id == $statusPerluRevisiId;
                        $belumDiisi  = $absensi->status_validasi_id == $statusMenungguPengisianId;
                        $editable    = $belumDiisi || $perluRevisi;
                    @endphp
                    <tr>
                        <td class="col-no">{{ $loop->iteration }}</td>
                        <td><strong>{{ \Carbon\Carbon::parse($absensi->tanggal)->locale('id')->translatedFormat('l, d F Y') }}</strong></td>
                        <td>{{ $absensi->kelas->nama_kelas ?? '—' }}</td>
                        <td class="col-center">
                            <span style="font-size:13px;color:var(--text-main);">
                                {{ $absensi->statusValidasi->nama_status ?? 'Menunggu Pengisian' }}
                            </span>
                        </td>
                        <td class="col-center">
                            @if ($editable)
                                <a href="{{ route('Absensi.isiAbsensi', $absensi->id) }}"
                                    class="btn btn-sm btn-primary">
                                    <i class="ri-edit-line"></i> {{ $perluRevisi ? 'Edit' : 'Isi Absensi' }}
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
                        <td>{{ $absensi->userUpdate->nama ?? $absensi->userInput->nama ?? 'Auto' }}</td>
                    </tr>
                @empty
                @endforelse
            </tbody>
        </table>
    </div>
</div>

{{-- Tabel History (Disetujui) --}}
<h3 style="margin-top:24px;margin-bottom:12px;font-size:16px;color:var(--text-main);">
    <i class="ri-history-line"></i> History Absensi (Disetujui)
</h3>
<div class="card">
    <div class="table-responsive">
        <table id="tableAbsensiHistory" class="dt-table">
            <thead>
                <tr>
                    <th class="col-no">No</th>
                    <th>Tanggal Absensi</th>
                    <th>Kelas</th>
                    <th>Tanggal Validasi</th>
                    <th class="col-center">Aksi</th>
                    <th>Update Terakhir</th>
                </tr>
            </thead>
            <tbody>
                @forelse ($historyList as $absensi)
                    <tr>
                        <td class="col-no">{{ $loop->iteration }}</td>
                        <td><strong>{{ \Carbon\Carbon::parse($absensi->tanggal)->locale('id')->translatedFormat('l, d F Y') }}</strong></td>
                        <td>{{ $absensi->kelas->nama_kelas ?? '—' }}</td>
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
                                <i class="ri-eye-line"></i> Detail Absensi
                            </a>
                        </td>
                        <td>{{ $absensi->userUpdate->nama ?? $absensi->userInput->nama ?? 'Auto' }}</td>
                    </tr>
                @empty
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