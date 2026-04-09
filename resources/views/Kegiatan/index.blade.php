@extends('layouts.app')

@section('content')

    {{-- Page Header --}}
    <div class="page-header">
        <div>
            <div class="breadcrumb">Menu / Daftar Kegiatan</div>
            <h2>Daftar Kegiatan</h2>
        </div>
        <a href="{{ route('Kegiatan.create') }}" class="btn btn-primary">
            <i class="ri-add-line"></i> Tambah Kegiatan
        </a>
    </div>

    {{-- Alert --}}
    @if (session('success'))
        <div class="alert alert-success" id="success-alert">
            <i class="ri-checkbox-circle-line"></i> {{ session('success') }}
        </div>
        <script>
            setTimeout(function() {
                document.getElementById('success-alert').remove();
            }, 3000);
        </script>
    @endif

    {{-- Table Card --}}
    <div class="card">
        <div class="table-responsive">
            <table>
                <thead>
                    <tr>
                        <th style="width:5%; text-align:center;">No</th>
                        <th>Judul Kegiatan</th>
                        <th>Jenis Kegiatan</th>
                        <th>Tanggal Awal</th>
                        <th>Tanggal Akhir</th>
                        <th>Deskripsi</th>
                        <th style="text-align:center;">Kuota</th>
                        <th>Penyelenggara</th>
                        <th style="width:15%; text-align:center;">Aksi</th>
                    </tr>
                </thead>
                <tbody>
                    @forelse ($data as $i => $val)
                        <tr>
                            <td style="text-align:center;">{{ $loop->iteration }}</td>
                            <td>{{ $val->title }}</td>
                            <td>{{ $val->tipe_kegiatan }}</td>
                            <td>{{ date('d M Y', strtotime($val->tgl_awal)) }}</td>
                            <td>{{ date('d M Y', strtotime($val->tgl_akhir)) }}</td>
                            <td style="max-width:200px;">
                                <span class="text-clamp">{{ $val->deskripsi }}</span>
                            </td>
                            <td style="text-align:center;">{{ $val->kuota_peserta }}</td>
                            <td>{{ $val->penyelenggara }}</td>
                            <td style="text-align:center;">
                                <a href="{{ route('Kegiatan.edit', $val->id) }}" class="btn btn-sm btn-primary">
                                    <i class="ri-edit-2-line"></i> Edit
                                </a>
                                <button type="button" class="btn btn-sm btn-danger" onclick="showDeleteModal({{ $val->id }})">
                                    <i class="ri-delete-bin-line"></i> Hapus
                                </button>
                            </td>
                        </tr>
                    @empty
                        <tr>
                            <td colspan="9" style="text-align:center; color:var(--text-muted); padding:32px;">
                                <i class="ri-inbox-line" style="font-size:32px; display:block; margin-bottom:8px;"></i>
                                Belum ada data kegiatan
                            </td>
                        </tr>
                    @endforelse
                </tbody>
            </table>
        </div>
    </div>

    {{-- Delete Modal --}}
    <div class="modal-overlay" id="deleteModal">
        <div class="modal-box">
            <div class="modal-header">
                <span class="modal-title">Konfirmasi Penghapusan</span>
                <button class="modal-close" onclick="closeDeleteModal()">
                    <i class="ri-close-line"></i>
                </button>
            </div>
            <div class="modal-body">
                <div class="modal-icon danger">
                    <i class="ri-error-warning-line"></i>
                </div>
                <p style="text-align:center; color:var(--text-muted); margin-top:12px;">
                    Apakah Anda yakin ingin menghapus data ini?<br>
                    <strong style="color:#ef4444;">Data yang dihapus tidak dapat dikembalikan!</strong>
                </p>
            </div>
            <div class="modal-footer">
                <button type="button" class="btn btn-secondary" onclick="closeDeleteModal()">Batal</button>
                <form id="delete-form" method="POST" style="display:inline;">
                    @csrf
                    @method('PUT')
                    <button type="submit" class="btn btn-danger">
                        <i class="ri-delete-bin-line"></i> Hapus
                    </button>
                </form>
            </div>
        </div>
    </div>

@endsection

@push('scripts')
<script>
    function showDeleteModal(id) {
        document.getElementById('delete-form').action = "{{ route('Kegiatan.destroy', '') }}/" + id;
        document.getElementById('deleteModal').classList.add('active');
    }

    function closeDeleteModal() {
        document.getElementById('deleteModal').classList.remove('active');
    }

    // Close modal when clicking overlay
    document.getElementById('deleteModal').addEventListener('click', function(e) {
        if (e.target === this) closeDeleteModal();
    });
</script>
@endpush