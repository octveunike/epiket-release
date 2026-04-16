@extends('layouts.app')

@section('content')

<div class="page-header">
    <div>
        <div class="breadcrumb">Wali Kelas / <span class="breadcrumb-link">Validasi Absensi</span></div>
        <h2>Validasi Absensi</h2>
    </div>
    <button type="button" id="btn-validasi-header" class="btn btn-primary"
        onclick="showBulkValidasiModal()">
        <i class="ri-checkbox-circle-line"></i> Validasi Absensi
    </button>
</div>

@if (session('success'))
    <div class="alert alert-success" id="success-alert">
        <i class="ri-checkbox-circle-line"></i> {{ session('success') }}
    </div>
    <script>setTimeout(()=>document.getElementById('success-alert')?.remove(), 3000);</script>
@endif
@if (session('error'))
    <div class="alert alert-danger"><i class="ri-error-warning-line"></i> {{ session('error') }}</div>
@endif

{{-- Filter --}}
<div class="card" style="padding:14px 20px;margin-bottom:16px;">
    <form method="GET" action="{{ route('Absensi.walikelas.index') }}" style="display:flex;align-items:flex-end;gap:12px;flex-wrap:wrap;">
        <div class="form-group" style="margin-bottom:0;min-width:160px;">
            <label class="form-label">Dari Tanggal</label>
            <input type="date" name="dari" class="form-control" value="{{ request('dari') }}">
        </div>
        <div class="form-group" style="margin-bottom:0;min-width:160px;">
            <label class="form-label">Sampai Tanggal</label>
            <input type="date" name="sampai" class="form-control" value="{{ request('sampai') }}">
        </div>
        <button type="submit" class="btn btn-primary" style="margin-bottom:0;">
            <i class="ri-filter-line"></i> Filter
        </button>
        <a href="{{ route('Absensi.walikelas.index') }}" class="btn btn-secondary" style="margin-bottom:0;">
            <i class="ri-refresh-line"></i> Reset
        </a>
    </form>
</div>

{{-- Info jumlah dipilih (simple, tanpa kotak hijau) --}}
<div id="bulk-bar" style="display:none;margin-bottom:10px;font-size:13px;color:var(--text-muted);">
    <i class="ri-checkbox-circle-line"></i>
    <span><strong id="bulk-count">0</strong> absensi dipilih</span>
</div>

<form id="bulk-form" method="POST" action="{{ route('Absensi.walikelas.bulkValidasi') }}">
    @csrf
    <div class="card">
        <div class="table-responsive">
            <table>
                <thead>
                    <tr>
                        <th style="width:36px;text-align:center;">
                            <input type="checkbox" id="check-all" style="cursor:pointer;">
                        </th>
                        <th class="col-no">No</th>
                        <th>Tanggal</th>
                        <th>Kelas</th>
                        <th>Periode Akademik</th>
                        <th class="col-center">Status</th>
                        <th class="col-center">Aksi</th>
                    </tr>
                </thead>
                <tbody>
                    @forelse ($absensiList as $absensi)
                        <tr class="absensi-row" data-id="{{ $absensi->id }}">
                            <td style="text-align:center;">
                                <input type="checkbox" name="ids[]" value="{{ $absensi->id }}"
                                    class="row-check" style="cursor:pointer;">
                            </td>
                            <td class="col-no">{{ $loop->iteration }}</td>
                            <td>
                                <strong>{{ \Carbon\Carbon::parse($absensi->tanggal)->translatedFormat('d F Y') }}</strong>
                                <div class="text-muted-sm">{{ \Carbon\Carbon::parse($absensi->tanggal)->translatedFormat('l') }}</div>
                            </td>
                            <td>{{ $absensi->kelas->nama_kelas ?? '—' }}</td>
                            <td>{{ $absensi->periodeAkademik->nama_periode ?? '—' }}</td>
                            <td class="col-center">
                                <span style="font-size:13px;color:var(--text-main);">
                                    {{ $absensi->statusVerifikasi->nama_status ?? '—' }}
                                </span>
                            </td>
                            <td class="col-center">
                                <a href="{{ route('Absensi.show', $absensi->id) }}"
                                    class="btn btn-sm btn-secondary">
                                    <i class="ri-eye-line"></i> Detail
                                </a>
                                <a href="{{ route('Absensi.isiAbsensi', $absensi->id) }}"
                                    class="btn btn-sm btn-primary">
                                    <i class="ri-edit-line"></i> Edit
                                </a>
                            </td>
                        </tr>
                    @empty
                        <tr>
                            <td colspan="7" class="td-empty">
                                <i class="ri-calendar-check-line" style="font-size:32px;display:block;margin-bottom:8px;"></i>
                                Tidak ada absensi yang menunggu validasi.
                            </td>
                        </tr>
                    @endforelse
                </tbody>
            </table>
        </div>
    </div>
</form>

