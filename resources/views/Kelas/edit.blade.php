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
                    @php
                        $periodeAktif = $periode->firstWhere('status', 1);
                        $selectedPeriode = old('periode_akademik_id', $Kelas->periode_akademik_id ?? ($periodeAktif->id ?? null));
                    @endphp
                    <select name="periode_akademik_id" class="form-control">
                        <option value="">-- Pilih Periode --</option>
                        @foreach ($periode as $p)
                            <option value="{{ $p->id }}"
                                {{ $selectedPeriode == $p->id ? 'selected' : '' }}>
                                {{ $p->nama_periode }}{{ $p->status == 1 ? ' (Aktif)' : '' }}
                            </option>
                        @endforeach
                    </select>
                    @error('periode_akademik_id')<small style="color:#ef4444;">{{ $message }}</small>@enderror
                </div>

                <div class="form-group">
                    <label class="form-label">Wali Kelas</label>
                    <select name="wali_kelas_id" id="wali-kelas-select" class="form-control">
                        <option value="" data-user-id="" data-guru-id="">-- Pilih Wali Kelas --</option>
                        @foreach ($guru as $g)
                            <option value="{{ $g->id }}"
                                data-user-id="{{ $g->user_id ?? '' }}"
                                data-guru-id="{{ $g->id }}"
                                {{ old('wali_kelas_id', $Kelas->wali_kelas_id) == $g->id ? 'selected' : '' }}>
                                {{ $g->nama_guru }}
                            </option>
                        @endforeach
                    </select>

                    <div id="wali-warning" class="account-warning" style="display:{{ $errors->has('wali_kelas_id') ? 'flex' : 'none' }};">
                        <i class="ri-error-warning-line"></i>
                        <span>Guru ini belum punya akun untuk dijadikan Wali Kelas.</span>
                        <a href="#" id="wali-edit-link" target="_blank">Edit Guru</a>
                    </div>
                </div>

                <div class="form-group">
                    <label class="form-label">Ketua Kelas</label>
                    <select name="ketua_kelas_id" id="ketua-kelas-select" class="form-control">
                        <option value="" data-user-id="" data-siswa-id="">-- Pilih Ketua Kelas --</option>
                        @foreach ($siswa as $s)
                            <option value="{{ $s->id }}"
                                data-user-id="{{ $s->user_id ?? '' }}"
                                data-siswa-id="{{ $s->id }}"
                                {{ old('ketua_kelas_id', $Kelas->ketua_kelas_id) == $s->id ? 'selected' : '' }}>
                                {{ $s->nama_siswa }}
                            </option>
                        @endforeach
                    </select>
                    @if ($siswa->isEmpty())
                        <small style="color:var(--text-muted);">Belum ada siswa terdaftar di kelas ini.</small>
                    @endif

                    <div id="ketua-warning" class="account-warning" style="display:{{ $errors->has('ketua_kelas_id') ? 'flex' : 'none' }};">
                        <i class="ri-error-warning-line"></i>
                        <span>Siswa ini belum punya akun untuk dijadikan Ketua Kelas.</span>
                        <a href="#" id="ketua-edit-link" target="_blank">Edit Siswa</a>
                    </div>
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

@push('scripts')
<script>
(function () {
    // Query param untuk dibawa ke halaman Edit Guru/Siswa supaya bisa balik ke sini
    const returnParam = '?return_to=kelas_edit&kelas_id={{ $Kelas->id }}';

    function bindAccountGuard({ selectId, warnId, linkId, editUrlTemplate, dataKey }) {
        const sel      = document.getElementById(selectId);
        const warn     = document.getElementById(warnId);
        const editLink = document.getElementById(linkId);
        if (!sel) return;

        function refresh() {
            const opt    = sel.options[sel.selectedIndex];
            const userId = opt?.getAttribute('data-user-id');
            const recId  = opt?.getAttribute(dataKey);

            if (sel.value && !userId) {
                warn.style.display = 'flex';
                editLink.href = editUrlTemplate.replace('__ID__', recId) + returnParam;
            } else {
                warn.style.display = 'none';
            }
        }

        sel.addEventListener('change', refresh);
        refresh();
    }

    bindAccountGuard({
        selectId:        'ketua-kelas-select',
        warnId:          'ketua-warning',
        linkId:          'ketua-edit-link',
        editUrlTemplate: '{{ route('Siswa.edit', ['id' => '__ID__']) }}',
        dataKey:         'data-siswa-id',
    });

    bindAccountGuard({
        selectId:        'wali-kelas-select',
        warnId:          'wali-warning',
        linkId:          'wali-edit-link',
        editUrlTemplate: '{{ route('Guru.edit', ['id' => '__ID__']) }}',
        dataKey:         'data-guru-id',
    });
})();
</script>
@endpush