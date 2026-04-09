@extends('layouts.app')

@section('content')

<div class="page-header">
    <div>
        <div class="breadcrumb">Admin / <span class="breadcrumb-link">Data Absensi</span></div>
        <h2>Data Absensi</h2>
    </div>
    <a href="{{ route('Absensi.create') }}" class="btn btn-primary">
        <i class="ri-add-line"></i> Tambah Absensi
    </a>
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
            <select name="kelas_id" class="form-control">
                <option value="">Semua Kelas</option>
                @foreach ($kelas as $k)
                    <option value="{{ $k->id }}" {{ request('kelas_id') == $k->id ? 'selected' : '' }}>
                        {{ $k->nama_kelas }}
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
                                {{-- Belum diisi → Isi Absensi --}}
                                <a href="{{ route('Absensi.isiAbsensi', $absensi->id) }}"
                                    class="btn btn-sm btn-primary">
                                    <i class="ri-edit-line"></i> Isi Absensi
                                </a>
                            @elseif (!$disetujui)
                                {{-- Sudah diisi tapi belum final → Edit + Hapus saja --}}
                                <a href="{{ route('Absensi.isiAbsensi', $absensi->id) }}"
                                    class="btn btn-sm btn-primary">
                                    <i class="ri-edit-line"></i> Edit
                                </a>
                                <button type="button" class="btn btn-sm btn-danger"
                                    onclick="showDeleteModal({{ $absensi->id }}, '{{ \Carbon\Carbon::parse($absensi->tanggal)->format('d/m/Y') }}', '{{ $absensi->kelas->nama_kelas ?? '' }}')">
                                    <i class="ri-delete-bin-line"></i> Hapus
                                </button>
                            @else
                                {{-- Sudah disetujui → Detail saja --}}
                                <a href="{{ route('Absensi.show', $absensi->id) }}"
                                    class="btn btn-sm btn-primary">
                                    <i class="ri-eye-line"></i> Detail
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

<div class="modal-overlay" id="deleteModal">
    <div class="modal-box">
        <div class="modal-header">
            <span class="modal-title">Konfirmasi Hapus</span>
            <button class="modal-close" onclick="closeDeleteModal()"><i class="ri-close-line"></i></button>
        </div>
        <div class="modal-body">
            <div class="modal-icon danger"><i class="ri-error-warning-line"></i></div>
            <p class="modal-confirm-text">
                Hapus data absensi<br>
                <strong id="deleteLabel" class="modal-confirm-label"></strong>?<br>
                <small class="text-danger-sm">Semua detail absensi siswa di data ini akan ikut terhapus.</small>
            </p>
        </div>
        <div class="modal-footer">
            <button type="button" class="btn btn-secondary" onclick="closeDeleteModal()">Batal</button>
            <form id="delete-form" method="POST" style="display:inline;">
                @csrf @method('DELETE')
                <button type="submit" class="btn btn-danger"><i class="ri-delete-bin-line"></i> Hapus</button>
            </form>
        </div>
    </div>
</div>

@endsection

@push('scripts')
<script>
    function showDeleteModal(id, tanggal, kelas) {
        document.getElementById('deleteLabel').textContent = tanggal + ' — ' + kelas;
        document.getElementById('delete-form').action = "{{ route('Absensi.destroy', '') }}/" + id;
        document.getElementById('deleteModal').classList.add('active');
    }
    function closeDeleteModal() {
        document.getElementById('deleteModal').classList.remove('active');
    }
    document.getElementById('deleteModal').addEventListener('click', function(e) {
        if (e.target === this) closeDeleteModal();
    });
</script>
@endpush