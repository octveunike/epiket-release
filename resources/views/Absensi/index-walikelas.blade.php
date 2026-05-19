@extends('layouts.app')

@section('content')

@php
    $u = auth()->user();
    $breadcrumbRole = $u->hasRole('Admin')
        ? 'Admin'
        : ($u->hasRole('Petugas Piket') ? 'Petugas Piket' : 'Wali Kelas');
@endphp

<div class="page-header">
    <div>
        <div class="breadcrumb">{{ $breadcrumbRole }} / <span class="breadcrumb-link">Validasi Absensi</span></div>
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
    <form method="GET" action="{{ route('Absensi.validasi.index') }}" style="display:flex;align-items:flex-end;gap:12px;flex-wrap:wrap;">
        <div class="form-group" style="margin-bottom:0;min-width:160px;">
            <label class="form-label">Dari Tanggal</label>
            <input type="date" name="dari" class="form-control" value="{{ request('dari') }}">
        </div>
        <div class="form-group" style="margin-bottom:0;min-width:160px;">
            <label class="form-label">Sampai Tanggal</label>
            <input type="date" name="sampai" class="form-control" value="{{ request('sampai') }}">
        </div>
        <div class="form-group" style="margin-bottom:0;min-width:180px;">
            <label class="form-label">Kelas</label>
            @if ($scopeKelas)
                <input type="hidden" name="kelas_id" value="{{ $scopeKelas->id }}">
                <input type="text" class="form-control" value="{{ $scopeKelas->nama_kelas }}" readonly tabindex="-1">
            @else
                <select name="kelas_id" class="form-control">
                    <option value="">Semua Kelas</option>
                    @foreach ($kelasList as $k)
                        <option value="{{ $k->id }}" {{ request('kelas_id') == $k->id ? 'selected' : '' }}>
                            {{ $k->nama_kelas }}
                        </option>
                    @endforeach
                </select>
            @endif
        </div>
        <div class="form-group" style="margin-bottom:0;min-width:180px;">
            <label class="form-label">Periode Akademik</label>
            <select name="periode_akademik_id" class="form-control">
                @foreach ($periodeList as $p)
                    <option value="{{ $p->id }}" {{ $periodeId == $p->id ? 'selected' : '' }}>
                        {{ $p->nama_periode }}
                    </option>
                @endforeach
            </select>
        </div>
        <button type="submit" class="btn btn-primary" style="margin-bottom:0;">
            <i class="ri-filter-line"></i> Filter
        </button>
        <a href="{{ route('Absensi.validasi.index') }}" class="btn btn-secondary" style="margin-bottom:0;">
            <i class="ri-refresh-line"></i> Reset
        </a>
    </form>
</div>

{{-- Info jumlah dipilih (simple, tanpa kotak hijau) --}}
<div id="bulk-bar" style="display:none;margin-bottom:10px;font-size:13px;color:var(--text-muted);">
    <i class="ri-checkbox-circle-line"></i>
    <span><strong id="bulk-count">0</strong> absensi dipilih</span>
</div>

<form id="bulk-form" method="POST" action="{{ route('Absensi.validasi.bulkValidasi') }}">
    @csrf
    <input type="hidden" name="bulk_action" id="bulk-action-input" value="validasi">
    <div class="card">
        <div class="table-responsive">
            <table id="tableValidasi" class="dt-table">
                <thead>
                    <tr>
                        <th style="width:36px;text-align:center;">
                            <input type="checkbox" id="check-all" style="cursor:pointer;">
                        </th>
                        <th class="col-no">No</th>
                        <th>Tanggal</th>
                        <th>Kelas</th>
                        <th class="col-center">Status</th>
                        <th class="col-center">Aksi</th>
                        <th>Update Terakhir</th>
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
                            <td><strong>{{ \Carbon\Carbon::parse($absensi->tanggal)->locale('id')->translatedFormat('l, d F Y') }}</strong></td>
                            <td>{{ $absensi->kelas->nama_kelas ?? '—' }}</td>
                            <td class="col-center">
                                <span style="font-size:13px;color:var(--text-main);">
                                    {{ $absensi->statusValidasi->nama_status ?? '—' }}
                                </span>
                            </td>
                            <td class="col-center">
                                <a href="{{ route('Absensi.show', $absensi->id) }}"
                                    class="btn btn-sm btn-info">
                                    <i class="ri-eye-line"></i> Detail Absensi
                                </a>
                            </td>
                            <td>{{ $absensi->userUpdate->nama ?? $absensi->userInput->nama ?? 'Auto' }}</td>
                        </tr>
                    @empty
                    @endforelse
                </tbody>
            </table>
        </div>
    </div>
