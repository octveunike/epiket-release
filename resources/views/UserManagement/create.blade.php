@extends('layouts.app')

@section('content')

    <div class="page-header">
        <div>
            <div class="breadcrumb">Admin / <a href="{{ route('UserManagement.index') }}" style="color:var(--primary);">User Management</a> / Tambah</div>
            <h2>Tambah User</h2>
        </div>
    </div>

    @if (session('error'))
        <div class="alert alert-danger">
            <i class="ri-error-warning-line"></i> {{ session('error') }}
        </div>
    @endif

    <div class="card">
        <form action="{{ route('UserManagement.store') }}" method="POST" id="createForm">
            @csrf
            <input type="hidden" name="ref_id" id="refId" value="{{ old('ref_id') }}">
            <input type="hidden" name="confirm_change_ketua" id="confirmChangeKetua" value="">
            <input type="hidden" name="confirm_change_wali" id="confirmChangeWali" value="">

            <div class="form-grid">

                {{-- Kategori: menentukan sumber data (Guru/Siswa) & role yang tersedia --}}
                <div class="form-group">
                    <label class="form-label">Kategori <span class="required">*</span></label>
                    <select name="kategori" id="kategori" class="form-control" required onchange="onKategoriChange()">
                        <option value="">-- Pilih --</option>
                        <option value="guru"  {{ old('kategori') === 'guru'  ? 'selected' : '' }}>Guru (Admin / Petugas Piket / Wali Kelas)</option>
                        <option value="siswa" {{ old('kategori') === 'siswa' ? 'selected' : '' }}>Siswa (Ketua Kelas)</option>
                    </select>
                    @error('kategori')<small style="color:#ef4444;">{{ $message }}</small>@enderror
                </div>

                {{-- Pilih data (Guru/Siswa) — selalu tampil di posisi tetap, default "-- Pilih --" --}}
                <div class="form-group">
                    <label class="form-label" id="refLabel">Pilih Data <span class="required">*</span></label>

                    <select class="form-control" id="refPlaceholder" disabled>
                        <option>-- Pilih kategori terlebih dahulu --</option>
                    </select>

                    <select class="form-control" id="guruSelect" style="display:none;" onchange="onRefChange(this)">
                        <option value="">-- Pilih --</option>
                        @foreach ($guruList as $g)
                            <option value="{{ $g->id }}" data-nama="{{ $g->nama_guru }}"
                                {{ (string) old('ref_id') === (string) $g->id ? 'selected' : '' }}>
                                {{ $g->nama_guru }}
                            </option>
                        @endforeach
                    </select>

                    <select class="form-control" id="siswaSelect" style="display:none;" onchange="onRefChange(this)">
                        <option value="">-- Pilih --</option>
                        @foreach ($siswaList as $s)
                            <option value="{{ $s->id }}"
                                data-nama="{{ $s->nama_siswa }}"
                                data-kelas="{{ $s->nama_kelas ?? '' }}"
                                data-has-kelas="{{ $s->kelas_id ? 1 : 0 }}"
                                data-has-ketua="{{ ($s->ketua_kelas_id && (int) $s->ketua_kelas_id !== (int) $s->id) ? 1 : 0 }}"
                                data-current-ketua="{{ $s->current_ketua_nama ?? '' }}"
                                {{ (string) old('ref_id') === (string) $s->id ? 'selected' : '' }}>
                                {{ $s->nama_siswa }}{{ $s->nama_kelas ? ' — '.$s->nama_kelas : ' — (belum ada kelas)' }}
                            </option>
                        @endforeach
                    </select>
                </div>

                <div class="form-group">
                    <label class="form-label">Username <span class="required">*</span></label>
                    <input type="text" name="username" class="form-control"
                        placeholder="Masukkan username (unik)"
                        value="{{ old('username') }}" required>
                    @error('username')<small style="color:#ef4444;">{{ $message }}</small>@enderror
                </div>

                <div class="form-group">
                    <label class="form-label">Email <span class="required">*</span></label>
                    <input type="email" name="email" class="form-control"
                        placeholder="Masukkan alamat email"
                        value="{{ old('email') }}" required>
                    @error('email')<small style="color:#ef4444;">{{ $message }}</small>@enderror
                </div>

                <div class="form-group">
                    <label class="form-label">Password <span class="required">*</span></label>
                    <div style="position:relative;">
                        <input type="password" name="password" id="password" class="form-control"
                            placeholder="Minimal 6 karakter" required style="padding-right:42px;">
                        <button type="button" onclick="togglePassword('password','eyePassword')"
                            style="position:absolute;right:10px;top:50%;transform:translateY(-50%);
                            background:none;border:none;cursor:pointer;color:var(--text-muted);font-size:16px;">
                            <i class="ri-eye-off-line" id="eyePassword"></i>
                        </button>
                    </div>
                    @error('password')<small style="color:#ef4444;">{{ $message }}</small>@enderror
                </div>

                <div class="form-group">
                    <label class="form-label">Konfirmasi Password <span class="required">*</span></label>
                    <div style="position:relative;">
                        <input type="password" name="password_confirmation" id="passwordConfirm" class="form-control"
                            placeholder="Ulangi password" required style="padding-right:42px;">
                        <button type="button" onclick="togglePassword('passwordConfirm','eyeConfirm')"
                            style="position:absolute;right:10px;top:50%;transform:translateY(-50%);
                            background:none;border:none;cursor:pointer;color:var(--text-muted);font-size:16px;">
                            <i class="ri-eye-off-line" id="eyeConfirm"></i>
                        </button>
                    </div>
                </div>

                {{-- Role: custom dropdown checklist, difilter sesuai kategori --}}
                <div class="form-group">
                    <label class="form-label">Role <span class="required">*</span></label>
                    <div class="role-dropdown-wrap" id="roleWrap">
                        <div class="role-trigger {{ $errors->has('role_ids') ? 'has-error' : '' }}" id="roleTrigger" onclick="toggleRoleDropdown()">
                            <span id="roleTriggerText" style="color:#94a3b8;">-- Pilih Role --</span>
                            <i class="ri-arrow-down-s-line" id="roleArrow" style="font-size:16px;color:#94a3b8;transition:.2s;"></i>
                        </div>
                        <div class="role-dropdown-list" id="roleDropdownList">
                            @forelse ($roles as $role)
                                <label class="role-option" data-tipe="{{ (int) $role->id === (int) $ketuaRoleId ? 'siswa' : 'guru' }}">
                                    <input type="checkbox" name="role_ids[]" value="{{ $role->id }}"
                                        class="role-cb"
                                        style="accent-color:var(--primary);"
                                        {{ in_array($role->id, old('role_ids', [])) ? 'checked' : '' }}
                                        onchange="updateRoleTrigger(); toggleWaliBlock();">
                                    <span class="role-option-name">{{ $role->nama_role }}</span>
                                    @if($role->keterangan)
                                        <small class="role-option-desc">{{ $role->keterangan }}</small>
                                    @endif
                                </label>
                            @empty
                                <div style="padding:12px;color:#94a3b8;font-size:13px;text-align:center;">
                                    Belum ada role tersedia
                                </div>
                            @endforelse
                        </div>
                    </div>
                    @error('role_ids')<small style="color:#ef4444;">{{ $message }}</small>@enderror
                </div>

                {{-- Pilih Kelas untuk Wali Kelas (muncul saat role Wali Kelas dicentang).
                     Sebaris dengan Role; Password & Konfirmasi sudah di atas jadi posisinya tetap. --}}
                <div class="form-group" id="waliWrap" style="display:none;">
                    <label class="form-label">Kelas yang Diampu (Wali Kelas) <span class="required">*</span></label>
                    <select name="wali_kelas_id" id="waliKelasSelect" class="form-control">
                        <option value="">-- Pilih Kelas --</option>
                        @foreach ($kelasList as $k)
                            <option value="{{ $k->id }}"
                                data-nama="{{ $k->nama_kelas }}"
                                data-has-wali="{{ $k->wali_kelas_id ? 1 : 0 }}"
                                data-current-wali="{{ $k->current_wali_nama ?? '' }}"
                                {{ (string) old('wali_kelas_id') === (string) $k->id ? 'selected' : '' }}>
                                {{ $k->nama_kelas }}{{ $k->current_wali_nama ? ' — Wali: '.$k->current_wali_nama : '' }}
                            </option>
                        @endforeach
                    </select>
                    <small style="color:var(--text-muted);">Kelas yang sudah punya wali akan meminta konfirmasi penggantian.</small>
                </div>

            </div>

            <div class="form-actions">
                <button type="submit" class="btn btn-primary">
                    <i class="ri-save-line"></i> Simpan
                </button>
                <a href="{{ route('UserManagement.index') }}" class="btn btn-secondary">
                    <i class="ri-arrow-left-line"></i> Batal
                </a>
            </div>

        </form>
    </div>

    {{-- Popup konfirmasi ganti Ketua/Wali Kelas (template confirm-overlay app) --}}
    <div class="confirm-overlay" id="changeConflictModal">
        <div class="confirm-box">
            <div class="confirm-icon">!</div>
            <h3 id="conflictTitle">Ganti?</h3>
            <p id="conflictText"></p>
            <div class="confirm-actions">
                <button type="button" class="btn btn-primary" onclick="confirmChange()">Ya, Ganti</button>
                <button type="button" class="btn btn-secondary" onclick="closeConflict()">Batal</button>
            </div>
        </div>
    </div>

