@extends('layouts.app')

@section('content')

    <div class="page-header">
        <div>
            <div class="breadcrumb">Admin / <a href="{{ route('Kelas.index') }}" style="color:var(--primary);">Data Kelas</a> / Edit</div>
            <h2>Edit Kelas</h2>
        </div>
    </div>

    @if (session('success'))
        <div class="alert alert-success" id="success-alert">
            <i class="ri-checkbox-circle-line"></i> {{ session('success') }}
        </div>
        <script>setTimeout(function() { document.getElementById('success-alert').remove(); }, 3000);</script>
    @endif

    <div class="card">
        <form method="POST" action="{{ route('Kelas.update', $Kelas->id) }}">
            @csrf
            @method('PUT')

            <div class="form-grid">

                <div class="form-group">
                    <label class="form-label">Nama Kelas <span class="required">*</span></label>
                    <input type="text" name="nama_kelas" class="form-control"
                        placeholder="Contoh: X-A, XI-IPA-1, XII-B"
                        value="{{ old('nama_kelas', $Kelas->nama_kelas) }}" required>
                    @error('nama_kelas')<small style="color:#ef4444;">{{ $message }}</small>@enderror
                </div>

                <div class="form-group">
                    <label class="form-label">Periode Akademik</label>
                    <select name="periode_akademik_id" class="form-control">
                        <option value="">-- Pilih Periode --</option>
                        @foreach ($periode as $p)
                            <option value="{{ $p->id }}"
                                {{ old('periode_akademik_id', $Kelas->periode_akademik_id) == $p->id ? 'selected' : '' }}>
                                {{ $p->nama_periode }}
                            </option>
                        @endforeach
                    </select>
                    @error('periode_akademik_id')<small style="color:#ef4444;">{{ $message }}</small>@enderror
                </div>

                <div class="form-group">
                    <label class="form-label">Wali Kelas</label>
                    <select name="wali_kelas_id" class="form-control">
                        <option value="">-- Pilih Wali Kelas --</option>
                        @foreach ($guru as $g)
                            <option value="{{ $g->id }}"
                                {{ old('wali_kelas_id', $Kelas->wali_kelas_id) == $g->id ? 'selected' : '' }}>
                                {{ $g->nama_guru }}
                            </option>
                        @endforeach
                    </select>
                    @error('wali_kelas_id')<small style="color:#ef4444;">{{ $message }}</small>@enderror
                </div>

                <div class="form-group">
                    <label class="form-label">Ketua Kelas</label>
                    <select name="ketua_kelas_id" class="form-control">
                        <option value="">-- Pilih Ketua Kelas --</option>
                        @foreach ($siswa as $s)
                            <option value="{{ $s->id }}"
                                {{ old('ketua_kelas_id', $Kelas->ketua_kelas_id) == $s->id ? 'selected' : '' }}>
                                {{ $s->nama_siswa }}
                            </option>
                        @endforeach
                    </select>
                    @error('ketua_kelas_id')<small style="color:#ef4444;">{{ $message }}</small>@enderror
                </div>

            </div>

            <div class="form-actions">
                <button type="submit" class="btn btn-primary">
                    <i class="ri-save-line"></i> Simpan Perubahan
                </button>
                <a href="{{ route('Kelas.index') }}" class="btn btn-secondary">
                    <i class="ri-arrow-left-line"></i> Batal
                </a>
            </div>

        </form>
    </div>

@endsection