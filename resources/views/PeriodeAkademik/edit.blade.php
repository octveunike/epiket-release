@extends('layouts.app')

@section('content')

<div class="page-header">
    <div>
        <div class="breadcrumb">Admin / <a href="{{ route('PeriodeAkademik.index') }}" style="color:var(--primary);">Data Periode Akademik</a> / Edit</div>
        <h2>Edit Periode Akademik</h2>
    </div>
</div>

@if (session('success'))
    <div class="alert alert-success" id="success-alert">
        <i class="ri-checkbox-circle-line"></i> {{ session('success') }}
    </div>
    <script>setTimeout(()=>document.getElementById('success-alert')?.remove(), 3000);</script>
@endif

<div class="card">
    <form method="POST" action="{{ route('PeriodeAkademik.update', $PeriodeAkademik->id) }}">
        @csrf
        @method('PUT')

        <div class="form-grid">

            <div class="form-group">
                <label class="form-label">Nama Periode <span class="required">*</span></label>
                <input type="text" name="nama_periode" class="form-control" placeholder="Masukkan Nama Periode" required
                    value="{{ old('nama_periode', $PeriodeAkademik->nama_periode) }}">
                @error('nama_periode')<small style="color:#ef4444;">{{ $message }}</small>@enderror
            </div>

            <div class="form-group">
                <label class="form-label">Tahun Ajaran <span class="required">*</span></label>
                <input type="text" name="tahun_ajaran" class="form-control" placeholder="Masukkan Tahun Ajaran" required
                    value="{{ old('tahun_ajaran', $PeriodeAkademik->tahun_ajaran) }}">
                @error('tahun_ajaran')<small style="color:#ef4444;">{{ $message }}</small>@enderror
            </div>

            <div class="form-group">
                <label class="form-label">Semester <span class="required">*</span></label>
                <input type="text" name="semester" class="form-control" placeholder="Masukkan Semester" required
                    value="{{ old('semester', $PeriodeAkademik->semester) }}">
                @error('semester')<small style="color:#ef4444;">{{ $message }}</small>@enderror
            </div>

            <div class="form-group">
                <label class="form-label">Tanggal Mulai <span class="required">*</span></label>
                <input type="date" name="tanggal_mulai" class="form-control" required
                    value="{{ old('tanggal_mulai', $PeriodeAkademik->tanggal_mulai) }}">
                @error('tanggal_mulai')<small style="color:#ef4444;">{{ $message }}</small>@enderror
            </div>

            <div class="form-group">
                <label class="form-label">Tanggal Selesai <span class="required">*</span></label>
                <input type="date" name="tanggal_selesai" class="form-control" required
                    value="{{ old('tanggal_selesai', $PeriodeAkademik->tanggal_selesai) }}">
                @error('tanggal_selesai')<small style="color:#ef4444;">{{ $message }}</small>@enderror
            </div>

            <div class="form-group">
                <label class="form-label">Status <span class="required">*</span></label>
                <select name="status" class="form-control">
                    <option value="0" {{ old('status', $PeriodeAkademik->status) == '0' ? 'selected' : '' }}>Non-Aktif</option>
                    <option value="1" {{ old('status', $PeriodeAkademik->status) == '1' ? 'selected' : '' }}>Aktif</option>
                </select>
                @error('status')<small style="color:#ef4444;">{{ $message }}</small>@enderror
            </div>

        </div>

        <div class="form-actions">
            <button type="submit" class="btn btn-primary">
                <i class="ri-save-line"></i> Simpan Perubahan
            </button>
            <a href="{{ route('PeriodeAkademik.index') }}" class="btn btn-secondary">
                <i class="ri-arrow-left-line"></i> Batal
            </a>
        </div>

    </form>
</div>

@if (session('error_aktif'))
<div class="confirm-overlay show" id="modalAktifError">
    <div class="confirm-box">
        <div class="confirm-icon">!</div>
        <h3>Tidak Dapat Mengaktifkan</h3>
        <p>Sudah ada periode aktif: <strong>{{ session('error_aktif') }}</strong>.<br>
        Hanya boleh 1 periode aktif. Non-aktifkan periode tersebut terlebih dahulu melalui menu Edit.</p>
        <div class="confirm-actions">
            <button onclick="document.getElementById('modalAktifError').classList.remove('show')" class="btn btn-primary">
                OK, Mengerti
            </button>
        </div>
    </div>
</div>
@endif

@endsection