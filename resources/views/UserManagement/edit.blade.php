@extends('layouts.app')

@section('content')

    <div class="page-header">
        <div>
            <div class="breadcrumb">Admin / <a href="{{ route('UserManagement.index') }}" style="color:var(--primary);">User Management</a> / Edit</div>
            <h2>Edit User</h2>
        </div>
    </div>

    @if (session('success'))
        <div class="alert alert-success" id="success-alert">
            <i class="ri-checkbox-circle-line"></i> {{ session('success') }}
        </div>
        <script>setTimeout(function() { document.getElementById('success-alert').remove(); }, 3000);</script>
    @endif

    <div class="card">
        <form method="POST" action="{{ route('UserManagement.update', $User->id) }}">
            @csrf
            @method('PUT')

            <div class="form-grid">

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

                <div class="form-group">
                    <label class="form-label">Email <span class="required">*</span></label>
                    <input type="email" name="email" class="form-control"
                        placeholder="Masukkan alamat email"
                        value="{{ old('email', $User->email) }}" required>
                    @error('email')<small style="color:#ef4444;">{{ $message }}</small>@enderror
                </div>

                {{-- Role: custom dropdown checklist --}}
                <div class="form-group">
                    <label class="form-label">Role</label>
                    <div class="role-dropdown-wrap" id="roleWrap">
                        <div class="role-trigger" id="roleTrigger" onclick="toggleRoleDropdown()">
                            <span id="roleTriggerText" style="color:#94a3b8;">-- Pilih Role --</span>
                            <i class="ri-arrow-down-s-line" id="roleArrow" style="font-size:16px;color:#94a3b8;transition:.2s;"></i>
                        </div>
                        <div class="role-dropdown-list" id="roleDropdownList">
                            @forelse ($roles as $role)
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

            </div>

            <div class="form-actions">
                <button type="submit" class="btn btn-primary">
                    <i class="ri-save-line"></i> Simpan Perubahan
                </button>
                <a href="{{ route('UserManagement.index') }}" class="btn btn-secondary">
                    <i class="ri-arrow-left-line"></i> Batal
                </a>
            </div>

        </form>
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

    document.addEventListener('click', function(e) {
        const wrap = document.getElementById('roleWrap');
        if (wrap && !wrap.contains(e.target)) {
            document.getElementById('roleDropdownList').classList.remove('open');
            document.getElementById('roleTrigger').classList.remove('open');
            document.getElementById('roleArrow').style.transform = 'rotate(0deg)';
        }
    });

    document.addEventListener('DOMContentLoaded', updateRoleTrigger);
</script>
@endpush