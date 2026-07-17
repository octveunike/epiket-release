@extends('layouts.app')

@section('content')

    <div class="page-header">
        <div>
            @if ($canEditRole)
                <div class="breadcrumb">Admin / <a href="{{ route('UserManagement.index') }}" style="color:var(--primary);">User Management</a> / Edit</div>
                <h2>Edit User</h2>
            @else
                <div class="breadcrumb">Profil / Edit</div>
                <h2>Edit Profil Saya</h2>
            @endif
        </div>
    </div>

    @if (session('success'))
        <div class="alert alert-success" id="success-alert">
            <i class="ri-checkbox-circle-line"></i> {{ session('success') }}
        </div>
        <script>setTimeout(function() { document.getElementById('success-alert').remove(); }, 3000);</script>
    @endif

    @if (session('error'))
        <div class="alert alert-danger">
            <i class="ri-error-warning-line"></i> {{ session('error') }}
        </div>
    @endif

    <div class="card">
        <form method="POST" action="{{ route('UserManagement.update', $User->id) }}" id="editForm">
            @csrf
            @method('PUT')
            <input type="hidden" name="confirm_change_ketua" id="confirmChangeKetua" value="">
            <input type="hidden" name="confirm_change_wali" id="confirmChangeWali" value="">

            <div class="form-grid">

                @if ($canEditRole)
                {{-- Kategori & data terkait: tetap (tidak bisa diubah), menentukan role yang boleh dipilih.
                     Guru -> Admin/Petugas Piket/Wali Kelas; Siswa -> Ketua Kelas saja. --}}
                <div class="form-group">
                    <label class="form-label">Kategori</label>
                    <select class="form-control" disabled>
                        <option>{{ $kategori }}</option>
                    </select>
                </div>

                <div class="form-group">
                    <label class="form-label">Data Terkait</label>
                    <input type="text" class="form-control" value="{{ $dataTerkait }}" disabled>
                </div>
                @endif

                <div class="form-group">
                    <label class="form-label">Nama Lengkap <span class="required">*</span></label>
                    <input type="text" name="nama" class="form-control"
                        placeholder="Masukkan nama lengkap"
                        value="{{ old('nama', $User->nama) }}" required>
                    @error('nama')<small style="color:#ef4444;">{{ $message }}</small>@enderror
                </div>

                <div class="form-group">
                    <label class="form-label">Username <span class="required">*</span></label>
                    <input type="text" name="username" class="form-control"
                        placeholder="Masukkan username"
                        value="{{ old('username', $User->username) }}" required>
                    @error('username')<small style="color:#ef4444;">{{ $message }}</small>@enderror
                </div>

                <div class="form-group" style="grid-column:1 / -1;">
                    <label class="form-label">Email <span class="required">*</span></label>
                    <input type="email" name="email" class="form-control"
                        placeholder="Masukkan alamat email"
                        value="{{ old('email', $User->email) }}" required>
                    @error('email')<small style="color:#ef4444;">{{ $message }}</small>@enderror
                </div>

                <div class="form-group">
                    <label class="form-label">
                        Password Baru
                        <small style="color:var(--text-muted);font-weight:400;">(kosongkan jika tidak diubah)</small>
                    </label>
                    <div style="position:relative;">
                        <input type="password" name="password" id="password" class="form-control"
                            placeholder="Minimal 6 karakter" style="padding-right:42px;">
                        <button type="button" onclick="togglePassword('password','eyePassword')"
                            style="position:absolute;right:10px;top:50%;transform:translateY(-50%);
                            background:none;border:none;cursor:pointer;color:var(--text-muted);font-size:16px;">
                            <i class="ri-eye-off-line" id="eyePassword"></i>
                        </button>
                    </div>
                    @error('password')<small style="color:#ef4444;">{{ $message }}</small>@enderror
                </div>

                <div class="form-group">
                    <label class="form-label">Konfirmasi Password Baru</label>
                    <div style="position:relative;">
                        <input type="password" name="password_confirmation" id="passwordConfirm" class="form-control"
                            placeholder="Ulangi password baru" style="padding-right:42px;">
                        <button type="button" onclick="togglePassword('passwordConfirm','eyeConfirm')"
                            style="position:absolute;right:10px;top:50%;transform:translateY(-50%);
                            background:none;border:none;cursor:pointer;color:var(--text-muted);font-size:16px;">
                            <i class="ri-eye-off-line" id="eyeConfirm"></i>
                        </button>
                    </div>
                </div>

                @if ($canEditRole)
                {{-- Role di bawah (sejajar dgn Tambah User); Ketua/Wali picker sebaris dengan Role. --}}
                <div class="form-group">
                    <label class="form-label">Role <span class="required">*</span></label>
                    <div class="role-dropdown-wrap" id="roleWrap">
                        <div class="role-trigger {{ $errors->has('role_ids') ? 'has-error' : '' }}" id="roleTrigger" onclick="toggleRoleDropdown()">
                            <span id="roleTriggerText" style="color:#94a3b8;">-- Pilih Role --</span>
                            <i class="ri-arrow-down-s-line" id="roleArrow" style="font-size:16px;color:#94a3b8;transition:.2s;"></i>
                        </div>
                        <div class="role-dropdown-list" id="roleDropdownList">
                            @forelse ($roles->whereIn('id', $allowedRoleIds) as $role)
                                <label class="role-option">
                                    <input type="checkbox" name="role_ids[]" value="{{ $role->id }}"
                                        class="role-cb"
                                        style="accent-color:var(--primary);"
                                        {{ in_array($role->id, old('role_ids', $activeRoles)) ? 'checked' : '' }}
                                        onchange="updateRoleTrigger()">
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

                @php $ketuaOk = ($ketuaInfo['is_siswa'] && $ketuaInfo['has_kelas']); @endphp
                <div class="form-group" id="ketuaKelasBlock" style="display:none;">
                    <label class="form-label">Penetapan Ketua Kelas</label>
                    <div class="account-warning" style="display:flex; align-items:flex-start; gap:8px;
                        {{ $ketuaOk ? 'background:#f0fdf4; border-color:var(--primary); color:var(--text-main);' : '' }}">
                        <i class="{{ $ketuaOk ? 'ri-team-line' : 'ri-error-warning-line' }}" style="margin-top:2px;"></i>
                        <span>
                            @if (!$ketuaInfo['is_siswa'])
                                User ini belum terhubung ke data siswa, jadi <strong>tidak bisa</strong> dijadikan Ketua Kelas.
                            @elseif (!$ketuaInfo['has_kelas'])
                                Siswa ini belum terdaftar di kelas manapun. Tetapkan kelasnya dulu lewat menu <strong>Data Siswa</strong>.
                            @else
                                Akan ditetapkan sebagai <strong>Ketua Kelas {{ $ketuaInfo['nama_kelas'] }}</strong>.
                                @if ($ketuaInfo['current_ketua_nama'] && !$ketuaInfo['is_current_ketua'])
                                    <br>Kelas ini sudah punya ketua: <strong>{{ $ketuaInfo['current_ketua_nama'] }}</strong> — Anda akan diminta konfirmasi penggantian saat menyimpan.
                                @endif
                            @endif
                        </span>
                    </div>
                </div>

                <div class="form-group" id="waliKelasBlock" style="display:none;">
                    <label class="form-label">Kelas yang Diampu (Wali Kelas) <span class="required">*</span></label>
                    @if (!$waliInfo['is_guru'])
                        <div class="account-warning" style="display:flex; align-items:flex-start; gap:8px;">
                            <i class="ri-error-warning-line" style="margin-top:2px;"></i>
                            <span>User ini belum terhubung ke data guru, jadi <strong>tidak bisa</strong> dijadikan Wali Kelas.</span>
                        </div>
                    @else
                        <select name="wali_kelas_id" id="waliKelasSelect" class="form-control">
                            <option value="">-- Pilih Kelas --</option>
                            @foreach ($kelasList as $k)
                                <option value="{{ $k->id }}"
                                    data-nama="{{ $k->nama_kelas }}"
                                    data-has-wali="{{ ($k->wali_kelas_id && (int) $k->id !== (int) ($waliInfo['current_kelas_id'] ?? 0)) ? 1 : 0 }}"
                                    data-current-wali="{{ $k->current_wali_nama ?? '' }}"
                                    {{ (string) old('wali_kelas_id', $waliInfo['current_kelas_id']) === (string) $k->id ? 'selected' : '' }}>
                                    {{ $k->nama_kelas }}{{ $k->current_wali_nama ? ' — Wali: '.$k->current_wali_nama : '' }}
                                </option>
                            @endforeach
                        </select>
                        <small style="color:var(--text-muted);">Kelas yang sudah punya wali akan meminta konfirmasi penggantian.</small>
                    @endif
                </div>
                @endif

            </div>

            <div class="form-actions">
                <button type="submit" class="btn btn-primary">
                    <i class="ri-save-line"></i> Simpan Perubahan
                </button>
                <a href="{{ $canEditRole ? route('UserManagement.index') : route('admin.index') }}" class="btn btn-secondary">
                    <i class="ri-arrow-left-line"></i> Batal
                </a>
            </div>

        </form>
    </div>

    {{-- Modal sukses ubah password (style mengikuti modal konfirmasi di index Guru) --}}
    @if (session('password_changed'))
        <div class="confirm-overlay show" id="pwSuccessModal">
            <div class="confirm-box">
                <div class="confirm-icon" style="border-color:var(--primary);color:var(--primary);">
                    <i class="ri-check-line"></i>
                </div>
                <h3>Password Berhasil Diubah</h3>
                <p>Password akun Anda sudah berhasil diperbarui.</p>
                <div class="confirm-actions">
                    <a href="{{ route('admin.index') }}" class="btn btn-primary">OK</a>
                </div>
            </div>
        </div>
    @endif

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
        if (!list || !trigger || !arrow) return;
        const isOpen  = list.classList.toggle('open');
        trigger.classList.toggle('open', isOpen);
        arrow.style.transform = isOpen ? 'rotate(180deg)' : 'rotate(0deg)';
    }

    function updateRoleTrigger() {
        const triggerText = document.getElementById('roleTriggerText');
        if (!triggerText) return;
        const checked = document.querySelectorAll('input.role-cb:checked');
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

    document.addEventListener('click', function(e) {
        const wrap = document.getElementById('roleWrap');
        if (wrap && !wrap.contains(e.target)) {
            document.getElementById('roleDropdownList').classList.remove('open');
            document.getElementById('roleTrigger').classList.remove('open');
            document.getElementById('roleArrow').style.transform = 'rotate(0deg)';
        }
    });

    // Sinkronkan label trigger dengan checkbox yang sudah ter-check (data role user).
    // Dipanggil langsung karena script ini sudah berada di bawah DOM (di-render lewat
    // stack scripts), jadi event DOMContentLoaded mungkin sudah lewat dan listener
    // tidak akan jalan.
    updateRoleTrigger();

    // Cegah submit ganda + validasi role & password sebelum simpan.
    const editForm = document.getElementById('editForm');
    let submitting = false;
    function lockSubmit() {
        submitting = true;
        const btn = editForm ? editForm.querySelector('button[type="submit"]') : null;
        if (btn) { btn.disabled = true; btn.innerHTML = '<i class="ri-loader-4-line"></i> Menyimpan...'; }
    }

    if (editForm) {
        editForm.addEventListener('submit', function(e) {
            if (submitting) { e.preventDefault(); return; }

            // Role wajib minimal 1 (hanya bila dropdown role ditampilkan / user Admin).
            const trigger = document.getElementById('roleTrigger');
            if (trigger) {
                const checked = document.querySelectorAll('input.role-cb:checked');
                if (checked.length === 0) {
                    e.preventDefault();
                    trigger.classList.add('has-error');
                    trigger.scrollIntoView({ behavior: 'smooth', block: 'center' });
                    alert('Pilih minimal 1 role untuk akun ini.');
                    return;
                }
            }

            // Password baru opsional; jika diisi wajib min 6 karakter & cocok konfirmasi.
            const pw  = document.getElementById('password').value;
            const pwc = document.getElementById('passwordConfirm').value;
            if (pw.length > 0 && pw.length < 6) {
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

            lockSubmit();
        });

        document.querySelectorAll('input.role-cb').forEach(cb => {
            cb.addEventListener('change', () => {
                const t = document.getElementById('roleTrigger');
                if (t && document.querySelectorAll('input.role-cb:checked').length > 0) {
                    t.classList.remove('has-error');
                }
            });
        });
    }

    // Modal sukses ubah password — backdrop click juga arahkan ke dashboard
    const pwSuccessModal = document.getElementById('pwSuccessModal');
    if (pwSuccessModal) {
        pwSuccessModal.addEventListener('click', function(e) {
            if (e.target === this) window.location.href = "{{ route('admin.index') }}";
        });
    }

    /* ---- Ketua & Wali Kelas: blok kondisional + konfirmasi ganti ---- */
    const KETUA_ROLE_ID = @json($ketuaRoleId ?? null);
    const WALI_ROLE_ID  = @json($waliRoleId ?? null);
    let pendingChange = null; // 'ketua' | 'wali'

    function roleChecked(roleId) {
        if (!roleId) return false;
        const cb = document.querySelector('input.role-cb[value="' + roleId + '"]');
        return cb && cb.checked;
    }
    function toggleKetuaBlock() {
        const block = document.getElementById('ketuaKelasBlock');
        if (block) block.style.display = roleChecked(KETUA_ROLE_ID) ? 'block' : 'none';
    }
    function toggleWaliBlock() {
        const block = document.getElementById('waliKelasBlock');
        if (block) block.style.display = roleChecked(WALI_ROLE_ID) ? 'block' : 'none';
    }

    document.querySelectorAll('input.role-cb').forEach(cb => {
        cb.addEventListener('change', function () { toggleKetuaBlock(); toggleWaliBlock(); });
    });
    toggleKetuaBlock();
    toggleWaliBlock();

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
        document.getElementById('editForm').submit();
    }
    function showConflict(type, title, html) {
        pendingChange = type;
        document.getElementById('conflictTitle').textContent = title;
        document.getElementById('conflictText').innerHTML = html;
        document.getElementById('changeConflictModal').classList.add('show');
    }
    const changeConflictModal = document.getElementById('changeConflictModal');
    if (changeConflictModal) {
        changeConflictModal.addEventListener('click', function(e) {
            if (e.target === this) closeConflict();
        });
    }
    @if (session('ketua_conflict'))
    (function () {
        var c = @json(session('ketua_conflict'));
        showConflict('ketua', 'Ganti Ketua Kelas?',
            'Kelas <strong>' + c.nama_kelas + '</strong> sudah punya Ketua Kelas (<strong>' +
            c.current_nama + '</strong>).<br>Ganti dengan <strong>' + c.new_nama + '</strong>?');
    })();
    @endif
    @if (session('wali_conflict'))
    (function () {
        var c = @json(session('wali_conflict'));
        showConflict('wali', 'Ganti Wali Kelas?',
            'Kelas <strong>' + c.nama_kelas + '</strong> sudah punya Wali Kelas (<strong>' +
            c.current_nama + '</strong>).<br>Ganti dengan guru ini?');
    })();
    @endif
</script>
@endpush