@extends('layouts.app')

@section('content')

    <div class="page-header">
        <div>
            <div class="breadcrumb">Admin / Master Data / <a href="{{ route('Siswa.index') }}" class="breadcrumb-link">Data Siswa</a></div>
            <h2>Data Siswa</h2>
        </div>
        <a href="{{ route('Siswa.create') }}" class="btn btn-primary">
            <i class="ri-add-line"></i> Tambah Siswa
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
        <table id="tableSiswa" class="dt-table"
            data-destroy-url="{{ route('Siswa.destroy', '') }}">
                <thead>
                    <tr>
                        <th>No</th>
                        <th>NIS</th>
                        <th>Nama Siswa</th>
                        <th>Jenis Kelamin</th>
                        <th>Tanggal Masuk</th>
                        <th>Kelas</th>
                        <th>Status</th>
                        <th class="dt-nosort">Aksi</th>
                    </tr>
                </thead>
                <tbody>
                    @forelse ($data as $val)
                        <tr>
                            <td>{{ $loop->iteration }}</td>
                            <td>{{ $val->nis }}</td>
                            <td>{{ $val->nama_siswa }}</td>
                            <td>{{ $val->jenis_kelamin == 'L' ? 'Laki-laki' : 'Perempuan' }}</td>
                            <td data-order="{{ $val->tanggal_masuk }}">{{ date('d M Y', strtotime($val->tanggal_masuk)) }}</td>
                            <td>{{ $val->kelas_id }}</td>
                            <td>
                                @if ($val->status_siswa_id == 1)
                                    <span class="badge badge-success">Aktif</span>
                                @elseif ($val->status_siswa_id == 2)
                                    <span class="badge badge-warning">Alumni</span>
                                @else
                                    <span class="badge badge-danger">Non-Aktif</span>
                                @endif
                            </td>
                            <td style="white-space:nowrap;">
                                <a href="{{ route('Siswa.edit', $val->id) }}" class="btn btn-sm btn-primary">
                                    <i class="ri-edit-2-line"></i> Edit
                                </a>
                                <button type="button" class="btn btn-sm btn-danger" onclick="showDeleteModal({{ $val->id }})">
                                    <i class="ri-delete-bin-line"></i> Hapus
                                </button>
                            </td>
                        </tr>
                    @empty
                        <tr>
                            <td colspan="8" class="td-empty">
                                <i class="ri-inbox-line" style="font-size:32px;display:block;margin-bottom:8px;"></i>
                                Belum ada data siswa
                            </td>
                        </tr>
                    @endforelse
                </tbody>
            </table>
    </div>

    {{-- Delete Modal --}}
    <div class="modal-overlay" id="deleteModal">
        <div class="modal-box">
            <div class="modal-header">
                <span class="modal-title">Konfirmasi Hapus</span>
                <button class="modal-close" onclick="closeDeleteModal()"><i class="ri-close-line"></i></button>
            </div>
            <div class="modal-body">
                <div class="modal-icon danger"><i class="ri-error-warning-line"></i></div>
                <p class="modal-confirm-text">
                    Hapus data siswa ini?<br>
                    <small class="text-danger-sm">Data yang dihapus tidak dapat dikembalikan.</small>
                </p>
            </div>
            <div class="modal-footer">
                <button type="button" class="btn btn-secondary" onclick="closeDeleteModal()">Batal</button>
                <form id="delete-form" method="POST"
                    data-base-url="{{ route('Siswa.destroy', '') }}"
                    style="display:inline;">
                    @csrf @method('DELETE')
                    <button type="submit" class="btn btn-danger"><i class="ri-delete-bin-line"></i> Hapus</button>
                </form>
            </div>
        </div>
    </div>

@endsection