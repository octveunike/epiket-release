@extends('layouts.app')

@section('content')

    <div class="page-header">
        <div>
            <div class="breadcrumb">Admin / <a href="{{ route('Staff.index') }}" style="color:var(--primary);">Data Staf</a> / Tambah</div>
            <h2>Tambah Staf</h2>
        </div>
    </div>

    <div class="card">
        <form action="{{ route('Staff.store') }}" method="POST">
            @csrf

            <div class="form-grid">

                <div class="form-group">
                    <label class="form-label">Nama Staf <span class="required">*</span></label>
                    <input type="text" name="nama_staff" class="form-control" placeholder="Masukkan Nama Staf" required
                        value="{{ old('nama_staff') }}">
                    @error('nama_staff')<small style="color:#ef4444;">{{ $message }}</small>@enderror
                </div>

                <div class="form-group">
                    <label class="form-label">User (Akun Login)</label>
                    <select name="user_id" class="form-control">
                        <option value="">-- Tidak Terhubung ke User --</option>
                        @foreach ($users as $u)
                            <option value="{{ $u->id }}" {{ old('user_id') == $u->id ? 'selected' : '' }}>
                                {{ $u->name }} ({{ $u->email }})
                            </option>
                        @endforeach
                    </select>
                    @error('user_id')<small style="color:#ef4444;">{{ $message }}</small>@enderror
                </div>

            </div>

            <div class="form-actions">
                <button type="submit" class="btn btn-primary">
                    <i class="ri-save-line"></i> Simpan
                </button>
                <a href="{{ route('Staff.index') }}" class="btn btn-secondary">
                    <i class="ri-arrow-left-line"></i> Batal
                </a>
            </div>

        </form>
    </div>

@endsection