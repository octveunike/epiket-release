@extends('layouts.app')

@section('content')

<div class="page-header">
    <div>
        <div class="breadcrumb">Admin / <span class="breadcrumb-link">Data Periode Akademik</span></div>
        <h2>Data Periode Akademik</h2>
    </div>
    <a href="{{ route('PeriodeAkademik.create') }}" class="btn btn-primary">
        <i class="ri-add-line"></i> Tambah Periode Akademik
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
        <table id="tablePeriode" class="dt-table">
            <thead>
                <tr>
                    <th class="col-no">No</th>
                    <th>Nama Periode</th>
                    <th>Tahun Ajaran</th>
                    <th>Semester</th>
                    <th>Tanggal Mulai</th>
                    <th>Tanggal Selesai</th>
                    <th class="col-center">Status</th>
                    <th class="col-center">Aksi</th>
                </tr>
            </thead>
            <tbody>
                @forelse ($data as $val)
                    <tr>
                        <td class="col-no">{{ $loop->iteration }}</td>
                        <td><strong>{{ $val->nama_periode }}</strong></td>
                        <td>{{ $val->tahun_ajaran }}</td>
                        <td>{{ $val->semester }}</td>
                        <td>{{ date('d M Y', strtotime($val->tanggal_mulai)) }}</td>
                        <td>{{ date('d M Y', strtotime($val->tanggal_selesai)) }}</td>
                        <td class="col-center">
                            @if ($val->status == 1)
                                <span class="badge badge-success">Aktif</span>
                            @else
                                <span class="badge" style="background:#f1f5f9;color:#64748b;">Non-Aktif</span>
                            @endif
                        </td>
                        <td class="col-center" style="white-space:nowrap;">
                            <a href="{{ route('PeriodeAkademik.edit', $val->id) }}" class="btn btn-sm btn-primary">
                                <i class="ri-edit-2-line"></i> Edit
                            </a>
                            <button type="button" class="btn btn-sm btn-danger"
                                onclick="showDeleteModal({{ $val->id }}, '{{ $val->nama_periode }}', {{ $val->status }})">
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

{{-- Modal Hapus --}}
<div class="confirm-overlay" id="deleteModal">
    <div class="confirm-box">
        <div class="confirm-icon">!</div>
        <h3>Hapus Periode Akademik?</h3>
        <p>Data <strong id="deleteLabel"></strong> akan dihapus.<br>Data yang dihapus tidak dapat dikembalikan.</p>
        <div class="confirm-actions">
            <form id="delete-form" method="POST" style="display:inline;">
                @csrf @method('DELETE')
                <button type="submit" class="btn btn-danger">Ya, Hapus</button>
            </form>
            <button onclick="closeDeleteModal()" class="btn btn-secondary">Batal</button>
        </div>
    </div>
</div>

{{-- Modal Warning: tidak bisa hapus periode aktif --}}
<div class="confirm-overlay" id="warningAktifModal">
    <div class="confirm-box">
        <div class="confirm-icon">!</div>
        <h3>Tidak Dapat Dihapus</h3>
        <p>Periode aktif tidak dapat dihapus.<br>Ubah status ke Non-Aktif terlebih dahulu melalui menu Edit.</p>
        <div class="confirm-actions">
            <button onclick="document.getElementById('warningAktifModal').classList.remove('show')" class="btn btn-primary">
                OK, Mengerti
            </button>
        </div>
    </div>
</div>

@endsection

@push('scripts')
<script>
function showDeleteModal(id, nama, statusAktif) {
    if (statusAktif == 1) {
        document.getElementById('warningAktifModal').classList.add('show');
        return;
    }
    document.getElementById('deleteLabel').textContent = nama;
    document.getElementById('delete-form').action = "{{ route('PeriodeAkademik.destroy', '') }}/" + id;
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