{{-- Tabel History Sudah Divalidasi --}}
<div class="card" style="margin-top:20px;">
    <div class="table-responsive">
        <table>
            <thead>
                <tr>
                    <th class="col-no">No</th>
                    <th>Tanggal</th>
                    <th>Kelas</th>
                    <th>Periode Akademik</th>
                    <th class="col-center">Status</th>
                    <th class="col-center">Aksi</th>
                </tr>
            </thead>
            <tbody>
                @forelse ($historyList as $absensi)
                    <tr>
                        <td class="col-no">{{ $loop->iteration }}</td>
                        <td>
                            <strong>{{ \Carbon\Carbon::parse($absensi->tanggal)->translatedFormat('d F Y') }}</strong>
                            <div class="text-muted-sm">{{ \Carbon\Carbon::parse($absensi->tanggal)->translatedFormat('l') }}</div>
                        </td>
                        <td>{{ $absensi->kelas->nama_kelas ?? '—' }}</td>
                        <td>{{ $absensi->periodeAkademik->nama_periode ?? '—' }}</td>
                        <td class="col-center">
                            <span style="font-size:13px;color:var(--text-main);">
                                {{ $absensi->statusVerifikasi->nama_status ?? '—' }}
                            </span>
                        </td>
                        <td class="col-center">
                            <a href="{{ route('Absensi.show', $absensi->id) }}"
                                class="btn btn-sm btn-secondary">
                                <i class="ri-eye-line"></i> Detail
                            </a>
                        </td>
                    </tr>
                @empty
                    <tr>
                        <td colspan="6" class="td-empty">
                            <i class="ri-history-line" style="font-size:32px;display:block;margin-bottom:8px;"></i>
                            Belum ada history validasi.
                        </td>
                    </tr>
                @endforelse
            </tbody>
        </table>
    </div>
</div>

{{-- Modal Bulk Validasi --}}
<div class="modal-overlay" id="bulkValidasiModal">
    <div class="modal-box">
        <div class="modal-header">
            <span class="modal-title">Validasi Sekaligus</span>
            <button class="modal-close" onclick="closeBulkValidasiModal()"><i class="ri-close-line"></i></button>
        </div>
        <div class="modal-body">
            <div class="modal-icon" style="background:var(--primary-light);color:var(--primary);">
                <i class="ri-checkbox-circle-line"></i>
            </div>
            <p class="modal-confirm-text">
                Validasi <strong id="bulkCountModal">0</strong> absensi yang dipilih?<br>
                <small style="color:var(--text-muted);">Semua akan berubah status menjadi <strong>Disetujui</strong>.</small>
            </p>
        </div>
        <div class="modal-footer">
            <button type="button" class="btn btn-secondary" onclick="closeBulkValidasiModal()">Batal</button>
            <button type="button" class="btn btn-primary" onclick="submitBulkValidasi()">
                <i class="ri-checkbox-circle-line"></i> Validasi Semua
            </button>
        </div>
    </div>
</div>

{{-- Modal Warning: belum pilih absensi --}}
<div class="modal-overlay" id="warningPilihModal">
    <div class="modal-box">
        <div class="modal-header">
            <span class="modal-title">Perhatian</span>
            <button class="modal-close" onclick="document.getElementById('warningPilihModal').classList.remove('active')"><i class="ri-close-line"></i></button>
        </div>
        <div class="modal-body">
            <div class="modal-icon danger"><i class="ri-error-warning-line"></i></div>
            <p class="modal-confirm-text">Pilih absensi yang ingin divalidasi!</p>
        </div>
        <div class="modal-footer">
            <button type="button" class="btn btn-primary"
                onclick="document.getElementById('warningPilihModal').classList.remove('active')">
                OK
            </button>
        </div>
    </div>
</div>

@endsection

@push('scripts')
<script>
    // ── Checklist Logic ──────────────────────────────────────
    const checkAll   = document.getElementById('check-all');
    const bulkBar    = document.getElementById('bulk-bar');
    const bulkCount  = document.getElementById('bulk-count');

    function updateBulkBar() {
        const checked = document.querySelectorAll('.row-check:checked').length;
        bulkCount.textContent = checked;
        document.getElementById('bulkCountModal').textContent = checked;
        bulkBar.style.display = checked > 0 ? 'block' : 'none';
    }

    checkAll.addEventListener('change', function () {
        document.querySelectorAll('.row-check').forEach(cb => cb.checked = this.checked);
        updateBulkBar();
    });

    document.querySelectorAll('.row-check').forEach(cb => {
        cb.addEventListener('change', function () {
            const all = document.querySelectorAll('.row-check');
            checkAll.checked = [...all].every(c => c.checked);
            updateBulkBar();
        });
    });

    function clearChecks() {
        document.querySelectorAll('.row-check').forEach(cb => cb.checked = false);
        checkAll.checked = false;
        updateBulkBar();
    }

    // ── Bulk Validasi ────────────────────────────────────────
    function showBulkValidasiModal() {
        const checked = document.querySelectorAll('.row-check:checked').length;
        if (checked === 0) {
            document.getElementById('warningPilihModal').classList.add('active');
            return;
        }
        document.getElementById('bulkValidasiModal').classList.add('active');
    }
    function closeBulkValidasiModal() {
        document.getElementById('bulkValidasiModal').classList.remove('active');
    }
    document.getElementById('bulkValidasiModal').addEventListener('click', function(e) {
        if (e.target === this) closeBulkValidasiModal();
    });

    function submitBulkValidasi() {
        document.getElementById('bulk-form').submit();
    }
</script>
@endpush