@extends('layouts.app')

@section('content')

    <div class="page-header">
        <div>
            <div class="breadcrumb">Admin / Master Data / <a href="{{ route('Staff.index') }}" class="breadcrumb-link">Data Staf</a></div>
            <h2>Data Staf</h2>
        </div>
        <a href="{{ route('Staff.create') }}" class="btn btn-primary">
            <i class="ri-add-line"></i> Tambah Staf
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
                        <th>Nama Staf</th>
                        <th>Akun User</th>
                        <th class="col-center">Aksi</th>
                    </tr>
                </thead>
                <tbody>
                    @forelse ($data as $val)
                        <tr>
                            <td class="col-no">{{ $loop->iteration }}</td>
                            <td>{{ $val->nama_staff }}</td>
                            <td>
                                @if ($val->user)
                                    {{ $val->user->username ?? $val->user->email }}
                                @else
                                    <span class="text-italic-muted">Tidak terhubung</span>
                                @endif
                            </td>
                            <td class="col-center" style="white-space:nowrap;">
                                <a href="{{ route('Staff.edit', $val->id) }}" class="btn btn-sm btn-primary">
                                    <i class="ri-edit-2-line"></i> Edit
                                </a>
                                <button type="button" class="btn btn-sm btn-danger" onclick="showDeleteModal({{ $val->id }})">
                                    <i class="ri-delete-bin-line"></i> Hapus
                                </button>
                            </td>
                        </tr>
                    @empty
                        <tr>
                            <td colspan="4" class="td-empty">
                                <i class="ri-inbox-line" style="font-size:32px;display:block;margin-bottom:8px;"></i>
                                Belum ada data staf
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
            <h3>Hapus Staf?</h3>
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
        document.getElementById('delete-form').action = "{{ route('Staff.destroy', '') }}/" + id;
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