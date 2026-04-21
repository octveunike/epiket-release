@extends('layouts.app')

@section('content')

    <div class="page-header">
        <div>
            <div class="breadcrumb">Admin / <a href="{{ route('Organisasi.index') }}" class="breadcrumb-link">Data Organisasi</a></div>
            <h2>Data Organisasi</h2>
        </div>
        <div style="display:flex; gap:10px;">
            <button type="button" class="btn btn-import" onclick="openImportModal()">
                <i class="ri-upload-2-line"></i> Import Data
            </button>
            <a href="{{ route('Organisasi.create') }}" class="btn btn-primary">
                <i class="ri-add-line"></i> Tambah Organisasi
            </a>
        </div>
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
        <table id="tableOrganisasi" class="dt-table"
            data-destroy-url="{{ route('Organisasi.destroy', '') }}">
            <thead>
                <tr>
                    <th style="text-align:center; width:5%;">No</th>
                    <th style="width:15%;">Nama Organisasi</th>
                    <th style="width:15%;">Pembina</th>
                    <th style="text-align:center; width:13%;">Anggota</th>
                    <th style="width:20%;">Keterangan</th>
                    <th style="text-align:center; width:30%;">Aksi</th>
                </tr>
            </thead>
            <tbody>
                @foreach ($data as $val)
                    <tr>
                        <td style="text-align:center;">{{ $loop->iteration }}</td>
                        <td style="font-weight:600; white-space:nowrap;">{{ $val->nama_organisasi }}</td>
                        <td style="white-space:nowrap;">
                            @if ($val->pembina)
                                <span style="display:flex; align-items:center; gap:6px;">
                                    <i class="ri-user-star-line" style="color:var(--primary); flex-shrink:0;"></i>
                                    {{ $val->pembina->nama_guru }}
                                </span>
                            @else
                                <span class="text-muted-sm">—</span>
                            @endif
                        </td>
                        <td style="text-align:center;">
                            <span style="font-size:13px; color:var(--text-main);">
                                <i class="ri-group-line" style="color:var(--primary); font-size:14px; vertical-align:middle;"></i>
                                {{ $val->siswaOrganisasi->count() }} siswa
                            </span>
                        </td>
                        <td style="color:var(--text-muted); font-size:13px;">{{ $val->keterangan ?? '—' }}</td>
                        <td style="text-align:center; white-space:nowrap;">
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
                @endforeach
            </tbody>
        </table>
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

    {{-- Import Modal --}}
    <div class="modal-overlay" id="importModal">
        <div class="modal-box">
            <div class="modal-header">
                <span class="modal-title"><i class="ri-upload-2-line"></i> Import Data Organisasi</span>
                <button class="modal-close" onclick="closeImportModal()"><i class="ri-close-line"></i></button>
            </div>
            <form action="{{ route('Organisasi.import') }}" method="POST" enctype="multipart/form-data">
                @csrf
                <div class="modal-body">
                    <p style="font-size:13.5px; color:var(--text-muted); margin-bottom:16px;">
                        Download template terlebih dahulu:
                        <a href="{{ asset('template/organisasi_template.xlsx') }}" download style="color:var(--primary); font-weight:600;">
                            <i class="ri-download-line"></i> Download Template
                        </a>
                    </p>
                    <div class="form-group">
                        <label class="form-label">Pilih File Excel <span class="required">*</span></label>
                        <label for="importFile" class="file-input-wrapper" style="cursor:pointer;">
                            <span class="file-input-label" style="background:#e2e8f0; color:#475569;">
                                <i class="ri-file-excel-2-line"></i> Pilih File
                            </span>
                            <span class="file-input-filename" id="importFileFilename" style="color:#475569;">Belum ada file dipilih</span>
                            <input type="file" id="importFile" name="file" class="file-input-hidden"
                                accept=".xlsx,.xls,.csv" required>
                        </label>
                    </div>
                </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-secondary" onclick="closeImportModal()">Batal</button>
                    <button type="submit" class="btn btn-success">
                        <i class="ri-upload-2-line"></i> Upload
                    </button>
                </div>
            </form>
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

    function openImportModal() {
        document.getElementById('importModal').classList.add('active');
    }
    function closeImportModal() {
        document.getElementById('importModal').classList.remove('active');
        document.getElementById('importFile').value = '';
        const filename = document.getElementById('importFileFilename');
        filename.textContent = 'Belum ada file dipilih';
        filename.classList.remove('has-file');
    }
    document.getElementById('importModal').addEventListener('click', function(e) {
        if (e.target === this) closeImportModal();
    });
</script>
@endpush