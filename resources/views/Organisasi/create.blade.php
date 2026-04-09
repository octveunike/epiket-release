@extends('layouts.app')

@section('content')

    <div class="page-header">
        <div>
            <div class="breadcrumb">Admin / <a href="{{ route('Organisasi.index') }}" style="color:var(--primary);">Data Organisasi</a> / Tambah</div>
            <h2>Tambah Organisasi</h2>
        </div>
    </div>

    @if (session('error'))
        <div class="alert alert-danger"><i class="ri-error-warning-line"></i> {{ session('error') }}</div>
    @endif

    <div class="card">
        <form action="{{ route('Organisasi.store') }}" method="POST">
            @csrf

            <div class="form-grid">

                <div class="form-group">
                    <label class="form-label">Nama Organisasi <span class="required">*</span></label>
                    <input type="text" name="nama_organisasi" class="form-control"
                        placeholder="Masukkan nama organisasi"
                        value="{{ old('nama_organisasi') }}" required>
                    @error('nama_organisasi')<small style="color:#ef4444;">{{ $message }}</small>@enderror
                </div>

                <div class="form-group">
                    <label class="form-label">Pembina</label>
                    <select name="pembina_id" class="form-control">
                        <option value="">-- Pilih Pembina --</option>
                        @foreach ($gurus as $g)
                            <option value="{{ $g->id }}" {{ old('pembina_id') == $g->id ? 'selected' : '' }}>
                                {{ $g->nama_guru }}
                            </option>
                        @endforeach
                    </select>
                    @error('pembina_id')<small style="color:#ef4444;">{{ $message }}</small>@enderror
                </div>

                <div class="form-group" style="grid-column:1/-1;">
                    <label class="form-label">Keterangan</label>
                    <textarea name="keterangan" class="form-control" rows="2"
                        placeholder="Deskripsi singkat organisasi">{{ old('keterangan') }}</textarea>
                    @error('keterangan')<small style="color:#ef4444;">{{ $message }}</small>@enderror
                </div>

            </div>

            <p style="color:var(--text-muted);font-size:13px;margin-top:4px;">
                <i class="ri-information-line"></i>
                Setelah menyimpan, Anda akan diarahkan ke halaman edit untuk menambahkan anggota.
            </p>

            <div class="form-actions">
                <button type="submit" class="btn btn-primary">
                    <i class="ri-save-line"></i> Simpan & Kelola Anggota
                </button>
                <a href="{{ route('Organisasi.index') }}" class="btn btn-secondary">
                    <i class="ri-arrow-left-line"></i> Batal
                </a>
            </div>

        </form>
    </div>

@endsection