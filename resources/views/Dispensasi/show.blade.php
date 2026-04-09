@extends('layouts.app')

@section('content')

<div class="page-header">
    <div>
        <div class="breadcrumb">
            Admin / <a href="{{ route('Dispensasi.index') }}" class="breadcrumb-link">Dispensasi</a> / Detail
        </div>
        <h2>Detail Dispensasi</h2>
    </div>
    <a href="{{ route('Dispensasi.index') }}" class="btn btn-secondary btn-sm">
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

{{-- Info Dispensasi --}}
<div class="card">
    <div class="card-title"><i class="ri-information-line"></i> Info Dispensasi</div>
    <div class="ab-info-grid">
        <div>
            <div class="ab-info-label">Kegiatan</div>
            <div class="ab-info-value">{{ $dispensasi->kegiatan }}</div>
        </div>
        <div>
            <div class="ab-info-label">Organisasi</div>
            <div class="ab-info-value-sm">{{ $dispensasi->organisasi->nama_organisasi ?? '—' }}</div>
        </div>
        <div>
            <div class="ab-info-label">Status</div>
            <div>
                @php $status = $dispensasi->statusVerifikasi->nama_status ?? 'Menunggu'; @endphp
                <span class="badge {{ $dispensasi->status_verifikasi_id === $statusDisetujuiId ? 'badge-success' : 'badge-warning' }}">
                    {{ $status }}
                </span>
            </div>
        </div>
        <div>
            <div class="ab-info-label">Waktu Mulai</div>
            <div class="ab-info-value-sm">
                {{ \Carbon\Carbon::parse($dispensasi->waktu_mulai)->translatedFormat('d F Y, H:i') }}
            </div>
        </div>
        <div>
            <div class="ab-info-label">Waktu Selesai</div>
            <div class="ab-info-value-sm">
                {{ \Carbon\Carbon::parse($dispensasi->waktu_selesai)->translatedFormat('d F Y, H:i') }}
            </div>
        </div>
        <div>
            <div class="ab-info-label">Lampiran</div>
            <div>
                @if ($dispensasi->lampiran_dispensasi)
                    <a href="{{ asset('storage/' . $dispensasi->lampiran_dispensasi) }}"
                        target="_blank" class="link-lampiran">
                        <i class="ri-attachment-2"></i> Lihat Lampiran
                    </a>
                @else
                    <span class="text-muted-sm">—</span>
                @endif
            </div>
        </div>
    </div>

    {{-- Tombol Verifikasi — hanya tampil kalau belum disetujui dan sudah ada siswa --}}
    @if ($dispensasi->status_verifikasi_id !== $statusDisetujuiId && $dispensasi->details->isNotEmpty())
        <div style="margin-top:16px;padding-top:16px;border-top:1px solid var(--border);">
            <button type="button" class="btn btn-success" onclick="showVerifikasiModal()">
                <i class="ri-checkbox-circle-line"></i> Verifikasi & Update Absensi
            </button>
            <small class="text-muted-sm" style="margin-left:10px;">
                Akan mengubah status absensi {{ $dispensasi->details->count() }} siswa menjadi <strong>Dispen</strong>
                pada tanggal {{ \Carbon\Carbon::parse($dispensasi->waktu_mulai)->format('d/m/Y') }}
                s/d {{ \Carbon\Carbon::parse($dispensasi->waktu_selesai)->format('d/m/Y') }}.
            </small>
        </div>
    @endif
</div>

{{-- Form Tambah Siswa — sembunyikan kalau sudah diverifikasi --}}
@if ($dispensasi->status_verifikasi_id !== $statusDisetujuiId)
    <div class="card">
        <div class="card-title"><i class="ri-user-add-line"></i> Tambah Siswa Dispensasi</div>
        <form method="POST" action="{{ route('Dispensasi.storeDetail', $dispensasi->id) }}">
            @csrf
            <div style="display:flex;gap:12px;align-items:flex-end;flex-wrap:wrap;">
                <div class="form-group" style="margin-bottom:0;flex:2;min-width:180px;">
                    <label class="form-label">Kelas</label>
                    <select id="kelas-select" class="form-control">
                        <option value="">-- Pilih Kelas --</option>
                        @foreach ($kelas as $k)
                            <option value="{{ $k->id }}">{{ $k->nama_kelas }}</option>
                        @endforeach
                    </select>
                </div>
                <div class="form-group" style="margin-bottom:0;flex:3;min-width:180px;">
                    <label class="form-label">Siswa <span class="required">*</span></label>
                    <select name="siswa_id" id="siswa-select" class="form-control" required>
                        <option value="">-- Pilih Kelas dulu --</option>
                    </select>
                    @error('siswa_id')<small style="color:#ef4444;">{{ $message }}</small>@enderror
                </div>
                <div style="flex-shrink:0;padding-bottom:1px;">
                    <button type="submit" class="btn btn-primary">
                        <i class="ri-user-add-line"></i> Tambah
                    </button>
                </div>
            </div>
        </form>
    </div>
