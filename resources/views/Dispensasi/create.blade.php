@extends('layouts.app')

@section('content')

<div class="page-header">
    <div>
        <div class="breadcrumb">
            Admin / <a href="{{ route('Dispensasi.index') }}" class="breadcrumb-link">Dispensasi</a> / Buat
        </div>
        <h2>Buat Dispensasi</h2>
    </div>
    <a href="{{ route('Dispensasi.index') }}" class="btn btn-secondary btn-sm">
        <i class="ri-arrow-left-line"></i> Kembali
    </a>
</div>

@if (session('error'))
    <div class="alert alert-danger"><i class="ri-error-warning-line"></i> {{ session('error') }}</div>
@endif

<div class="card">
    <div class="card-title"><i class="ri-calendar-event-line"></i> Form Dispensasi</div>
    <form method="POST" action="{{ route('Dispensasi.store') }}" enctype="multipart/form-data">
        @csrf

        <div class="form-grid">
            <div class="form-group">
                <label class="form-label">Organisasi <span class="required">*</span></label>
                <select name="organisasi_id" class="form-control" required>
                    <option value="">-- Pilih Organisasi --</option>
                    @foreach ($organisasi as $org)
                        <option value="{{ $org->id }}" {{ old('organisasi_id') == $org->id ? 'selected' : '' }}>
                            {{ $org->nama_organisasi }}
                        </option>
                    @endforeach
                </select>
                @error('organisasi_id')<small style="color:#ef4444;">{{ $message }}</small>@enderror
            </div>

            <div class="form-group">
                <label class="form-label">Periode Akademik <span class="required">*</span></label>
                <select name="periode_akademik_id" class="form-control" required>
                    <option value="">-- Pilih Periode --</option>
                    @foreach ($periode as $p)
                        <option value="{{ $p->id }}" {{ old('periode_akademik_id') == $p->id ? 'selected' : '' }}>
                            {{ $p->nama_periode }}
                        </option>
                    @endforeach
                </select>
                @error('periode_akademik_id')<small style="color:#ef4444;">{{ $message }}</small>@enderror
            </div>

            <div class="form-group">
                <label class="form-label">Waktu Mulai <span class="required">*</span></label>
                <input type="datetime-local" name="waktu_mulai" class="form-control"
                    value="{{ old('waktu_mulai') }}" required>
                @error('waktu_mulai')<small style="color:#ef4444;">{{ $message }}</small>@enderror
            </div>

            <div class="form-group">
                <label class="form-label">Waktu Selesai <span class="required">*</span></label>
                <input type="datetime-local" name="waktu_selesai" class="form-control"
                    value="{{ old('waktu_selesai') }}" required>
                @error('waktu_selesai')<small style="color:#ef4444;">{{ $message }}</small>@enderror
            </div>

            <div class="form-group form-full">
                <label class="form-label">Nama Kegiatan <span class="required">*</span></label>
                <input type="text" name="kegiatan" class="form-control"
                    placeholder="Mis: Lomba Paskibra Tingkat Kabupaten"
                    value="{{ old('kegiatan') }}" required>
                @error('kegiatan')<small style="color:#ef4444;">{{ $message }}</small>@enderror
            </div>

            <div class="form-group form-full">
                <label class="form-label">Lampiran <span style="color:var(--text-muted);font-weight:400;">(opsional)</span></label>
                <input type="file" name="lampiran_dispensasi" class="form-control"
                    accept=".pdf,.jpg,.jpeg,.png">
                <small style="color:var(--text-muted);margin-top:4px;display:block;">
                    Format: PDF, JPG, PNG. Maks 2MB.
                </small>
                @error('lampiran_dispensasi')<small style="color:#ef4444;">{{ $message }}</small>@enderror
            </div>
        </div>

        <div class="form-actions">
            <button type="submit" class="btn btn-primary">
                <i class="ri-save-line"></i> Simpan & Lanjut Input Siswa
            </button>
            <a href="{{ route('Dispensasi.index') }}" class="btn btn-secondary">Batal</a>
        </div>
    </form>
</div>

@endsection