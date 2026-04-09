@extends('layouts.app')

@section('content')

    {{-- Page Header --}}
    <div class="page-header">
        <div>
            <div class="breadcrumb">Menu / <a href="{{ route('Kegiatan.index') }}" style="color:var(--primary);">Daftar Kegiatan</a> / Tambah</div>
            <h2>Tambah Kegiatan</h2>
        </div>
    </div>

    {{-- Form Card --}}
    <div class="card">
        <form action="{{ route('Kegiatan.store') }}" method="POST" enctype="multipart/form-data">
            @csrf

            <div class="form-grid">

                <div class="form-group">
                    <label class="form-label">Judul Kegiatan <span class="required">*</span></label>
                    <input type="text" name="title" class="form-control" placeholder="Masukkan Judul Kegiatan" required>
                </div>

                <div class="form-group">
                    <label class="form-label">Jenis Kegiatan <span class="required">*</span></label>
                    <input type="text" name="tipe_kegiatan" class="form-control" placeholder="Masukkan Jenis Kegiatan" required>
                </div>

                <div class="form-group">
                    <label class="form-label">Tanggal Awal <span class="required">*</span></label>
                    <input type="date" name="tgl_awal" class="form-control" required>
                </div>

                <div class="form-group">
                    <label class="form-label">Tanggal Akhir <span class="required">*</span></label>
                    <input type="date" name="tgl_akhir" class="form-control" required>
                </div>

                <div class="form-group form-full">
                    <label class="form-label">Deskripsi</label>
                    <textarea name="deskripsi" class="form-control" rows="4" placeholder="Masukkan Deskripsi"></textarea>
                </div>

                <div class="form-group">
                    <label class="form-label">Kuota Peserta <span class="required">*</span></label>
                    <input type="number" name="kuota_peserta" class="form-control" placeholder="Masukkan Kuota Peserta" required min="1">
                </div>

                <div class="form-group">
                    <label class="form-label">Penyelenggara <span class="required">*</span></label>
                    <input type="text" name="penyelenggara" class="form-control" placeholder="Masukkan Penyelenggara" required>
                </div>

                <div class="form-group form-full">
                    <label class="form-label">Sampul Kegiatan</label>
                    <div class="file-input-wrapper">
                        <input type="file" name="sampul" id="sampul" class="file-input-hidden" accept=".jpg,.jpeg,.png">
                        <label for="sampul" class="file-input-label">
                            <i class="ri-upload-cloud-2-line"></i>
                            <span class="file-input-text">Pilih File</span>
                        </label>
                        <span class="file-input-filename" id="sampulFilename">Tidak ada file dipilih</span>
                    </div>
                    <small style="color:var(--text-muted); font-size:12px;">Format: JPG, JPEG, PNG</small>
                </div>

            </div>

            <div class="form-actions">
                <button type="submit" class="btn btn-primary">
                    <i class="ri-save-line"></i> Simpan
                </button>
                <a href="{{ route('Kegiatan.index') }}" class="btn btn-secondary">
                    <i class="ri-arrow-left-line"></i> Batal
                </a>
            </div>

        </form>
    </div>

@endsection