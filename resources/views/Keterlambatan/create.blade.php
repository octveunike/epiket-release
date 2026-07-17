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

{{-- Pilih Kelas + Tanggal --}}
<div class="card">
    <div class="card-title"><i class="ri-door-open-line"></i> Pilih Kelas & Tanggal</div>
    <form method="GET" action="{{ route('Keterlambatan.create') }}" id="form-filter">
        <div style="display:flex;gap:12px;align-items:flex-end;flex-wrap:wrap;">
            <div class="form-group" style="margin-bottom:0;flex:2;min-width:180px;">
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
            <div class="form-group" style="margin-bottom:0;flex:1;min-width:160px;">
                <label class="form-label">Tanggal <span class="required">*</span></label>
                <input type="date" name="tanggal" id="tanggal-input" class="form-control"
                    value="{{ request('tanggal', now()->format('Y-m-d')) }}" required>
            </div>
            <div style="flex-shrink:0;padding-bottom:1px;">
                <button type="submit" class="btn btn-primary">
                    <i class="ri-search-line"></i> Tampilkan
                </button>
            </div>
        </div>
    </form>
</div>

{{-- Konten hasil pencarian --}}
@if (request('kelas_id') && request('tanggal'))

    @if (!$absensi)
        <div class="alert alert-warning">
            <i class="ri-error-warning-line"></i>
            Absensi untuk kelas ini pada tanggal
            <strong>{{ \Carbon\Carbon::parse(request('tanggal'))->translatedFormat('d F Y') }}</strong>
            belum dibuat. Hubungi admin untuk membuat data absensi terlebih dahulu.
        </div>

    @else

        {{-- Info absensi yang ditemukan --}}
        <div class="ab-infobar" style="margin-bottom:16px;">
            <div class="ab-infobar-left">
                <i class="ri-school-line"></i>
                <strong>{{ $absensi->kelas->nama_kelas ?? '—' }}</strong>
                <span class="ab-infobar-sep">·</span>
                <span>{{ \Carbon\Carbon::parse($absensi->tanggal)->translatedFormat('l, d F Y') }}</span>
                <span class="ab-infobar-sep">·</span>
                <span>{{ $absensi->periodeAkademik->nama_periode ?? '—' }}</span>
            </div>
        </div>

        @if ($siswa->isNotEmpty())
            <div class="card">
                <div class="card-title"><i class="ri-time-line"></i> Form Keterlambatan</div>
                <form method="POST" action="{{ route('Keterlambatan.store') }}">
                    @csrf
                    <input type="hidden" name="absensi_id" value="{{ $absensi->id }}">
                    <input type="hidden" name="kelas_id"   value="{{ request('kelas_id') }}">
                    <input type="hidden" name="tanggal"    value="{{ request('tanggal') }}">

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

                        <div class="form-group" style="margin-bottom:0;flex:0 0 150px;">
                            <label class="form-label">Waktu Masuk <span class="required">*</span></label>
                            @include('partials.timepick', [
                                'name'    => 'waktu_masuk',
                                'value'   => old('waktu_masuk'),
                                'max'     => '12:00',
                                'default' => '06:30',
                            ])
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
                Semua siswa di kelas ini sudah tercatat terlambat pada tanggal ini.
            </div>
        @endif

        {{-- Tabel sudah tercatat --}}
        @if ($sudahTercatat->isNotEmpty())
            <div class="card">
                <div style="display:flex;align-items:center;justify-content:space-between;margin-bottom:14px;">
                    <div class="card-title" style="margin-bottom:0;">
                        <i class="ri-list-check-2"></i> Sudah Tercatat Terlambat
                    </div>
                    <span class="badge badge-danger">{{ $sudahTercatat->count() }} siswa</span>
                </div>
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

{{-- Modal Hapus --}}
<div class="confirm-overlay" id="deleteModal">
    <div class="confirm-box">
        <div class="confirm-icon">!</div>
        <h3>Hapus Keterlambatan?</h3>
        <p>Data yang dihapus tidak dapat dikembalikan.<br>
        <small class="text-danger-sm">Status absensi siswa akan dikembalikan ke Hadir.</small></p>
        <div class="confirm-actions">
            <form id="delete-form" method="POST">
                @csrf
                @method('DELETE')
                <button type="submit" class="btn btn-danger">Ya, Hapus</button>
            </form>
            <button onclick="closeDeleteModal()" class="btn btn-secondary">Batal</button>
        </div>
    </div>
</div>

@endsection

@push('scripts')
<script>
// Catatan: logika time picker (#timepick) ada di /public/js/app.js
function showDeleteModal(id) {
    document.getElementById('delete-form').action = "{{ route('Keterlambatan.destroy', '') }}/" + id;
    document.getElementById('deleteModal').classList.add('show');
}
const dm = document.getElementById('deleteModal');
if (dm) dm.addEventListener('click', function (e) {
    if (e.target === this) closeDeleteModal();
});
</script>
@endpush