</form>

{{-- Tabel History Sudah Divalidasi (Disetujui) --}}
<h3 style="margin-top:24px;margin-bottom:12px;font-size:16px;color:var(--text-main);">
    <i class="ri-history-line"></i> History Validasi (Disetujui)
</h3>
<div class="card">
    <div class="table-responsive">
        <table id="tableValidasiHistory" class="dt-table">
            <thead>
                <tr>
                    <th class="col-no">No</th>
                    <th>Tanggal Absensi</th>
                    <th>Kelas</th>
                    <th>Tanggal Validasi</th>
                    <th class="col-center">Aksi</th>
                    <th>Update Terakhir</th>
                </tr>
            </thead>
            <tbody>
                @forelse ($historyList as $absensi)
                    <tr>
                        <td class="col-no">{{ $loop->iteration }}</td>
                        <td><strong>{{ \Carbon\Carbon::parse($absensi->tanggal)->locale('id')->translatedFormat('l, d F Y') }}</strong></td>
                        <td>{{ $absensi->kelas->nama_kelas ?? '—' }}</td>
                        <td>
                            @if ($absensi->tanggal_update)
                                {{ \Carbon\Carbon::parse($absensi->tanggal_update)->translatedFormat('d F Y, H:i') }}
                            @else
                                —
                            @endif
                        </td>
                        <td class="col-center">
                            <a href="{{ route('Absensi.show', $absensi->id) }}"
                                class="btn btn-sm btn-secondary">
                                <i class="ri-eye-line"></i> Detail Absensi
                            </a>
                        </td>
                        <td>{{ $absensi->userUpdate->nama ?? $absensi->userInput->nama ?? 'Auto' }}</td>
                    </tr>
                @empty
                @endforelse
            </tbody>
        </table>
    </div>
</div>

{{-- Modal Bulk Validasi --}}
<div class="confirm-overlay" id="bulkValidasiModal">
    <div class="confirm-box">
        <div class="confirm-icon" style="background:#e0f2fe;color:#0ea5e9;">
            <i class="ri-checkbox-circle-line" style="font-size:32px;line-height:64px;"></i>
        </div>
        <h3>Tindakan untuk Absensi Terpilih?</h3>
        <p>Validasi jika sudah sesuai atau ajukan revisi kepada ketua kelas.<br></p>
        <div class="confirm-actions">
            <button type="button" class="btn btn-primary" onclick="submitBulkAction('validasi')">Ya, Validasi</button>
            <button type="button" class="btn btn-warning" onclick="submitBulkAction('revisi')">Perlu Revisi</button>
        </div>
    </div>
</div>

{{-- Modal Warning: belum pilih absensi --}}
<div class="confirm-overlay" id="warningPilihModal">
    <div class="confirm-box">
        <div class="confirm-icon" style="background:#fee2e2;color:#ef4444;">!</div>
        <h3>Perhatian</h3>
        <p>Pilih absensi yang ingin divalidasi terlebih dahulu!</p>
        <div class="confirm-actions">
            <button type="button" class="btn btn-secondary" onclick="closeWarningModal()">Mengerti</button>
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
            document.getElementById('warningPilihModal').classList.add('show');
            return;
        }
        document.getElementById('bulkValidasiModal').classList.add('show');
    }
    function closeBulkValidasiModal() {
        document.getElementById('bulkValidasiModal').classList.remove('show');
    }
    document.getElementById('bulkValidasiModal').addEventListener('click', function(e) {
        if (e.target === this) closeBulkValidasiModal();
    });

    function closeWarningModal() {
        document.getElementById('warningPilihModal').classList.remove('show');
    }
    document.getElementById('warningPilihModal').addEventListener('click', function(e) {
        if (e.target === this) closeWarningModal();
    });

    function submitBulkAction(action) {
        document.getElementById('bulk-action-input').value = action;
        document.getElementById('bulk-form').submit();
    }
</script>
@endpush