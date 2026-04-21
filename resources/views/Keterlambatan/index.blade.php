@extends('layouts.app')

@section('content')

<div class="page-header">
    <div>
        <div class="breadcrumb">Admin / <span class="breadcrumb-link">Keterlambatan</span></div>
        <h2>Data Keterlambatan</h2>
    </div>
    <a href="{{ route('Keterlambatan.create') }}" class="btn btn-primary">
        <i class="ri-add-line"></i> Catat Keterlambatan
    </a>
</div>

@if (session('success'))
    <div class="alert alert-success" id="s-alert">
        <i class="ri-checkbox-circle-line"></i> {{ session('success') }}
    </div>
    <script>setTimeout(()=>document.getElementById('s-alert')?.remove(),3000);</script>
@endif
@if (session('error'))
    <div class="alert alert-danger"><i class="ri-error-warning-line"></i> {{ session('error') }}</div>
@endif

{{-- Filter --}}
<div class="card" style="padding:14px 20px;margin-bottom:16px;">
    <form method="GET" action="{{ route('Keterlambatan.index') }}" style="display:flex;align-items:flex-end;gap:12px;flex-wrap:wrap;">

        <div class="form-group" style="margin-bottom:0;min-width:160px;">
            <label class="form-label">Dari Tanggal</label>
            <input type="date" name="dari" class="form-control"
                value="{{ request('dari', \Carbon\Carbon::now()->subDays(7)->format('Y-m-d')) }}">
        </div>

        <div class="form-group" style="margin-bottom:0;min-width:160px;">
            <label class="form-label">Sampai Tanggal</label>
            <input type="date" name="sampai" class="form-control"
                value="{{ request('sampai', \Carbon\Carbon::now()->format('Y-m-d')) }}">
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
        <a href="{{ route('Keterlambatan.index') }}" class="btn btn-secondary" style="margin-bottom:0;">
            <i class="ri-refresh-line"></i> Reset
        </a>
    </form>
</div>

<div class="card">
    <div class="table-responsive">
        <table id="tableKeterlambatan" class="dt-table"
            data-destroy-url="{{ route('Keterlambatan.destroy', '') }}">
            <thead>
                <tr>
                    <th style="text-align:center; width:5%;">No</th>
                    <th style="text-align:center;">Tanggal</th>
                    <th style="text-align:center;">Nama Siswa</th>
                    <th style="text-align:center;">Kelas</th>
                    <th style="text-align:center;">Waktu Masuk</th>
                    <th style="text-align:center;">Alasan</th>
                    <th style="text-align:center;">Periode</th>
                    <th style="text-align:center;">Aksi</th>
                </tr>
            </thead>
            <tbody>
                @foreach ($keterlambatan as $kt)
                    <tr>
                        <td class="col-no">{{ $loop->iteration }}</td>
                        <td>
                            <strong>{{ \Carbon\Carbon::parse($kt->absensi->tanggal)->translatedFormat('d F Y') }}</strong>
                            <div class="text-muted-sm">{{ \Carbon\Carbon::parse($kt->absensi->tanggal)->translatedFormat('l') }}</div>
                        </td>
                        <td><strong>{{ $kt->siswa->nama_siswa ?? '—' }}</strong></td>
                        <td>{{ $kt->absensi->kelas->nama_kelas ?? '—' }}</td>
                        <td class="col-center" style="font-weight:600;color:var(--primary);">
                            {{ \Carbon\Carbon::parse($kt->waktu_masuk)->format('H:i') }}
                        </td>
                        <td class="text-muted-sm">{{ $kt->alasan ?: '—' }}</td>
                        <td class="text-muted-sm">{{ $kt->periodeAkademik->nama_periode ?? '—' }}</td>
                        <td class="col-center">
                            <button type="button" class="btn btn-sm btn-danger"
                                onclick="showDeleteModal({{ $kt->id }}, '{{ $kt->siswa->nama_siswa ?? '' }}')">
                                <i class="ri-delete-bin-line"></i> Hapus
                            </button>
                        </td>
                    </tr>
                @endforeach
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
                Hapus keterlambatan<br>
                <strong id="deleteLabel" class="modal-confirm-label"></strong>?<br>
                <small class="text-danger-sm">Status absensi siswa akan dikembalikan ke Hadir.</small>
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
function showDeleteModal(id, nama) {
    document.getElementById('deleteLabel').textContent = nama;
    document.getElementById('delete-form').action = "{{ route('Keterlambatan.destroy', '') }}/" + id;
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