@endsection

@push('styles')
<style>
.role-dropdown-wrap{position:relative;}
.role-trigger{
    display:flex;align-items:center;justify-content:space-between;
    padding:10px 12px;border:1.5px solid #e0e0e0;border-radius:8px;
    background:#fff;cursor:pointer;font-size:13px;font-weight:500;
    color:#37474f;transition:.2s;user-select:none;
}
.role-trigger:hover{border-color:var(--primary);}
.role-trigger.open{border-color:var(--primary);box-shadow:0 0 0 3px rgba(76,175,80,.12);}
.role-trigger.has-error{border-color:#ef4444;box-shadow:0 0 0 3px rgba(239,68,68,.12);}
.role-dropdown-list{
    display:none;position:absolute;top:calc(100% + 4px);left:0;right:0;
    background:#fff;border:1.5px solid #e0e0e0;border-radius:8px;
    box-shadow:0 4px 16px rgba(0,0,0,.1);z-index:200;
    max-height:220px;overflow-y:auto;
}
.role-dropdown-list.open{display:block;}
.role-option{
    display:flex;align-items:center;gap:10px;
    padding:10px 14px;cursor:pointer;transition:.15s;
}
.role-option:hover{background:#f0fdf4;}
.role-option input[type=checkbox]{width:15px;height:15px;flex-shrink:0;cursor:pointer;}
.role-option-name{font-size:13px;font-weight:500;color:#37474f;flex:1;}
.role-option-desc{font-size:11.5px;color:#94a3b8;}
</style>
@endpush

@push('scripts')
<script>
    const KETUA_ROLE_ID = @json($ketuaRoleId ?? null);
    const WALI_ROLE_ID  = @json($waliRoleId ?? null);
    let pendingChange = null; // 'ketua' | 'wali'

    function togglePassword(inputId, iconId) {
        const input = document.getElementById(inputId);
        const icon  = document.getElementById(iconId);
        input.type  = input.type === 'password' ? 'text' : 'password';
        icon.className = input.type === 'text' ? 'ri-eye-line' : 'ri-eye-off-line';
    }

    function toggleRoleDropdown() {
        const list    = document.getElementById('roleDropdownList');
        const trigger = document.getElementById('roleTrigger');
        const arrow   = document.getElementById('roleArrow');
        const isOpen  = list.classList.toggle('open');
        trigger.classList.toggle('open', isOpen);
        arrow.style.transform = isOpen ? 'rotate(180deg)' : 'rotate(0deg)';
    }

    function updateRoleTrigger() {
        const checked     = document.querySelectorAll('input.role-cb:checked');
        const triggerText = document.getElementById('roleTriggerText');
        if (checked.length === 0) {
            triggerText.textContent = '-- Pilih Role --';
            triggerText.style.color = '#94a3b8';
        } else {
            const names = Array.from(checked).map(cb =>
                cb.closest('.role-option').querySelector('.role-option-name').textContent.trim()
            );
            triggerText.textContent = names.join(', ');
            triggerText.style.color = '#37474f';
        }
    }

    function waliChecked() {
        if (!WALI_ROLE_ID) return false;
        const cb = document.querySelector('input.role-cb[value="' + WALI_ROLE_ID + '"]');
        return cb && cb.checked;
    }

    function toggleWaliBlock() {
        document.getElementById('waliWrap').style.display = waliChecked() ? 'block' : 'none';
    }

    /* ---- Kategori: tampilkan sumber data & filter role ---- */
    function onKategoriChange() {
        const kat = document.getElementById('kategori').value;

        document.getElementById('refPlaceholder').style.display = kat ? 'none' : 'block';
        document.getElementById('guruSelect').style.display     = (kat === 'guru')  ? 'block' : 'none';
        document.getElementById('siswaSelect').style.display    = (kat === 'siswa') ? 'block' : 'none';
        document.getElementById('refLabel').firstChild.textContent =
            kat === 'guru' ? 'Pilih Guru ' : kat === 'siswa' ? 'Pilih Siswa ' : 'Pilih Data ';

        if (kat !== 'guru')  document.getElementById('guruSelect').value  = '';
        if (kat !== 'siswa') document.getElementById('siswaSelect').value = '';
        const active = kat === 'guru' ? document.getElementById('guruSelect')
                     : kat === 'siswa' ? document.getElementById('siswaSelect') : null;
        document.getElementById('refId').value = active ? active.value : '';

        document.querySelectorAll('.role-option').forEach(opt => {
            const match = opt.getAttribute('data-tipe') === kat;
            opt.style.display = kat ? (match ? 'flex' : 'none') : 'none';
            if (!match) {
                const cb = opt.querySelector('input.role-cb');
                if (cb) cb.checked = false;
            }
        });
        if (kat === 'siswa' && KETUA_ROLE_ID) {
            const ketuaCb = document.querySelector('input.role-cb[value="' + KETUA_ROLE_ID + '"]');
            if (ketuaCb) ketuaCb.checked = true;
        }
        updateRoleTrigger();
        toggleWaliBlock();
    }

    function onRefChange(sel) {
        document.getElementById('refId').value = sel.value;
    }

    document.addEventListener('click', function(e) {
        const wrap = document.getElementById('roleWrap');
        if (wrap && !wrap.contains(e.target)) {
            document.getElementById('roleDropdownList').classList.remove('open');
            document.getElementById('roleTrigger').classList.remove('open');
            document.getElementById('roleArrow').style.transform = 'rotate(0deg)';
        }
    });

    /* ---- Popup ganti Ketua/Wali ---- */
    function closeConflict() {
        document.getElementById('changeConflictModal').classList.remove('show');
        pendingChange = null;
    }
    function confirmChange() {
        if (submitting) return;
        if (pendingChange === 'ketua') document.getElementById('confirmChangeKetua').value = '1';
        if (pendingChange === 'wali')  document.getElementById('confirmChangeWali').value  = '1';
        lockSubmit();
        document.getElementById('createForm').submit();
    }
    document.getElementById('changeConflictModal').addEventListener('click', function(e) {
        if (e.target === this) closeConflict();
    });
    function showConflict(type, title, html) {
        pendingChange = type;
        document.getElementById('conflictTitle').textContent = title;
        document.getElementById('conflictText').innerHTML = html;
        document.getElementById('changeConflictModal').classList.add('show');
    }

    /* ---- Cegah submit ganda (agar tidak dobel buat user / username "sudah dipakai") ---- */
    let submitting = false;
    function lockSubmit() {
        submitting = true;
        const btn = document.querySelector('#createForm button[type="submit"]');
        if (btn) { btn.disabled = true; btn.innerHTML = '<i class="ri-loader-4-line"></i> Menyimpan...'; }
    }

    /* ---- Submit guard ---- */
    document.getElementById('createForm').addEventListener('submit', function(e) {
        if (submitting) { e.preventDefault(); return; }

        const kat     = document.getElementById('kategori').value;
        const refId   = document.getElementById('refId').value;
        const checked = document.querySelectorAll('input.role-cb:checked');
        const trigger = document.getElementById('roleTrigger');

        if (!kat)   { e.preventDefault(); alert('Pilih kategori (Guru atau Siswa) dulu.'); return; }
        if (!refId) { e.preventDefault(); alert('Pilih data ' + kat + ' yang akan dibuatkan akun.'); return; }
        if (checked.length === 0) {
            e.preventDefault();
            trigger.classList.add('has-error');
            trigger.scrollIntoView({ behavior: 'smooth', block: 'center' });
            alert('Pilih minimal 1 role untuk akun ini.');
            return;
        }

        // Password: wajib, minimal 6 karakter, dan cocok dengan konfirmasi.
        const pw  = document.getElementById('password').value;
        const pwc = document.getElementById('passwordConfirm').value;
        if (pw.length < 6) {
            e.preventDefault();
            document.getElementById('password').focus();
            alert('Password minimal 6 karakter.');
            return;
        }
        if (pw !== pwc) {
            e.preventDefault();
            document.getElementById('passwordConfirm').focus();
            alert('Konfirmasi password tidak cocok dengan password.');
            return;
        }

        // Ketua Kelas (siswa)
        if (kat === 'siswa' && KETUA_ROLE_ID) {
            const opt = document.getElementById('siswaSelect').options[document.getElementById('siswaSelect').selectedIndex];
            if (opt && opt.getAttribute('data-has-kelas') === '0') {
                e.preventDefault();
                alert('Siswa ini belum terdaftar di kelas manapun. Tetapkan kelasnya dulu lewat menu Data Siswa.');
                return;
            }
            if (opt && opt.getAttribute('data-has-ketua') === '1'
                && document.getElementById('confirmChangeKetua').value !== '1') {
                e.preventDefault();
                showConflict('ketua', 'Ganti Ketua Kelas?',
                    'Kelas <strong>' + (opt.getAttribute('data-kelas') || '-') + '</strong> sudah punya Ketua Kelas (<strong>' +
                    (opt.getAttribute('data-current-ketua') || '-') + '</strong>).<br>Ganti dengan <strong>' +
                    (opt.getAttribute('data-nama') || '-') + '</strong>?');
                return;
            }
        }

        // Wali Kelas (guru)
        if (kat === 'guru' && waliChecked()) {
            const wsel = document.getElementById('waliKelasSelect');
            if (!wsel.value) {
                e.preventDefault();
                alert('Pilih kelas yang akan diampu sebagai Wali Kelas.');
                return;
            }
            const wopt = wsel.options[wsel.selectedIndex];
            if (wopt && wopt.getAttribute('data-has-wali') === '1'
                && document.getElementById('confirmChangeWali').value !== '1') {
                e.preventDefault();
                const guruOpt = document.getElementById('guruSelect').options[document.getElementById('guruSelect').selectedIndex];
                showConflict('wali', 'Ganti Wali Kelas?',
                    'Kelas <strong>' + (wopt.getAttribute('data-nama') || '-') + '</strong> sudah punya Wali Kelas (<strong>' +
                    (wopt.getAttribute('data-current-wali') || '-') + '</strong>).<br>Ganti dengan <strong>' +
                    (guruOpt ? guruOpt.getAttribute('data-nama') : '-') + '</strong>?');
                return;
            }
        }

        // Semua field valid → kunci tombol agar tidak dobel submit.
        lockSubmit();
    });

    document.querySelectorAll('input.role-cb').forEach(cb => {
        cb.addEventListener('change', () => {
            if (document.querySelectorAll('input.role-cb:checked').length > 0) {
                document.getElementById('roleTrigger').classList.remove('has-error');
            }
        });
    });

    // State awal (mis. setelah validasi gagal, old() me-restore pilihan).
    onKategoriChange();
    updateRoleTrigger();
    toggleWaliBlock();
</script>
@endpush
