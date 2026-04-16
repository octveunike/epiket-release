@extends('layouts.app')

@section('content')

<div class="page-header">
    <div>
        <div class="breadcrumb">Admin / <a href="{{ route('Absensi.index') }}" style="color:var(--primary);">Data Absensi</a> / Tambah</div>
        <h2>Tambah Absensi</h2>
    </div>
</div>

<div class="card">
    <form action="{{ route('Absensi.store') }}" method="POST">
        @csrf

        <div class="form-grid" style="grid-template-columns:1fr 1fr 1fr;">

            <div class="form-group">
                <label class="form-label">Kelas <span class="required">*</span></label>
                <select name="kelas_id" class="form-control" required>
                    <option value="">-- Pilih Kelas --</option>
                    @foreach ($kelas as $k)
                        <option value="{{ $k->id }}" {{ old('kelas_id') == $k->id ? 'selected' : '' }}>
                            {{ $k->nama_kelas }}
                        </option>
                    @endforeach
                </select>
                @error('kelas_id')<small style="color:#ef4444;">{{ $message }}</small>@enderror
            </div>

            <div class="form-group">
                <label class="form-label">Tanggal <span class="required">*</span></label>
                <input type="date" name="tanggal" class="form-control" required
                    value="{{ old('tanggal', date('Y-m-d')) }}">
                @error('tanggal')<small style="color:#ef4444;">{{ $message }}</small>@enderror
            </div>

            <div class="form-group">
                <label class="form-label">Periode Akademik <span class="required">*</span></label>
                <input type="text" class="form-control" value="{{ $periodeAktif->nama_periode }}" readonly>
                @error('periode_akademik_id')<small style="color:#ef4444;">{{ $message }}</small>@enderror
            </div>

        </div>

        <div class="form-actions">
            <button type="submit" class="btn btn-primary">
                <i class="ri-save-line"></i> Simpan
            </button>
            <a href="{{ route('Absensi.index') }}" class="btn btn-secondary">
                <i class="ri-arrow-left-line"></i> Batal
            </a>
        </div>

    </form>
</div>

@endsection