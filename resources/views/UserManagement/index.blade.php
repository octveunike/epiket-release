@extends('layouts.app')

@section('content')

    <div class="page-header">
        <div>
            <div class="breadcrumb">Admin / <a href="{{ route('UserManagement.index') }}" class="breadcrumb-link">User Management</a></div>
            <h2>User Management</h2>
        </div>
        <a href="{{ route('UserManagement.create') }}" class="btn btn-primary">
            <i class="ri-user-add-line"></i> Tambah User
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
        <table id="tableUser" class="dt-table"
            data-destroy-url="{{ route('UserManagement.destroy', '') }}">
            <thead>
                <tr>
                    <th style="text-align:center; width:5%;">No</th>
                    <th>Nama</th>
                    <th>Username</th>
                    <th>Role</th>
                    <th style="text-align:center;">Aksi</th>
                </tr>
            </thead>
            <tbody>
                @forelse ($data as $val)
                    <tr>
                        <td style="text-align:center;">{{ $loop->iteration }}</td>
                        <td>{{ $val->nama }}</td>
                        <td>{{ $val->username }}</td>
                        <td>
                            @forelse ($val->roles as $role)
                                <span class="role-badge">{{ $role->nama_role }}</span>
                            @empty
                                <span class="text-italic-muted">Tidak ada role</span>
                            @endforelse
                        </td>
                        <td style="text-align:center; white-space:nowrap;">
                            <a href="{{ route('UserManagement.edit', $val->id) }}" class="btn btn-sm btn-primary">
                                <i class="ri-edit-2-line"></i> Edit
                            </a>
                            <button type="button" class="btn btn-sm btn-danger"
                                onclick="showDeleteModal({{ $val->id }}, '{{ addslashes($val->nama) }}')">
                                <i class="ri-delete-bin-line"></i> Hapus
                            </button>
                        </td>
                    </tr>
                @empty
                    <tr>
                        <td colspan="5" class="td-empty">
                            <i class="ri-user-line" style="font-size:32px;display:block;margin-bottom:8px;"></i>
                            Belum ada data user
                        </td>
                    </tr>
                @endforelse
            </tbody>
        </table>
    </div>

    <div class="confirm-overlay" id="deleteModal">
        <div class="confirm-box">
            <div class="confirm-icon">!</div>
            <h3>Hapus User?</h3>
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
    function showDeleteModal(id, nama) {
        document.getElementById('delete-form').action = "{{ route('UserManagement.destroy', '') }}/" + id;
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