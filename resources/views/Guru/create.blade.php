@extends('layouts.app')

@section('content')

    <div class="page-header">
        <div>
            <div class="breadcrumb">Admin / <a href="{{ route('Guru.index') }}" style="color:var(--primary);">Data Guru</a> / Tambah</div>
            <h2>Tambah Guru</h2>
        </div>
    </div>

    <div class="card">
        <form action="{{ route('Guru.store') }}" method="POST">
            @csrf

            <div class="form-grid">

                <div class="form-group">
                    <label class="form-label">NIP <span class="required">*</span></label>
                    <input type="text" name="nip" class="form-control" placeholder="Masukkan NIP" required
                        value="{{ old('nip') }}">
                    @error('nip')<small style="color:#ef4444;">{{ $message }}</small>@enderror
                </div>

                <div class="form-group">
                    <label class="form-label">Nama Guru <span class="required">*</span></label>
                    <input type="text" name="nama_guru" class="form-control" placeholder="Masukkan Nama Guru" required
                        value="{{ old('nama_guru') }}">
                    @error('nama_guru')<small style="color:#ef4444;">{{ $message }}</small>@enderror
                </div>

                <div class="form-group">
                    <label class="form-label">Mata Pelajaran</label>
                    <input type="text" name="mata_pelajaran" class="form-control" placeholder="Contoh: Matematika, Bahasa Inggris"
                        value="{{ old('mata_pelajaran') }}">
                    @error('mata_pelajaran')<small style="color:#ef4444;">{{ $message }}</small>@enderror
                </div>

                <div class="form-group">
                    <label class="form-label">User (Akun Login)</label>
                    <div style="display:flex; gap:8px; align-items:stretch;">
                        <select name="user_id" id="guru-user-select" class="form-control" style="flex:1;">
                            <option value="">-- Tidak Terhubung ke User --</option>
                            @foreach ($users as $u)
                                <option value="{{ $u->id }}" {{ old('user_id') == $u->id ? 'selected' : '' }}>
                                    {{ $u->nama }} ({{ $u->username }})
                                </option>
                            @endforeach
                        </select>
                        <button type="button" class="btn btn-secondary" onclick="openBuatAkunModal()" style="white-space:nowrap;">
                            <i class="ri-user-add-line"></i> Buat Akun User
                        </button>
                    </div>
                    <small style="color:var(--text-muted);">Diperlukan kalau guru ini ditunjuk jadi Wali Kelas.</small>
                    @error('user_id')<small style="color:#ef4444;">{{ $message }}</small>@enderror
                </div>

            </div>

            @include('Guru._buat-akun-modal')

            <div class="form-actions">
                <button type="submit" class="btn btn-primary">
                    <i class="ri-save-line"></i> Simpan
                </button>
                <a href="{{ route('Guru.index') }}" class="btn btn-secondary">
                    <i class="ri-arrow-left-line"></i> Batal
                </a>
            </div>

        </form>
    </div>

@endsection