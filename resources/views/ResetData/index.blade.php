@extends('layouts.app')

@section('title', 'Reset Data')

@section('content')

    <div class="page-header">
        <div>
            <div class="breadcrumb">Admin / Sistem / Reset Data</div>
            <h2>Reset Data Aplikasi</h2>
        </div>
    </div>

    @if (session('error'))
        <div class="alert alert-danger">
            <i class="ri-error-warning-line"></i> {{ session('error') }}
        </div>
    @endif

    {{-- Backup dulu sebelum reset --}}
    <div class="card">
        <div class="card-title" style="margin-bottom:6px;">
            <i class="ri-download-2-line" style="color:var(--primary);"></i> Backup Data (unduh dulu sebelum reset)
        </div>
        <p style="font-size:13px; color:var(--text-muted); margin-bottom:14px;">
            Sangat disarankan mengunduh data penting sebagai cadangan sebelum melakukan reset.
            File Excel yang diunduh memakai format template import, sehingga bisa di-import kembali nanti bila perlu.
        </p>
        <div style="display:flex; gap:10px; flex-wrap:wrap;">
            <a href="{{ route('Guru.export') }}" class="btn btn-secondary">
                <i class="ri-file-excel-2-line"></i> Data Guru
            </a>
            <a href="{{ route('Siswa.export') }}" class="btn btn-secondary">
                <i class="ri-file-excel-2-line"></i> Data Siswa
            </a>
            <a href="{{ route('Staff.export') }}" class="btn btn-secondary">
                <i class="ri-file-excel-2-line"></i> Data Staf
            </a>
            <a href="{{ route('Laporan.export') }}" class="btn btn-secondary">
                <i class="ri-file-excel-2-line"></i> Laporan Absensi
            </a>
        </div>
    </div>

    <div class="card" style="border:1.5px solid #fecaca;">

        <div class="alert alert-danger" style="margin-bottom:18px;">
            <i class="ri-alarm-warning-line"></i>
            <span><strong>PERHATIAN:</strong> Tindakan ini menghapus <strong>SELURUH data</strong> aplikasi dan
            mengembalikannya ke kondisi awal. Tindakan ini <strong>TIDAK dapat dibatalkan</strong>.</span>
        </div>

        <div class="reset-grid">
            <div style="border:1px solid #fecaca; border-radius:10px; padding:14px; background:#fef2f2;">
                <div style="font-weight:600; color:#b91c1c; margin-bottom:8px;">
                    <i class="ri-delete-bin-line"></i> Data yang akan DIHAPUS
                </div>
                <ul style="margin:0; padding-left:18px; font-size:13px; color:#7f1d1d; line-height:1.9;">
                    <li>Data Guru, Siswa, Staf</li>
                    <li>Data Absensi &amp; detailnya</li>
                    <li>Dispensasi &amp; detailnya</li>
                    <li>Keterlambatan</li>
                    <li>Daftar Tamu</li>
                    <li>Organisasi &amp; anggotanya</li>
                    <li>User tambahan yang Anda buat</li>
                </ul>
            </div>
            <div style="border:1px solid #bbf7d0; border-radius:10px; padding:14px; background:#f0fdf4;">
                <div style="font-weight:600; color:#15803d; margin-bottom:8px;">
                    <i class="ri-shield-check-line"></i> Data yang DIPERTAHANKAN
                </div>
                <ul style="margin:0; padding-left:18px; font-size:13px; color:#14532d; line-height:1.9;">
                    <li>Role (Admin, Petugas Piket, dll.)</li>
                    <li>Akun bawaan (admin, dll.)</li>
                    <li>Data Kelas (kosong, tanpa wali/ketua)</li>
                    <li>Status Absensi / Siswa / Validasi</li>
                    <li>Jam Absensi</li>
                    <li>Periode Akademik</li>
                </ul>
            </div>
        </div>

        <div style="font-size:12.5px; color:var(--text-muted); margin:16px 0;">
            <i class="ri-information-line"></i> Setelah reset, seluruh password akun kembali ke bawaan dan Anda akan
            diminta login ulang (akun: <strong>admin / admin</strong>).
        </div>

        <form method="POST" action="{{ route('ResetData.reset') }}" id="resetForm">
            @csrf
            <div class="form-grid">
                <div class="form-group">
                    <label class="form-label">Ketik <strong>RESET</strong> untuk konfirmasi <span class="required">*</span></label>
                    <input type="text" name="konfirmasi" id="konfirmasi" class="form-control"
                        placeholder="RESET" autocomplete="off" required>
                    @error('konfirmasi')<small style="color:#ef4444;">{{ $message }}</small>@enderror
                </div>
                <div class="form-group">
                    <label class="form-label">Password Anda <span class="required">*</span></label>
                    <input type="password" name="password" id="password" class="form-control"
                        placeholder="Password akun Anda" required>
                    @error('password')<small style="color:#ef4444;">{{ $message }}</small>@enderror
                </div>
            </div>

            <div class="form-actions">
                <button type="button" id="resetBtn" class="btn btn-danger" disabled onclick="openResetConfirm()">
                    <i class="ri-refresh-line"></i> Reset Data Sekarang
                </button>
                <a href="{{ route('admin.index') }}" class="btn btn-secondary">
                    <i class="ri-arrow-left-line"></i> Batal
                </a>
            </div>
        </form>
    </div>

    {{-- Konfirmasi akhir --}}
    <div class="confirm-overlay" id="resetModal">
        <div class="confirm-box">
            <div class="confirm-icon">!</div>
            <h3>Reset Semua Data?</h3>
            <p>Seluruh data akan dihapus permanen dan dikembalikan ke kondisi awal.<br>
               Tindakan ini <strong>tidak dapat dibatalkan</strong>.</p>
            <div class="confirm-actions">
                <button type="button" class="btn btn-danger" onclick="submitReset()">Ya, Reset Sekarang</button>
                <button type="button" class="btn btn-secondary" onclick="closeResetConfirm()">Batal</button>
            </div>
        </div>
    </div>

@endsection

@push('styles')
<style>
.reset-grid{ display:grid; grid-template-columns:1fr 1fr; gap:16px; }
@media (max-width:640px){ .reset-grid{ grid-template-columns:1fr; } }
</style>
@endpush

@push('scripts')
<script>
    const konfInput = document.getElementById('konfirmasi');
    const resetBtn  = document.getElementById('resetBtn');
    let resetting = false;

    konfInput.addEventListener('input', function () {
        resetBtn.disabled = (this.value.trim() !== 'RESET');
    });

    function openResetConfirm() {
        if (konfInput.value.trim() !== 'RESET') return;
        if (!document.getElementById('password').value) {
            alert('Masukkan password Anda untuk konfirmasi.');
            document.getElementById('password').focus();
            return;
        }
        document.getElementById('resetModal').classList.add('show');
    }
    function closeResetConfirm() {
        document.getElementById('resetModal').classList.remove('show');
    }
    function submitReset() {
        if (resetting) return;
        resetting = true;
        const btn = document.querySelector('#resetModal .btn-danger');
        if (btn) { btn.disabled = true; btn.innerHTML = '<i class="ri-loader-4-line"></i> Mereset...'; }
        document.getElementById('resetForm').submit();
    }
    document.getElementById('resetModal').addEventListener('click', function (e) {
        if (e.target === this) closeResetConfirm();
    });
</script>
@endpush
