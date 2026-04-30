@extends('layouts.app')

@section('content')

<div class="page-header">
    <div>
        <div class="breadcrumb">Admin / <span class="breadcrumb-link">Dispensasi</span></div>
        <h2>Data Dispensasi</h2>
    </div>
    @if(auth()->user()->hasRole(['Admin', 'Siswa', 'Ketua Kelas', 'Petugas Piket']))
    <a href="{{ route('Dispensasi.create') }}" class="btn btn-primary">
        <i class="ri-add-line"></i> Ajukan Dispensasi
    </a>
    @endif
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

{{-- Filter --}}
<div class="card" style="padding:14px 20px;margin-bottom:16px;">
    <form method="GET" action="{{ route('Dispensasi.index') }}" style="display:flex;align-items:flex-end;gap:12px;flex-wrap:wrap;">
        <div class="form-group" style="margin-bottom:0;min-width:160px;">
            <label class="form-label">Dari Tanggal</label>
            <input type="date" name="dari" class="form-control" value="{{ request('dari', $dari) }}">
        </div>
        <div class="form-group" style="margin-bottom:0;min-width:160px;">
            <label class="form-label">Sampai Tanggal</label>
            <input type="date" name="sampai" class="form-control" value="{{ request('sampai', $sampai) }}">
        </div>
        <div class="form-group" style="margin-bottom:0;min-width:180px;">
            <label class="form-label">Organisasi</label>
            <select name="organisasi_id" class="form-control">
                <option value="">Semua Organisasi</option>
                @foreach ($organisasi as $org)
                    <option value="{{ $org->id }}" {{ request('organisasi_id') == $org->id ? 'selected' : '' }}>
                        {{ $org->nama_organisasi }}
                    </option>
                @endforeach
            </select>
        </div>
        <button type="submit" class="btn btn-primary" style="margin-bottom:0;">
            <i class="ri-filter-line"></i> Filter
        </button>
        <a href="{{ route('Dispensasi.index') }}" class="btn btn-secondary" style="margin-bottom:0;">
            <i class="ri-refresh-line"></i> Reset
        </a>
    </form>
</div>

<div class="card">
    <div class="table-responsive">
        <table id="tableDispensasi" class="dt-table" data-destroy-url="{{ route('Dispensasi.destroy', '') }}">
            <thead>
                <tr>
                    <th class="col-no">No</th>
                    <th>Kegiatan</th>
                    <th class="col-center">Waktu Mulai</th>
                    <th class="col-center">Waktu Selesai</th>
                    <th class="col-center">Siswa</th>
                    <th class="col-center">Status</th>
                    <th class="col-center">Aksi</th>
                </tr>
            </thead>
            <tbody>
                @foreach ($dispensasi as $d)
                    <tr>
                        <td class="col-no">{{ $loop->iteration }}</td>
                        <td>
                            <strong>{{ $d->kegiatan }}</strong>
                            @if ($d->lampiran_dispensasi)
                                <div>
                                    <a href="{{ asset('storage/' . $d->lampiran_dispensasi) }}"
                                        target="_blank" class="link-lampiran">
                                        <i class="ri-attachment-2"></i> Lampiran
                                    </a>
                                </div>
                            @endif
                        </td>
                        <td class="col-center text-muted-sm">
                            {{ \Carbon\Carbon::parse($d->waktu_mulai)->translatedFormat('d M Y, H:i') }}
                        </td>
                        <td class="col-center text-muted-sm">
                            {{ \Carbon\Carbon::parse($d->waktu_selesai)->translatedFormat('d M Y, H:i') }}
                        </td>
                        <td class="col-center">
                            <span class="ab-ipill ab-ipill-t">{{ $d->details_count }} siswa</span>
                        </td>
                        <td class="col-center">
                            <span class="text-muted-sm">
                                {{ $d->statusValidasi->nama_status ?? 'Menunggu' }}
                            </span>
                        </td>
                        <td class="col-center" style="white-space:nowrap;">
                            @php
                                $statusId        = (int) $d->status_validasi_id;
                                $isMenungguPiket = $statusId === (int) $statusMenungguPiketId;
                                $isOwner         = auth()->user()->hasRole(['Siswa', 'Ketua Kelas'])
                                                   && (int) $d->user_input === (int) auth()->user()->id;
                                $isEditable      = in_array($statusId, [(int) $statusMenungguPengisianId, (int) $statusPerluRevisiId], true);
                            @endphp

                            @if(auth()->user()->hasRole(['Admin', 'Petugas Piket']))
                                @if($isMenungguPiket && $d->details_count > 0)
                                    <button type="button" class="btn btn-sm btn-success"
                                        onclick="showVerifikasiModal({{ $d->id }})">
                                        <i class="ri-checkbox-circle-line"></i> Validasi
                                    </button>
                                @endif
                                <a href="{{ route('Dispensasi.show', $d->id) }}" class="btn btn-sm btn-info">
                                    <i class="ri-eye-line"></i> Detail
                                </a>

                            @elseif(auth()->user()->hasRole(['Siswa', 'Ketua Kelas']))
                                <a href="{{ route('Dispensasi.show', $d->id) }}" class="btn btn-sm btn-info">
                                    <i class="ri-eye-line"></i> Detail
                                </a>
                                @if ($isOwner && $isEditable)
                                    <button type="button" class="btn btn-sm btn-danger"
                                        onclick="showDeleteModal({{ $d->id }})">
                                        <i class="ri-delete-bin-line"></i> Hapus
                                    </button>
                                @endif
                            @endif
                        </td>
                    </tr>
                @endforeach
            </tbody>
        </table>
    </div>
