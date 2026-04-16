@extends('layouts.app')

@section('content')

    <div class="page-header">
        <div>
            <div class="breadcrumb">Admin / Master Data / <a href="{{ route('PeriodeAkademik.index') }}" class="breadcrumb-link">Data Periode Akademik</a></div>
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
        <script>
            setTimeout(function() { document.getElementById('success-alert').remove(); }, 3000);
        </script>
    @endif

    @if (session('error'))
        <div class="alert alert-danger" id="error-alert">
            <i class="ri-error-warning-line"></i> {{ session('error') }}
        </div>
    @endif

    <div class="card">
        <table id="tablePeriode" class="dt-table"
            data-destroy-url="{{ route('PeriodeAkademik.destroy', '') }}">
            <thead>
                <tr>
                    <th style="text-align:center; width:5%;">No</th>
                    <th>Nama Periode</th>
                    <th>Tahun Ajaran</th>
                    <th>Semester</th>
                    <th>Tanggal Mulai</th>
                    <th>Tanggal Selesai</th>
                    <th style="text-align:center;">Aksi</th>
                </tr>
            </thead>
            <tbody>
                @forelse ($data as $val)
                    <tr>
                        <td style="text-align:center;">{{ $loop->iteration }}</td>
                        <td>{{ $val->nama_periode }}</td>
                        <td>{{ $val->tahun_ajaran }}</td>
                        <td>{{ $val->semester }}</td>
                        <td data-order="{{ $val->tanggal_mulai }}">{{ date('d M Y', strtotime($val->tanggal_mulai)) }}</td>
                        <td data-order="{{ $val->tanggal_selesai }}">{{ date('d M Y', strtotime($val->tanggal_selesai)) }}</td>
                        <td style="text-align:center; white-space:nowrap;">
                            <a href="{{ route('PeriodeAkademik.edit', $val->id) }}" class="btn btn-sm btn-primary">
                                <i class="ri-edit-2-line"></i> Edit
                            </a>
                            <button type="button" class="btn btn-sm btn-danger" onclick="showDeleteModal({{ $val->id }})">
                                <i class="ri-delete-bin-line"></i> Hapus
                            </button>
                        </td>
                    </tr>
                @empty
                    <tr>
                        <td colspan="7" class="td-empty">
                            <i class="ri-inbox-line" style="font-size:32px;display:block;margin-bottom:8px;"></i>
                            Belum ada data periode akademik
                        </td>
                    </tr>
                @endforelse
            </tbody>
        </table>
    </div>

    <div class="confirm-overlay" id="deleteModal">
        <div class="confirm-box">
            <div class="confirm-icon">!</div>
            <h3>Are you sure?</h3>
            <p>You won't be able to revert this!</p>
            <div class="confirm-actions">
                <form id="delete-form" method="POST">
                    @csrf
                    @method('DELETE')
                    <button type="submit" class="btn btn-danger">
                        Yes, delete it!
                    </button>
                </form>
                <button onclick="closeDeleteModal()" class="btn btn-secondary">
                    Cancel
                </button>
            </div>
        </div>
    </div>

@endsection

@push('scripts')
<script>
    function showDeleteModal(id) {
        document.getElementById('delete-form').action = "{{ route('PeriodeAkademik.destroy', '') }}/" + id;
        document.getElementById('deleteModal').classList.add('show');
    }
    function closeDeleteModal() {
        document.getElementById('deleteModal').classList.remove('show');
    }
    document.getElementById('deleteModal').addEventListener('click', function(e) {
        if (e.target === this) {
            closeDeleteModal();
        }
    });
</script>
@endpush