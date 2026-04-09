@extends('layouts.app')

@section('content')

    <div class="page-header">
        <div>
            <div class="breadcrumb">Admin / Master Data / <a href="{{ route('Organisasi.index') }}" class="breadcrumb-link">Data Organisasi</a></div>
            <h2>Data Organisasi</h2>
        </div>
        <a href="{{ route('Organisasi.create') }}" class="btn btn-primary">
            <i class="ri-add-line"></i> Tambah Organisasi
        </a>
    </div>

    @if (session('success'))
        <div class="alert alert-success" id="success-alert">
            <i class="ri-checkbox-circle-line"></i> {{ session('success') }}
        </div>
        <script>setTimeout(function() { document.getElementById('success-alert').remove(); }, 3000);</script>
    @endif

    @if (session('error'))
        <div class="alert alert-danger">
            <i class="ri-error-warning-line"></i> {{ session('error') }}
        </div>
    @endif

    <div class="card">
        <div class="table-responsive">
            <table>
                <thead>
                    <tr>
                        <th class="col-no">No</th>
                        <th style="width:15%;">Nama Organisasi</th>
                        <th style="width:15%;">Pembina</th>
                        <th class="col-center" style="width:13%;">Anggota</th>
                        <th style="width:20%;">Keterangan</th>
                        <th class="col-center" style="width:30%;">Aksi</th>
                    </tr>
                </thead>
                <tbody>
                    @forelse ($data as $val)
                        <tr>
                            <td class="col-no">{{ $loop->iteration }}</td>
                            <td style="font-weight:600;white-space:nowrap;">{{ $val->nama_organisasi }}</td>
                            <td style="white-space:nowrap;">
                                @if ($val->pembina)
                                    <span style="display:flex;align-items:center;gap:6px;">
                                        <i class="ri-user-star-line" style="color:var(--primary);flex-shrink:0;"></i>
                                        {{ $val->pembina->nama_guru }}
                                    </span>
                                @else
                                    <span class="text-muted-sm">—</span>
                                @endif
                            </td>
                            <td class="col-center">
                                <span style="font-size:13px;color:var(--text-main);">
                                    <i class="ri-group-line" style="color:var(--primary);font-size:14px;vertical-align:middle;"></i>
                                    {{ $val->siswaOrganisasi->count() }} siswa
                                </span>
                            </td>
                            <td style="color:var(--text-muted);font-size:13px;">{{ $val->keterangan ?? '—' }}</td>
                            <td class="col-center" style="white-space:nowrap;">
                                <a href="{{ route('Organisasi.show', $val->id) }}" class="btn btn-sm btn-secondary">
                                    <i class="ri-group-line"></i> Anggota
                                </a>
                                <a href="{{ route('Organisasi.edit', $val->id) }}" class="btn btn-sm btn-primary">
                                    <i class="ri-edit-2-line"></i> Edit
                                </a>
                                <button type="button" class="btn btn-sm btn-danger" onclick="showDeleteModal({{ $val->id }})">
                                    <i class="ri-delete-bin-line"></i> Hapus
                                </button>
                            </td>
                        </tr>
                    @empty
                        <tr>
                            <td colspan="6" class="td-empty">
                                <i class="ri-team-line" style="font-size:32px;display:block;margin-bottom:8px;"></i>
                                Belum ada data organisasi
                            </td>
                        </tr>
                    @endforelse
                </tbody>
            </table>
        </div>
    </div>

    <div class="confirm-overlay" id="deleteModal">
        <div class="confirm-box">
            <div class="confirm-icon">!</div>
            <h3>Hapus Organisasi?</h3>
            <p>Data yang dihapus tidak dapat dikembalikan.</p>
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
    function showDeleteModal(id) {
        document.getElementById('delete-form').action = "{{ route('Organisasi.destroy', '') }}/" + id;
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