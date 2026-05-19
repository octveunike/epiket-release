@extends('layouts.app')

@section('content')

    <div class="page-header">
        <div>
            <div class="breadcrumb">
                Admin /
                @if (request('return_to') === 'kelas_edit' && request('kelas_id'))
                    <a href="{{ route('Kelas.edit', request('kelas_id')) }}" style="color:var(--primary);">Edit Kelas</a> /
                @elseif (request('return_to') === 'kelas_create')
                    <a href="{{ route('Kelas.create') }}" style="color:var(--primary);">Tambah Kelas</a> /
                @endif
                <a href="{{ route('Siswa.index') }}" style="color:var(--primary);">Data Siswa</a> / Edit
            </div>
            <h2>Edit Siswa</h2>
        </div>
    </div>

    @if (session('success'))
        <div class="alert alert-success" id="success-alert">
            <i class="ri-checkbox-circle-line"></i> {{ session('success') }}
        </div>
        <script>
            setTimeout(function() { document.getElementById('success-alert').remove(); }, 3000);
        </script>
    @endif

    <div class="card">
        @php
            $returnQuery = '';
            if (request('return_to') === 'kelas_edit' && request('kelas_id')) {
                $returnQuery = '?return_to=kelas_edit&kelas_id=' . request('kelas_id');
            } elseif (request('return_to') === 'kelas_create') {
                $returnQuery = '?return_to=kelas_create';
            }
        @endphp
        <form method="POST" action="{{ route('Siswa.update', $Siswa->id) }}{{ $returnQuery }}">
            @csrf
            @method('PUT')

            <div class="form-grid">

                <div class="form-group">
                    <label class="form-label">NIS <span class="required">*</span></label>
                    <input type="text" name="nis" class="form-control" placeholder="Masukkan NIS" required
                        value="{{ old('nis', $Siswa->nis) }}">
                    @error('nis')<small style="color:#ef4444;">{{ $message }}</small>@enderror
                </div>

                <div class="form-group">
                    <label class="form-label">Nama Siswa <span class="required">*</span></label>
                    <input type="text" name="nama_siswa" class="form-control" placeholder="Masukkan Nama Siswa" required
                        value="{{ old('nama_siswa', $Siswa->nama_siswa) }}">
                    @error('nama_siswa')<small style="color:#ef4444;">{{ $message }}</small>@enderror
                </div>

                <div class="form-group">
                    <label class="form-label">Jenis Kelamin <span class="required">*</span></label>
                    <select name="jenis_kelamin" class="form-control" required>
                        <option value="" disabled>Pilih Jenis Kelamin</option>
                        <option value="L" {{ old('jenis_kelamin', $Siswa->jenis_kelamin) == 'L' ? 'selected' : '' }}>Laki-laki</option>
                        <option value="P" {{ old('jenis_kelamin', $Siswa->jenis_kelamin) == 'P' ? 'selected' : '' }}>Perempuan</option>
                    </select>
                    @error('jenis_kelamin')<small style="color:#ef4444;">{{ $message }}</small>@enderror
                </div>

                <div class="form-group">
                    <label class="form-label">Tanggal Masuk <span class="required">*</span></label>
                    <input type="date" name="tanggal_masuk" class="form-control" required
                        value="{{ old('tanggal_masuk', $Siswa->tanggal_masuk) }}">
                    @error('tanggal_masuk')<small style="color:#ef4444;">{{ $message }}</small>@enderror
                </div>

                <div class="form-group">
                    <label class="form-label">Kelas <span class="required">*</span></label>
                    <select name="kelas_id" class="form-control" required>
                        <option value="" disabled>Pilih Kelas</option>
                        @foreach ($kelas as $k)
                            <option value="{{ $k->id }}" {{ old('kelas_id', $Siswa->kelas_id) == $k->id ? 'selected' : '' }}>
                                {{ $k->nama_kelas }}
                            </option>
                        @endforeach
                    </select>
                    @error('kelas_id')<small style="color:#ef4444;">{{ $message }}</small>@enderror
                </div>

                <div class="form-group">
                    <label class="form-label">Status Siswa <span class="required">*</span></label>
                    <select name="status_siswa_id" class="form-control" required>
                        <option value="" disabled>Pilih Status</option>
                        <option value="1" {{ old('status_siswa_id', $Siswa->status_siswa_id) == '1' ? 'selected' : '' }}>Aktif</option>
                        <option value="2" {{ old('status_siswa_id', $Siswa->status_siswa_id) == '2' ? 'selected' : '' }}>Alumni</option>
                        <option value="3" {{ old('status_siswa_id', $Siswa->status_siswa_id) == '3' ? 'selected' : '' }}>Non-Aktif</option>
                    </select>
                    @error('status_siswa_id')<small style="color:#ef4444;">{{ $message }}</small>@enderror
                </div>

                <div class="form-group">
                    <label class="form-label">User (Akun Login)</label>
                    <div style="display:flex; gap:8px; align-items:stretch;">
                        <select name="user_id" id="siswa-user-select" class="form-control" style="flex:1;">
                            <option value="">-- Tidak Terhubung ke User --</option>
                            @foreach ($users as $u)
                                <option value="{{ $u->id }}" {{ old('user_id', $Siswa->user_id) == $u->id ? 'selected' : '' }}>
                                    {{ $u->nama }} ({{ $u->username }})
                                </option>
                            @endforeach
                        </select>
                        <button type="button" class="btn btn-secondary" onclick="openBuatAkunModal()" style="white-space:nowrap;">
                            <i class="ri-user-add-line"></i> Buat Akun User
                        </button>
                    </div>
                    <small style="color:var(--text-muted);">Diperlukan kalau siswa ini ditunjuk jadi Ketua Kelas.</small>
                    @error('user_id')<small style="color:#ef4444;">{{ $message }}</small>@enderror
                </div>

            </div>

            @include('Siswa._buat-akun-modal')

            <div class="form-actions">
                <button type="submit" class="btn btn-primary">
                    <i class="ri-save-line"></i> Simpan Perubahan
                </button>
                @if (request('return_to') === 'kelas_edit' && request('kelas_id'))
                    <a href="{{ route('Kelas.edit', request('kelas_id')) }}" class="btn btn-secondary">
                        <i class="ri-arrow-left-line"></i> Batal
                    </a>
                @elseif (request('return_to') === 'kelas_create')
                    <a href="{{ route('Kelas.create') }}" class="btn btn-secondary">
                        <i class="ri-arrow-left-line"></i> Batal
                    </a>
                @else
                    <a href="{{ route('Siswa.index') }}" class="btn btn-secondary">
                        <i class="ri-arrow-left-line"></i> Batal
                    </a>
                @endif
            </div>

        </form>
    </div>

@endsection