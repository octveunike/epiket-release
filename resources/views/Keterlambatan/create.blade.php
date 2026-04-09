@extends('layouts.app')

@section('content')

<div class="page-header">
    <div>
        <div class="breadcrumb">
            Admin / <a href="{{ route('Keterlambatan.index') }}" class="breadcrumb-link">Keterlambatan</a> / Catat
        </div>
        <h2>Catat Keterlambatan</h2>
    </div>
    <a href="{{ route('Keterlambatan.index') }}" class="btn btn-secondary btn-sm">
        <i class="ri-arrow-left-line"></i> Kembali
    </a>
</div>

@if (session('success'))
    <div class="alert alert-success" id="s-alert">
        <i class="ri-checkbox-circle-line"></i> {{ session('success') }}
    </div>
    <script>setTimeout(()=>document.getElementById('s-alert')?.remove(),3000);</script>
@endif
@if (session('error'))
    <div class="alert alert-danger"><i class="ri-error-warning-line"></i> {{ session('error') }}</div>
@endif

{{-- Step 1: Pilih Kelas (auto-submit on change) --}}
<div class="card">
    <div class="card-title"><i class="ri-door-open-line"></i> Pilih Kelas</div>
    <form method="GET" action="{{ route('Keterlambatan.create') }}" id="form-kelas">
        <div class="form-group" style="margin-bottom:0;">
            <label class="form-label">Kelas <span class="required">*</span></label>
            <select name="kelas_id" id="kelas-select" class="form-control" required>
                <option value="">-- Pilih Kelas --</option>
                @foreach ($kelas as $k)
                    <option value="{{ $k->id }}" {{ request('kelas_id') == $k->id ? 'selected' : '' }}>
                        {{ $k->nama_kelas }}
                    </option>
                @endforeach
            </select>
        </div>
    </form>
</div>

{{-- Area konten yang berubah sesuai kelas (di-render server-side saat ada kelas_id) --}}
<div id="kelas-content">
@if (request('kelas_id'))

    @if (!$absensi)
        <div class="alert alert-warning">
            <i class="ri-error-warning-line"></i>
            Absensi hari ini untuk kelas ini belum dibuat. Hubungi admin untuk membuat data absensi terlebih dahulu.
        </div>

    @else

        @if ($siswa->isNotEmpty())
            <div class="card">
                <div class="card-title"><i class="ri-time-line"></i> Form Keterlambatan</div>
                <form method="POST" action="{{ route('Keterlambatan.store') }}" id="form-keterlambatan">
                    @csrf
                    <input type="hidden" name="absensi_id" value="{{ $absensi->id }}">

                    <div style="display:flex;gap:12px;align-items:flex-end;flex-wrap:wrap;">
                        <div class="form-group" style="margin-bottom:0;flex:2;min-width:180px;">
                            <label class="form-label">Siswa <span class="required">*</span></label>
                            <select name="siswa_id" class="form-control" required>
                                <option value="">-- Pilih Siswa --</option>
                                @foreach ($siswa as $s)
                                    <option value="{{ $s->id }}" {{ old('siswa_id') == $s->id ? 'selected' : '' }}>
                                        {{ $s->nama_siswa }}
                                    </option>
                                @endforeach
                            </select>
                            @error('siswa_id')<small style="color:#ef4444;">{{ $message }}</small>@enderror
                        </div>

                        <div class="form-group" style="margin-bottom:0;flex:0 0 140px;">
                            <label class="form-label">Waktu Masuk <span class="required">*</span></label>
                            <input type="time" name="waktu_masuk" class="form-control"
                                value="{{ old('waktu_masuk', now()->format('H:i')) }}" required>
                            @error('waktu_masuk')<small style="color:#ef4444;">{{ $message }}</small>@enderror
                        </div>

                        <div class="form-group" style="margin-bottom:0;flex:3;min-width:180px;">
                            <label class="form-label">Alasan</label>
                            <input type="text" name="alasan" class="form-control"
                                placeholder="Mis: macet, bangun kesiangan…"
                                value="{{ old('alasan') }}">
                        </div>

                        <div style="flex-shrink:0;padding-bottom:1px;">
                            <button type="submit" class="btn btn-primary">
                                <i class="ri-save-line"></i> Simpan
                            </button>
                        </div>
                    </div>
                </form>
            </div>
        @else
            <div class="alert alert-info">
                <i class="ri-information-line"></i>
                Semua siswa di kelas ini sudah tercatat terlambat hari ini.
            </div>
        @endif

        @if ($sudahTercatat->isNotEmpty())
            <div class="card">
                <div class="table-responsive">
                    <table>
                        <thead>
                            <tr>
                                <th class="col-no">No</th>
                                <th>Nama Siswa</th>
                                <th class="col-center">Waktu Masuk</th>
                                <th>Alasan</th>
                                <th class="col-center">Aksi</th>
                            </tr>
                        </thead>
                        <tbody>
                            @foreach ($sudahTercatat as $kt)
                                <tr>
                                    <td class="col-no">{{ $loop->iteration }}</td>
                                    <td><strong>{{ $kt->siswa->nama_siswa ?? '—' }}</strong></td>
                                    <td class="col-center" style="font-weight:600;color:var(--primary);">
                                        {{ \Carbon\Carbon::parse($kt->waktu_masuk)->format('H:i') }}
                                    </td>
                                    <td class="text-muted-sm">{{ $kt->alasan ?: '—' }}</td>
                                    <td class="col-center">
                                        <button type="button" class="btn btn-sm btn-danger"
                                            onclick="showDeleteModal({{ $kt->id }})">
                                            <i class="ri-delete-bin-line"></i> Hapus
                                        </button>
                                    </td>
                                </tr>
                            @endforeach
                        </tbody>
                    </table>
                </div>
            </div>
        @endif

    @endif
@endif
</div>

{{-- Modal hapus konsisten dengan Kelas index --}}
<div class="confirm-overlay" id="deleteModal">
    <div class="confirm-box">
        <div class="confirm-icon">!</div>
        <h3>Are you sure?</h3>
        <p>Data keterlambatan ini akan dihapus.<br>Status absensi siswa akan dikembalikan ke <strong>Hadir</strong>.</p>
        <div class="confirm-actions">
            <form id="delete-form" method="POST" style="display:inline;">
                @csrf @method('DELETE')
                <button type="submit" class="btn btn-danger">Yes, delete it!</button>
            </form>
            <button onclick="closeDeleteModal()" class="btn btn-secondary">Cancel</button>
        </div>
    </div>
</div>

@endsection

@push('scripts')
<script>
// Auto-submit form kelas saat dropdown berubah
document.getElementById('kelas-select').addEventListener('change', function () {
    if (this.value) {
        // Tampilkan loading state
        const content = document.getElementById('kelas-content');
        content.style.opacity = '0.4';
        content.style.pointerEvents = 'none';
        document.getElementById('form-kelas').submit();
    } else {
        // Kosongkan konten jika pilih "--"
        document.getElementById('kelas-content').innerHTML = '';
        window.history.replaceState({}, '', '{{ route('Keterlambatan.create') }}');
    }
});

function showDeleteModal(id) {
    document.getElementById('delete-form').action = "{{ route('Keterlambatan.destroy', '') }}/" + id;
    document.getElementById('deleteModal').classList.add('show');
}
function closeDeleteModal() {
    document.getElementById('deleteModal').classList.remove('show');
}
const dm = document.getElementById('deleteModal');
if (dm) dm.addEventListener('click', function(e) {
    if (e.target === this) closeDeleteModal();
});
</script>
@endpush