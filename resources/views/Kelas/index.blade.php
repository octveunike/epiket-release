@extends('layouts.app')

@section('content')

<div class="page-header">
    <div>
        <div class="breadcrumb">Admin / Master Data / <a href="{{ route('Kelas.index') }}" class="breadcrumb-link">Data Kelas</a></div>
        <h2>Data Kelas</h2>
    </div>
    <a href="{{ route('Kelas.create') }}" class="btn btn-primary">
        <i class="ri-add-line"></i> Tambah Kelas
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

<div class="card">
    <div class="table-responsive">
        <table id="tableKelas" class="dt-table">
            <thead>
                <tr>
                    <th class="col-no">No</th>
                    <th>Nama Kelas</th>
                    <th>Wali Kelas</th>
                    <th>Ketua Kelas</th>
                    <th>Periode Akademik</th>
                    <th class="col-center">Aksi</th>
                </tr>
            </thead>
            <tbody>
                @forelse ($data as $val)
                    <tr>
                        <td class="col-no">{{ $loop->iteration }}</td>
                        <td class="col-center"><strong>{{ $val->nama_kelas }}</strong></td>
                        <td>{{ $val->waliKelas->nama_guru ?? '' }}</td>
                        <td>{{ $val->ketuaKelas->nama_siswa ?? '' }}</td>
                        <td class="col-center">{{ $val->periodeAkademik->nama_periode ?? '' }}</td>
                        <td class="col-center" style="white-space:nowrap;">
                            <a href="{{ route('Kelas.edit', $val->id) }}" class="btn btn-sm btn-primary">
                                <i class="ri-edit-2-line"></i> Edit
                            </a>
                            <button type="button" class="btn btn-sm btn-danger"
                                onclick="showDeleteModal({{ $val->id }}, '{{ $val->nama_kelas }}')">
                                <i class="ri-delete-bin-line"></i> Hapus
                            </button>
                        </td>
                    </tr>
                @empty
                @endforelse
            </tbody>
        </table>
    </div>
</div>

<div class="confirm-overlay" id="deleteModal">
    <div class="confirm-box">
        <div class="confirm-icon">!</div>
        <h3>Hapus Kelas?</h3>
        <p>Kelas <strong id="deleteLabel"></strong> akan dihapus.<br>Data yang dihapus tidak dapat dikembalikan.</p>
        <div class="confirm-actions">
            <form id="delete-form" method="POST">
                @csrf @method('DELETE')
                <button type="submit" class="btn btn-danger">Ya, Hapus</button>
            </form>
            <button onclick="closeDeleteModal()" class="btn btn-secondary">Batal</button>
        </div>
    </div>
</div>

@endsection

@push('scripts')
<script>
function showDeleteModal(id, nama) {
    document.getElementById('deleteLabel').textContent = nama;
    document.getElementById('delete-form').action = "{{ route('Kelas.destroy', '') }}/" + id;
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