</div>

{{-- Modal Hapus --}}
<div class="confirm-overlay" id="deleteModal">
    <div class="confirm-box">
        <div class="confirm-icon">!</div>
        <h3>Hapus Dispensasi?</h3>
        <p>Data yang dihapus tidak dapat dikembalikan.</p>
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

{{-- Modal Validasi (Admin + Petugas Piket) --}}
@if(auth()->user()->hasRole(['Admin', 'Petugas Piket']))
<div class="confirm-overlay" id="verifikasiModal">
    <div class="confirm-box">
        <div class="confirm-icon" style="border-color:#43a047;color:#43a047;">
            <i class="ri-checkbox-circle-line" style="font-size:26px;"></i>
        </div>
        <h3>Tindakan untuk Dispensasi?</h3>
        <div class="confirm-actions">
            <form id="verifikasi-form" method="POST" style="display:inline;">
                @csrf @method('PATCH')
                <button type="submit" class="btn btn-success">
                    <i class="ri-checkbox-circle-line"></i> Ya, Validasi
                </button>
            </form>
            <form id="revisi-form" method="POST" style="display:inline;">
                @csrf @method('PATCH')
                <button type="submit" class="btn btn-warning">
                    <i class="ri-loop-left-line"></i> Ajukan Revisi
                </button>
            </form>
        </div>
    </div>
</div>
@endif

@endsection

@push('scripts')
<script>
function showDeleteModal(id) {
    document.getElementById('delete-form').action = "{{ route('Dispensasi.destroy', '') }}/" + id;
    document.getElementById('deleteModal').classList.add('show');
}
function closeDeleteModal() {
    document.getElementById('deleteModal').classList.remove('show');
}

function showVerifikasiModal(id) {
    document.getElementById('verifikasi-form').action = '/dispensasi/' + id + '/verifikasi';
    document.getElementById('revisi-form').action     = '/dispensasi/' + id + '/revisi';
    document.getElementById('verifikasiModal').classList.add('show');
}
function closeVerifikasiModal() {
    document.getElementById('verifikasiModal').classList.remove('show');
}

document.querySelectorAll('.confirm-overlay').forEach(el => {
    el.addEventListener('click', function(e) {
        if (e.target === this) this.classList.remove('show');
    });
});
</script>
@endpush