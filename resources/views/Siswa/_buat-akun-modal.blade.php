{{-- Modal: Buat Akun User inline (dipakai di Siswa create/edit) --}}
<div class="modal-overlay" id="buatAkunModal">
    <div class="modal-box">
        <div class="modal-header">
            <span class="modal-title">
                <i class="ri-user-add-line"></i> Buat Akun User
            </span>
            <button type="button" class="modal-close" onclick="closeBuatAkunModal()">
                <i class="ri-close-line"></i>
            </button>
        </div>
        <div class="modal-body">
            <div id="buatAkunError" class="alert alert-danger" style="display:none; margin-bottom:12px;"></div>

            <div class="form-group">
                <label class="form-label">Nama <span class="required">*</span></label>
                <input type="text" id="ba-nama" class="form-control" placeholder="Nama lengkap">
                <small class="ba-err" data-for="nama" style="color:#ef4444;"></small>
            </div>
            <div class="form-group">
                <label class="form-label">Role</label>
                <select class="form-control">
                    <option selected>Ketua Kelas</option>
                </select>
            </div>
            <div class="form-group">
                <label class="form-label">Username <span class="required">*</span></label>
                <input type="text" id="ba-username" class="form-control" placeholder="Dipakai untuk login" autocomplete="off">
                <small class="ba-err" data-for="username" style="color:#ef4444;"></small>
            </div>
            <div class="form-group">
                <label class="form-label">Email <span class="required">*</span></label>
                <input type="email" id="ba-email" class="form-control" placeholder="nama@email.com">
                <small class="ba-err" data-for="email" style="color:#ef4444;"></small>
            </div>
            <div class="form-group">
                <label class="form-label">Password <span class="required">*</span></label>
                <input type="password" id="ba-password" class="form-control" placeholder="Minimal 6 karakter" autocomplete="new-password">
                <small class="ba-err" data-for="password" style="color:#ef4444;"></small>
            </div>
        </div>
        <div class="modal-footer">
            <button type="button" class="btn btn-secondary" onclick="closeBuatAkunModal()">Batal</button>
            <button type="button" class="btn btn-primary" id="buatAkunSubmit" onclick="submitBuatAkun()">
                <i class="ri-save-line"></i> Simpan Akun
            </button>
        </div>
    </div>
</div>

@push('scripts')
<script>
(function () {
    const modal      = document.getElementById('buatAkunModal');
    const btn        = document.getElementById('buatAkunSubmit');
    const errBox     = document.getElementById('buatAkunError');
    const csrf       = '{{ csrf_token() }}';
    const endpoint   = '{{ route("UserManagement.storeInline") }}';
    const select     = document.getElementById('siswa-user-select');
    const namaSiswa  = document.querySelector('input[name="nama_siswa"]');
    const namaInput  = document.getElementById('ba-nama');

    window.openBuatAkunModal = function () {
        clearBuatAkunErrors();
        ['ba-username','ba-email','ba-password'].forEach(id => {
            document.getElementById(id).value = '';
        });
        // Pre-fill Nama dari kolom Nama Siswa kalau sudah terisi
        namaInput.value = (namaSiswa && namaSiswa.value) ? namaSiswa.value : '';
        modal.classList.add('active');
        namaInput.focus();
    };

    window.closeBuatAkunModal = function () {
        modal.classList.remove('active');
    };

    modal.addEventListener('click', function (e) {
        if (e.target === modal) closeBuatAkunModal();
    });

    function clearBuatAkunErrors() {
        errBox.style.display = 'none';
        errBox.textContent = '';
        document.querySelectorAll('.ba-err').forEach(el => el.textContent = '');
    }

    window.submitBuatAkun = async function () {
        clearBuatAkunErrors();
        btn.disabled = true;

        const payload = {
            nama:     document.getElementById('ba-nama').value.trim(),
            username: document.getElementById('ba-username').value.trim(),
            email:    document.getElementById('ba-email').value.trim(),
            password: document.getElementById('ba-password').value,
        };

        try {
            const res = await fetch(endpoint, {
                method: 'POST',
                headers: {
                    'Content-Type': 'application/json',
                    'Accept': 'application/json',
                    'X-CSRF-TOKEN': csrf,
                    'X-Requested-With': 'XMLHttpRequest',
                },
                body: JSON.stringify(payload),
            });

            if (res.status === 422) {
                const data = await res.json();
                Object.entries(data.errors || {}).forEach(([field, msgs]) => {
                    const el = document.querySelector(`.ba-err[data-for="${field}"]`);
                    if (el) el.textContent = msgs[0];
                });
                return;
            }

            if (!res.ok) {
                const data = await res.json().catch(() => ({}));
                errBox.textContent = data.message || `Gagal membuat akun (HTTP ${res.status}).`;
                errBox.style.display = 'block';
                return;
            }

            const user = await res.json();
            const opt = document.createElement('option');
            opt.value = user.id;
            opt.textContent = `${user.nama} (${user.username})`;
            opt.selected = true;
            select.appendChild(opt);

            // Push nama akun balik ke kolom Nama Siswa supaya konsisten (tetap bisa diedit).
            if (namaSiswa) namaSiswa.value = user.nama;

            closeBuatAkunModal();
        } catch (e) {
            errBox.textContent = 'Koneksi gagal: ' + e.message;
            errBox.style.display = 'block';
        } finally {
            btn.disabled = false;
        }
    };
})();
</script>
@endpush
