@extends('layouts.app')

@section('content')

    <div class="page-header">
        <div>
            <div class="breadcrumb">Admin / Master Data / <a href="{{ route('Siswa.index') }}" class="breadcrumb-link">Data Siswa</a></div>
            <h2>Data Siswa</h2>
        </div>
        
        <div style="display:flex; gap:10px;">
            <button type="button" class="btn btn-import" onclick="openImportModal()">
                <i class="ri-upload-2-line"></i> Import Data
            </button>
            <a href="{{ route('Siswa.create') }}" class="btn btn-primary">
                <i class="ri-add-line"></i> Tambah Siswa
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
        <table id="tableSiswa" class="dt-table">
            <thead>
                <tr>
                    <th class="col-no" style="width:5%;">No</th>
                    <th>NIS</th>
                    <th>Nama Siswa</th>
                    <th>Jenis Kelamin</th>
                    <th>Tanggal Masuk</th>
                    <th>Kelas</th>
                    <th>Status</th>
                    <th class="col-center">Aksi</th>
                </tr>
            </thead>
            <tbody>
                @forelse ($data as $val)
                    <tr>
                        <td class="col-no">{{ $loop->iteration }}</td>
                        <td>{{ $val->nis }}</td>
                        <td>{{ $val->nama_siswa }}</td>
                        <td>{{ $val->jenis_kelamin == 'L' ? 'Laki-laki' : 'Perempuan' }}</td>
                        <td data-order="{{ $val->tanggal_masuk }}">
                            {{ date('d M Y', strtotime($val->tanggal_masuk)) }}
                        </td>
                        <td>{{ $val->kelas->nama_kelas ?? '-' }}</td>
                        <td>
                            @if ($val->status_siswa_id == 1)
                                <span class="badge badge-success">Aktif</span>
                            @elseif ($val->status_siswa_id == 2)
                                <span class="badge badge-warning">Alumni</span>
                            @else
                                <span class="badge badge-danger">Non-Aktif</span>
                            @endif
                        </td>
                        <td class="col-center" style="white-space:nowrap;">
                            <a href="{{ route('Siswa.edit', $val->id) }}" class="btn btn-sm btn-primary">
                                <i class="ri-edit-2-line"></i> Edit
                            </a>
                            <button type="button" class="btn btn-sm btn-danger" onclick="showDeleteModal({{ $val->id }})">
                                <i class="ri-delete-bin-line"></i> Hapus
                            </button>
                        </td>
                    </tr>
                @empty
                @endforelse
            </tbody>
        </table>
    </div>

    {{-- Delete Modal --}}
    <div class="confirm-overlay" id="deleteModal">
        <div class="confirm-box">
            <div class="confirm-icon">!</div>
            <h3>Hapus Siswa?</h3>
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
                <span class="modal-title">
                    <i class="ri-upload-2-line"></i> Import Data Siswa
                </span>
                <button class="modal-close" onclick="closeImportModal()">
                    <i class="ri-close-line"></i>
                </button>
            </div>
            <form action="{{ route('Siswa.import') }}" method="POST" enctype="multipart/form-data">
                @csrf
                <div class="modal-body">
                    <p style="font-size:13.5px; color:var(--text-muted); margin-bottom:16px;">
                        Download template terlebih dahulu:
                        <a href="{{ asset('template/siswa_template.xlsx') }}" download style="color:var(--primary); font-weight:600;">
                            <i class="ri-download-line"></i> Download Template
                        </a>
                    </p>
                    <div class="form-group">
                        <label class="form-label">Pilih File Excel <span class="required">*</span></label>
                        <label for="importFile" class="file-input-wrapper" style="cursor:pointer;">
                            <span class="file-input-label" style="background:#e2e8f0; color:#475569;">
                                <i class="ri-file-excel-2-line"></i> Pilih File
                            </span>
                            <span class="file-input-filename" id="importFileFilename" style="color:#475569;">
                                Belum ada file dipilih
                            </span>
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
        document.getElementById('delete-form').action = "{{ route('Siswa.destroy', '') }}/" + id;
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

    document.getElementById('importFile').addEventListener('change', function(e) {
        const fileName = e.target.files[0]?.name || 'Belum ada file dipilih';
        const label = document.getElementById('importFileFilename');
        label.textContent = fileName;
        if (e.target.files.length > 0) label.classList.add('has-file');
    });
</script>
@endpush