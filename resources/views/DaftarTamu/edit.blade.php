@extends('layouts.app')

@section('content')

    <div class="page-header">
        <div>
            <div class="breadcrumb">Admin / <a href="{{ route('DaftarTamu.index') }}" style="color:var(--primary);">Data Daftar Tamu</a> / Edit</div>
            <h2>Edit Data Tamu</h2>
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
        <form method="POST" action="{{ route('DaftarTamu.update', $DaftarTamu->id) }}">
            @csrf
            @method('PUT')

            <div class="form-grid">

                <div class="form-group">
                    <label class="form-label">Tanggal Kunjungan <span class="required">*</span></label>
                    <input type="date" name="tanggal_kunjungan" class="form-control" required
                        value="{{ old('tanggal_kunjungan', $DaftarTamu->tanggal_kunjungan ?? \Carbon\Carbon::today()->format('Y-m-d')) }}">
                    @error('tanggal_kunjungan')<small style="color:#ef4444;">{{ $message }}</small>@enderror
                </div>

                <div class="form-group">
                    <label class="form-label">Nama <span class="required">*</span></label>
                    <input type="text" name="nama" class="form-control" placeholder="Masukkan Nama" required
                        value="{{ old('nama', $DaftarTamu->nama) }}">
                    @error('nama')<small style="color:#ef4444;">{{ $message }}</small>@enderror
                </div>

                <div class="form-group">
                    <label class="form-label">Lembaga / Organisasi</label>
                    <input type="text" name="lembaga_organisasi" class="form-control"
                        value="{{ old('lembaga_organisasi', $DaftarTamu->lembaga_organisasi) }}">
                    @error('lembaga_organisasi')<small style="color:#ef4444;">{{ $message }}</small>@enderror
                </div>

                <div class="form-group">
                    <label class="form-label">Alamat</label>
                    <textarea name="alamat" class="form-control">{{ old('alamat', $DaftarTamu->alamat) }}</textarea>
                    @error('alamat')<small style="color:#ef4444;">{{ $message }}</small>@enderror
                </div>

                <div class="form-group">
                    <label class="form-label">Orang yang Dituju</label>
                    <input type="text" name="orang_yang_dituju" class="form-control"
                        value="{{ old('orang_yang_dituju', $DaftarTamu->orang_yang_dituju) }}">
                    @error('orang_yang_dituju')<small style="color:#ef4444;">{{ $message }}</small>@enderror
                </div>

                <div class="form-group">
                    <label class="form-label">Tujuan Kunjungan</label>
                    <textarea name="tujuan_kunjungan" class="form-control">{{ old('tujuan_kunjungan', $DaftarTamu->tujuan_kunjungan) }}</textarea>
                    @error('tujuan_kunjungan')<small style="color:#ef4444;">{{ $message }}</small>@enderror
                </div>

            </div>

            <div class="form-actions">
                <button type="submit" class="btn btn-primary">
                    <i class="ri-save-line"></i> Simpan Perubahan
                </button>
                <a href="{{ route('DaftarTamu.index') }}" class="btn btn-secondary">
                    <i class="ri-arrow-left-line"></i> Batal
                </a>
            </div>

        </form>
    </div>

@endsection