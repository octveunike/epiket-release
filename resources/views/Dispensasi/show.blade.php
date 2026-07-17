@extends('layouts.app')

@section('content')

<div class="page-header">
    <div>
        <div class="breadcrumb">
            Admin / <a href="{{ route('Dispensasi.index') }}" class="breadcrumb-link">Dispensasi</a> / Detail
        </div>
        <h2>Detail Dispensasi</h2>
    </div>
    <div style="display:flex; gap:8px;">
        @if ($canEditDetail)
            <a href="{{ route('Dispensasi.edit', $dispensasi->id) }}" class="btn btn-primary">
                <i class="ri-edit-line"></i> Edit Info Dispensasi
            </a>
        @endif
        <a href="{{ route('Dispensasi.index') }}" class="btn btn-secondary btn-sm">
            <i class="ri-arrow-left-line"></i> Kembali
        </a>
    </div>
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
            <div class="ab-info-value-sm">{{ $dispensasi->nama_kegiatan }}</div>
        </div>
        <div>
            <div class="ab-info-label">Organisasi</div>
            <div class="ab-info-value-sm">{{ $dispensasi->organisasi->nama_organisasi ?? '—' }}</div>
        </div>
        <div>
            <div class="ab-info-label">Status</div>
            <div>
                @php $status = $dispensasi->statusValidasi->nama_status ?? 'Menunggu'; @endphp
                <span class="badge {{ $dispensasi->status_validasi_id === $statusDisetujuiId ? 'badge-success' : 'badge-warning' }}">
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

    {{-- Tombol Ajukan — tampil kalau status Menunggu Pengisian / Perlu Revisi dan sudah ada siswa --}}
    @if (
        $canEditDetail &&
        in_array((int) $dispensasi->status_validasi_id, [(int) $statusMenungguPengisianId, (int) $statusPerluRevisiId], true) &&
        $dispensasi->details->isNotEmpty()
    )
        <div style="margin-top:16px;padding-top:16px;border-top:1px solid var(--border);">
            <button type="button" class="btn btn-primary" onclick="showAjukanModal()">
                <i class="ri-send-plane-line"></i> Ajukan Dispensasi
            </button>
            <small class="text-muted-sm" style="margin-left:10px;">
                @if (auth()->user()->hasRole(['Admin', 'Petugas Piket']))
                    Setelah diajukan, absensi siswa akan diperbarui sesuai rentang dispensasi.
                @else
                    Setelah diajukan, status berubah menjadi <strong>Menunggu Piket</strong> dan siswa tidak dapat ditambah/dihapus.
                @endif
            </small>
        </div>
    @endif

</div>

{{-- Form Tambah Siswa — visibility ditentukan oleh $canEditDetail di controller --}}
@if ($canEditDetail)
    <div class="card">
        <div class="card-title"><i class="ri-user-add-line"></i> Tambah Siswa Dispensasi</div>
        <form method="POST" action="{{ route('Dispensasi.storeDetail', $dispensasi->id) }}">
            @csrf
            <div style="display:flex;gap:12px;align-items:flex-end;flex-wrap:wrap;">
                <div class="form-group" style="margin-bottom:0;flex:2;min-width:180px;">
                    <label class="form-label">Kelas</label>
                    @if ($ketuaKelas)
                        <input type="text" class="form-control" value="{{ $ketuaKelas->nama_kelas }}" readonly tabindex="-1">
                    @else
                        <select id="kelas-select" class="form-control">
                            <option value="">-- Pilih Kelas --</option>
                            @foreach ($kelas as $k)
                                <option value="{{ $k->id }}">{{ $k->nama_kelas }}</option>
                            @endforeach
                        </select>
                    @endif
                </div>
                <div class="form-group" style="margin-bottom:0;flex:3;min-width:180px;">
                    <label class="form-label">Siswa <span class="required">*</span></label>
                    <select name="siswa_id" id="siswa-select" class="form-control" required>
                        @if ($ketuaKelas && isset($siswaPerKelas[$ketuaKelas->id]))
                            <option value="">-- Pilih Siswa --</option>
                            @foreach ($siswaPerKelas[$ketuaKelas->id] as $s)
                                <option value="{{ $s->id }}">{{ $s->nama_siswa }}</option>
                            @endforeach
                        @else
                            <option value="">-- Pilih Kelas dulu --</option>
                        @endif
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
                        @if ($canEditDetail)
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
                            @if ($canEditDetail)
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

{{-- Modal Ajukan --}}
<div class="confirm-overlay" id="ajukanModal">
    <div class="confirm-box">
        <div class="confirm-icon" style="border-color:#0ea5e9;color:#0ea5e9;">
            <i class="ri-send-plane-line" style="font-size:26px;"></i>
        </div>
        <h3>Ajukan Dispensasi?</h3>
        <p>
            @if (auth()->user()->hasRole(['Admin', 'Petugas Piket']))
                Absensi <strong>{{ $dispensasi->details->count() }} siswa</strong> akan diperbarui menjadi
                <strong>Dispen</strong> pada rentang tanggal dispensasi ini. Pastikan semua siswa sudah terdaftar.
            @else
                Absensi <strong>{{ $dispensasi->details->count() }} siswa</strong> akan diperbarui menjadi
                <strong>Dispen</strong> ketika disetujui pada rentang tanggal dispensasi ini. Pastikan semua siswa sudah terdaftar.
            @endif
        </p>
        <div class="confirm-actions">
            <form method="POST" action="{{ route('Dispensasi.ajukan', $dispensasi->id) }}" style="display:inline;">
                @csrf @method('PATCH')
                <button type="submit" class="btn btn-primary">
                    <i class="ri-send-plane-line"></i> Ya, Ajukan
                </button>
            </form>
            <button onclick="closeAjukanModal()" class="btn btn-secondary">Batal</button>
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

function showAjukanModal() {
    document.getElementById('ajukanModal').classList.add('show');
}
function closeAjukanModal() {
    document.getElementById('ajukanModal').classList.remove('show');
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