@endif

{{-- Daftar Siswa --}}
@if ($dispensasi->details->isNotEmpty())
    <div class="card">
        <div class="card-title">
            <i class="ri-group-line"></i> Daftar Siswa
            <span class="ab-ipill ab-ipill-t" style="margin-left:8px;">{{ $dispensasi->details->count() }} siswa</span>
        </div>
        <div class="table-responsive">
            <table>
                <thead>
                    <tr>
                        <th class="col-no">No</th>
                        <th>Nama Siswa</th>
                        <th>Kelas</th>
                        @if ($dispensasi->status_verifikasi_id !== $statusDisetujuiId)
                            <th class="col-center">Aksi</th>
                        @endif
                    </tr>
                </thead>
                <tbody>
                    @foreach ($dispensasi->details as $detail)
                        <tr>
                            <td class="col-no">{{ $loop->iteration }}</td>
                            <td><strong>{{ $detail->siswa->nama_siswa ?? '—' }}</strong></td>
                            <td>{{ $detail->siswa->kelas->nama_kelas ?? '—' }}</td>
                            @if ($dispensasi->status_verifikasi_id !== $statusDisetujuiId)
                                <td class="col-center">
                                    <button type="button" class="btn btn-sm btn-danger"
                                        onclick="showDeleteModal({{ $detail->id }})">
                                        <i class="ri-delete-bin-line"></i> Hapus
                                    </button>
                                </td>
                            @endif
                        </tr>
                    @endforeach
                </tbody>
            </table>
        </div>
    </div>
@endif

{{-- Modal Verifikasi --}}
<div class="confirm-overlay" id="verifikasiModal">
    <div class="confirm-box">
        <div class="confirm-icon" style="border-color:#43a047;color:#43a047;">
            <i class="ri-checkbox-circle-line" style="font-size:26px;"></i>
        </div>
        <h3>Verifikasi Dispensasi?</h3>
        <p>
            Absensi <strong>{{ $dispensasi->details->count() }} siswa</strong> akan diubah menjadi
            <strong>Dispen</strong> pada semua tanggal absensi yang ada di rentang waktu ini.
            Tindakan ini tidak bisa dibatalkan.
        </p>
        <div class="confirm-actions">
            <form method="POST" action="{{ route('Dispensasi.verifikasi', $dispensasi->id) }}" style="display:inline;">
                @csrf @method('PATCH')
                <button type="submit" class="btn btn-success">
                    <i class="ri-checkbox-circle-line"></i> Ya, Verifikasi
                </button>
            </form>
            <button onclick="closeVerifikasiModal()" class="btn btn-secondary">Batal</button>
        </div>
    </div>
</div>

{{-- Modal Hapus Siswa --}}
<div class="confirm-overlay" id="deleteModal">
    <div class="confirm-box">
        <div class="confirm-icon">!</div>
        <h3>Are you sure?</h3>
        <p>Siswa ini akan dihapus dari daftar dispensasi.</p>
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
const siswaPerKelas = @json($siswaPerKelas);

document.getElementById('kelas-select')?.addEventListener('change', function () {
    const kelasId = this.value;
    const siswaSelect = document.getElementById('siswa-select');
    siswaSelect.innerHTML = '<option value="">-- Pilih Siswa --</option>';
    if (kelasId && siswaPerKelas[kelasId]) {
        siswaPerKelas[kelasId].forEach(s => {
            const opt = document.createElement('option');
            opt.value = s.id;
            opt.textContent = s.nama_siswa;
            siswaSelect.appendChild(opt);
        });
    }
});

function showVerifikasiModal() {
    document.getElementById('verifikasiModal').classList.add('show');
}
function closeVerifikasiModal() {
    document.getElementById('verifikasiModal').classList.remove('show');
}

function showDeleteModal(id) {
    document.getElementById('delete-form').action = "{{ route('Dispensasi.destroyDetail', '') }}/" + id;
    document.getElementById('deleteModal').classList.add('show');
}
function closeDeleteModal() {
    document.getElementById('deleteModal').classList.remove('show');
}

document.querySelectorAll('.confirm-overlay').forEach(el => {
    el.addEventListener('click', function(e) {
        if (e.target === this) this.classList.remove('show');
    });
});
</script>
@endpush