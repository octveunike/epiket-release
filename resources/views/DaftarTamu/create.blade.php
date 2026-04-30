@extends('layouts.app')

@section('content')

    <div class="page-header">
        <div>
            <div class="breadcrumb">Admin / <a href="{{ route('DaftarTamu.index') }}" style="color:var(--primary);">Data Daftar Tamu</a> / Tambah</div>
            <h2>Tambah Data Tamu</h2>
        </div>
    </div>

    <div class="card">
        <form action="{{ route('DaftarTamu.store') }}" method="POST">
            @csrf

            <div class="form-grid">

                <div class="form-group">
                    <label class="form-label">Tanggal Kunjungan <span class="required">*</span></label>
                    <input type="date" name="tanggal_kunjungan" class="form-control" required
                        value="{{ old('tanggal_kunjungan', \Carbon\Carbon::today()->format('Y-m-d')) }}">
                    @error('tanggal_kunjungan')<small style="color:#ef4444;">{{ $message }}</small>@enderror
                </div>

                <div class="form-group">
                    <label class="form-label">Nama <span class="required">*</span></label>
                    <input type="text" name="nama" class="form-control" placeholder="Masukkan Nama" required
                        value="{{ old('nama') }}">
                    @error('nama')<small style="color:#ef4444;">{{ $message }}</small>@enderror
                </div>

                <div class="form-group">
                    <label class="form-label">Lembaga / Organisasi</label>
                    <input type="text" name="lembaga_organisasi" class="form-control" placeholder="Masukkan Lembaga / Organisasi"
                        value="{{ old('lembaga_organisasi') }}">
                    @error('lembaga_organisasi')<small style="color:#ef4444;">{{ $message }}</small>@enderror
                </div>

                <div class="form-group">
                    <label class="form-label">Alamat</label>
                    <input type="text" name="alamat" class="form-control" placeholder="Masukkan Alamat"
                        value="{{ old('alamat') }}">
                    @error('alamat')<small style="color:#ef4444;">{{ $message }}</small>@enderror
                </div>

                <div class="form-group">
                    <label class="form-label">Orang yang Dituju</label>
                    <input type="text" name="orang_yang_dituju" class="form-control" placeholder="Masukkan Nama Tujuan"
                        value="{{ old('orang_yang_dituju') }}">
                    @error('orang_yang_dituju')<small style="color:#ef4444;">{{ $message }}</small>@enderror
                </div>

                <div class="form-group">
                    <label class="form-label">Tujuan Kunjungan</label>
                    <input type="text" name="tujuan_kunjungan" class="form-control" placeholder="Masukkan Tujuan Kunjungan"
                        value="{{ old('tujuan_kunjungan') }}">
                    @error('tujuan_kunjungan')<small style="color:#ef4444;">{{ $message }}</small>@enderror
                </div>
            </div>
            <div class="form-actions">
                <button type="submit" class="btn btn-primary">
                    <i class="ri-save-line"></i> Simpan
                </button>
                <a href="{{ route('DaftarTamu.index') }}" class="btn btn-secondary">
                    <i class="ri-arrow-left-line"></i> Batal
                </a>
            </div>

        </form>
    </div>

@endsection