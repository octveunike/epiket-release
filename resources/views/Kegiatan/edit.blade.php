@extends('layouts.app')

@section('content')

    {{-- Page Header --}}
    <div class="page-header">
        <div>
            <div class="breadcrumb">Menu / <a href="{{ route('Kegiatan.index') }}" style="color:var(--primary);">Daftar Kegiatan</a> / Edit</div>
            <h2>Edit Kegiatan</h2>
        </div>
    </div>

    {{-- Alert --}}
    @if (session('success'))
        <div class="alert alert-success" id="success-alert">
            <i class="ri-checkbox-circle-line"></i> {{ session('success') }}
        </div>
        <script>
            setTimeout(function() {
                document.getElementById('success-alert').remove();
            }, 3000);
        </script>
    @endif

    {{-- Form Card --}}
    <div class="card">
        <form id="FormEditKegiatan" method="POST" enctype="multipart/form-data"
            action="{{ !empty($Kegiatan) ? route('Kegiatan.update', $Kegiatan->id) : route('Kegiatan.create') }}">
            @csrf
            @method('PUT')

            <input type="hidden" name="id" value="{{ !empty($Kegiatan) ? $Kegiatan->id : '' }}">

            <div class="form-grid">

                <div class="form-group">
                    <label class="form-label">Judul Kegiatan <span class="required">*</span></label>
                    <input type="text" name="title" class="form-control"
                        placeholder="Masukkan Judul Kegiatan"
                        value="{{ !empty($Kegiatan) ? $Kegiatan->title : '' }}" required>
                </div>

                <div class="form-group">
                    <label class="form-label">Jenis Kegiatan <span class="required">*</span></label>
                    <input type="text" name="tipe_kegiatan" class="form-control"
                        placeholder="Masukkan Jenis Kegiatan"
                        value="{{ !empty($Kegiatan) ? $Kegiatan->tipe_kegiatan : '' }}" required>
                </div>

                <div class="form-group">
                    <label class="form-label">Tanggal Awal <span class="required">*</span></label>
                    <input type="date" name="tgl_awal" class="form-control"
                        value="{{ !empty($Kegiatan) ? $Kegiatan->tgl_awal : '' }}" required>
                </div>

                <div class="form-group">
                    <label class="form-label">Tanggal Akhir <span class="required">*</span></label>
                    <input type="date" name="tgl_akhir" class="form-control"
                        value="{{ !empty($Kegiatan) ? $Kegiatan->tgl_akhir : '' }}" required>
                </div>

                <div class="form-group form-full">
                    <label class="form-label">Deskripsi</label>
                    <textarea name="deskripsi" class="form-control" rows="4"
                        placeholder="Masukkan Deskripsi">{{ !empty($Kegiatan) ? $Kegiatan->deskripsi : '' }}</textarea>
                </div>

                <div class="form-group">
                    <label class="form-label">Kuota Peserta <span class="required">*</span></label>
                    <input type="number" name="kuota_peserta" class="form-control"
                        placeholder="Masukkan Kuota Peserta"
                        value="{{ !empty($Kegiatan) ? $Kegiatan->kuota_peserta : '' }}" required min="1">
                </div>

                <div class="form-group">
                    <label class="form-label">Penyelenggara <span class="required">*</span></label>
                    <input type="text" name="penyelenggara" class="form-control"
                        placeholder="Masukkan Penyelenggara"
                        value="{{ !empty($Kegiatan) ? $Kegiatan->penyelenggara : '' }}" required>
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
                    <small style="color:var(--text-muted); font-size:12px;">Format: JPG, JPEG, PNG. Kosongkan jika tidak ingin mengubah sampul.</small>

                    @if (!empty($Kegiatan->sampul))
                        <div class="sampul-preview">
                            <small style="color:var(--text-muted);">Sampul saat ini:</small><br>
                            <img src="{{ asset('/' . $Kegiatan->sampul) }}" alt="Sampul Kegiatan">
                        </div>
                    @endif
                </div>

            </div>

            <div class="form-actions">
                <button type="submit" class="btn btn-primary">
                    <i class="ri-save-line"></i> Simpan Perubahan
                </button>
                <a href="{{ route('Kegiatan.index') }}" class="btn btn-secondary">
                    <i class="ri-arrow-left-line"></i> Batal
                </a>
            </div>

        </form>
    </div>

@endsection