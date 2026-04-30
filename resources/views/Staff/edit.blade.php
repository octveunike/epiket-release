@extends('layouts.app')

@section('content')

    <div class="page-header">
        <div>
            <div class="breadcrumb">Admin / <a href="{{ route('Staff.index') }}" style="color:var(--primary);">Data Staf</a> / Edit</div>
            <h2>Edit Staf</h2>
        </div>
    </div>

    @if (session('success'))
        <div class="alert alert-success" id="success-alert">
            <i class="ri-checkbox-circle-line"></i> {{ session('success') }}
        </div>
        <script>setTimeout(function() { document.getElementById('success-alert').remove(); }, 3000);</script>
    @endif

    <div class="card">
        <form method="POST" action="{{ route('Staff.update', $Staff->id) }}">
            @csrf
            @method('PUT')

            <div class="form-grid">

                <div class="form-group">
                    <label class="form-label">Nama Staf <span class="required">*</span></label>
                    <input type="text" name="nama_staff" class="form-control" placeholder="Masukkan Nama Staf" required
                        value="{{ old('nama_staff', $Staff->nama_staff) }}">
                    @error('nama_staff')<small style="color:#ef4444;">{{ $message }}</small>@enderror
                </div>

                <div class="form-group">
                    <label class="form-label">User (Akun Login)</label>
                    <div style="display:flex; gap:8px; align-items:stretch;">
                        <select name="user_id" id="staff-user-select" class="form-control" style="flex:1;">
                            <option value="">-- Tidak Terhubung ke User --</option>
                            @foreach ($users as $u)
                                <option value="{{ $u->id }}" {{ old('user_id', $Staff->user_id) == $u->id ? 'selected' : '' }}>
                                    {{ $u->nama }} ({{ $u->username }})
                                </option>
                            @endforeach
                        </select>
                        <button type="button" class="btn btn-secondary" onclick="openBuatAkunModal()" style="white-space:nowrap;">
                            <i class="ri-user-add-line"></i> Buat Akun User
                        </button>
                    </div>
                    @error('user_id')<small style="color:#ef4444;">{{ $message }}</small>@enderror
                </div>

            </div>

            @include('Staff._buat-akun-modal')

            <div class="form-actions">
                <button type="submit" class="btn btn-primary">
                    <i class="ri-save-line"></i> Simpan Perubahan
                </button>
                <a href="{{ route('Staff.index') }}" class="btn btn-secondary">
                    <i class="ri-arrow-left-line"></i> Batal
                </a>
            </div>

        </form>
    </div>

@endsection