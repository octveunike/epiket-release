@extends('layouts.app')

@section('content')

    <div class="page-header">
        <div>
            <div class="breadcrumb">Admin / <a href="{{ route('PeriodeAkademik.index') }}" style="color:var(--primary);">Data PeriodeAkademik</a> / Tambah</div>
            <h2>Tambah Periode Akademik</h2>
        </div>
    </div>

    <div class="card">
        <form action="{{ route('PeriodeAkademik.store') }}" method="POST">
            @csrf

            <div class="form-grid">

                <div class="form-group">
                    <label class="form-label">Nama Periode <span class="required">*</span></label>
                    <input type="text" name="nama_periode" class="form-control" placeholder="Masukkan Nama Periode" required
                        value="{{ old('nama_periode') }}">
                    @error('nama_periode')<small style="color:#ef4444;">{{ $message }}</small>@enderror
                </div>

                <div class="form-group">
                    <label class="form-label">Tahun Ajaran <span class="required">*</span></label>
                    <input type="text" name="tahun_ajaran" class="form-control" placeholder="Masukkan Tahun Ajaran" required
                        value="{{ old('tahun_ajaran') }}">
                    @error('tahun_ajaran')<small style="color:#ef4444;">{{ $message }}</small>@enderror
                </div>

                <div class="form-group">
                    <label class="form-label">Semester <span class="required">*</span></label>
                    <input type="text" name="semester" class="form-control" placeholder="Masukkan Semester" required
                        value="{{ old('semester') }}">
                    @error('semester')<small style="color:#ef4444;">{{ $message }}</small>@enderror
                </div>

                <div class="form-group">
                    <label class="form-label">Tanggal Mulai <span class="required">*</span></label>
                    <input type="date" name="tanggal_mulai" class="form-control" required
                        value="{{ old('tanggal_mulai') }}">
                    @error('tanggal_mulai')<small style="color:#ef4444;">{{ $message }}</small>@enderror
                </div>

                <div class="form-group">
                    <label class="form-label">Tanggal Selesai <span class="required">*</span></label>
                    <input type="date" name="tanggal_selesai" class="form-control" required
                        value="{{ old('tanggal_selesai') }}">
                    @error('tanggal_selesai')<small style="color:#ef4444;">{{ $message }}</small>@enderror
                </div>

            </div>

            <div class="form-actions">
                <button type="submit" class="btn btn-primary">
                    <i class="ri-save-line"></i> Simpan
                </button>
                <a href="{{ route('PeriodeAkademik.index') }}" class="btn btn-secondary">
                    <i class="ri-arrow-left-line"></i> Batal
                </a>
            </div>

        </form>
    </div>

@endsection