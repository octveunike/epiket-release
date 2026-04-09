@extends('layouts.app')

@section('content')

    <div class="page-header">
        <div>
            <div class="breadcrumb">Admin / <a href="{{ route('Organisasi.index') }}" style="color:var(--primary);">Data Organisasi</a> / Edit</div>
            <h2>Edit Organisasi</h2>
        </div>
    </div>

    @if (session('success'))
        <div class="alert alert-success" id="success-alert">
            <i class="ri-checkbox-circle-line"></i> {{ session('success') }}
        </div>
        <script>setTimeout(function() { document.getElementById('success-alert').remove(); }, 3000);</script>
    @endif
    @if (session('error'))
        <div class="alert alert-danger"><i class="ri-error-warning-line"></i> {{ session('error') }}</div>
    @endif

    {{-- ── FORM DATA ORGANISASI ── --}}
    <div class="card">
        <form method="POST" action="{{ route('Organisasi.update', $Organisasi->id) }}">
            @csrf @method('PUT')
            <div class="form-grid">
                <div class="form-group">
                    <label class="form-label">Nama Organisasi <span class="required">*</span></label>
                    <input type="text" name="nama_organisasi" class="form-control"
                        value="{{ old('nama_organisasi', $Organisasi->nama_organisasi) }}" required>
                    @error('nama_organisasi')<small style="color:#ef4444;">{{ $message }}</small>@enderror
                </div>
                <div class="form-group">
                    <label class="form-label">Pembina</label>
                    <select name="pembina_id" class="form-control">
                        <option value="">-- Pilih Pembina --</option>
                        @foreach ($gurus as $g)
                            <option value="{{ $g->id }}" {{ old('pembina_id', $Organisasi->pembina_id) == $g->id ? 'selected' : '' }}>
                                {{ $g->nama_guru }}
                            </option>
                        @endforeach
                    </select>
                </div>
                <div class="form-group" style="grid-column:1/-1;">
                    <label class="form-label">Keterangan</label>
                    <textarea name="keterangan" class="form-control" rows="2">{{ old('keterangan', $Organisasi->keterangan) }}</textarea>
                </div>
            </div>
            <div class="form-actions">
                <button type="submit" class="btn btn-primary"><i class="ri-save-line"></i> Simpan Perubahan</button>
                <a href="{{ route('Organisasi.index') }}" class="btn btn-secondary"><i class="ri-arrow-left-line"></i> Batal</a>
            </div>
        </form>
    </div>

    {{-- ── DAFTAR ANGGOTA ── --}}
    <div class="card" style="margin-top:24px;">
        <div style="display:flex;align-items:center;justify-content:space-between;margin-bottom:16px;">
            <div>
                <h3 style="margin:0;font-size:16px;font-weight:700;color:#37474f;">
                    <i class="ri-group-line" style="color:var(--primary);margin-right:6px;"></i>Daftar Anggota
                </h3>
                <small style="color:var(--text-muted);">{{ $anggota->count() }} anggota aktif</small>
            </div>
            <button type="button" class="btn btn-primary btn-sm" onclick="openTambahModal()">
                <i class="ri-user-add-line"></i> Tambah Anggota
            </button>
        </div>
        <div class="table-responsive">
            <table>
                <thead>
                    <tr>
                        <th style="width:5%;text-align:center;">No</th>
                        <th>Nama Siswa</th>
                        <th>Kelas</th>
                        <th style="text-align:center;">Aksi</th>
                    </tr>
                </thead>
                <tbody>
                    @forelse ($anggota as $item)
                        <tr>
                            <td style="text-align:center;">{{ $loop->iteration }}</td>
                            <td><strong>{{ $item->siswa->nama_siswa ?? '—' }}</strong></td>
                            <td>{{ $item->siswa->kelas->nama_kelas ?? '—' }}</td>
                            <td style="text-align:center;">
                                <button type="button" class="btn btn-sm btn-danger"
                                    onclick="showHapusModal({{ $item->id }}, '{{ addslashes($item->siswa->nama_siswa ?? '') }}')">
                                    <i class="ri-delete-bin-line"></i> Hapus
                                </button>
                            </td>
                        </tr>
                    @empty
                        <tr>
                            <td colspan="4" style="text-align:center;color:var(--text-muted);padding:28px;">
                                <i class="ri-user-line" style="font-size:28px;display:block;margin-bottom:6px;"></i>
                                Belum ada anggota. Klik <strong>Tambah Anggota</strong> untuk menambahkan.
                            </td>
                        </tr>
                    @endforelse
                </tbody>
            </table>
        </div>
    </div>

    {{-- ── MODAL TAMBAH ANGGOTA ── --}}
    <div class="modal-overlay" id="tambahAnggotaModal">
        <div class="modal-box" style="width:min(700px,95vw);max-width:700px;">
            <div class="modal-header">
                <span class="modal-title"><i class="ri-user-add-line"></i> Tambah Anggota</span>
                <button class="modal-close" onclick="closeTambahModal()"><i class="ri-close-line"></i></button>
            </div>
            <form method="POST" action="{{ route('Organisasi.anggota.store', $Organisasi->id) }}">
                @csrf
                <div class="modal-body">
                    {{-- Filter --}}
                    <div style="display:grid;grid-template-columns:1fr 1fr;gap:10px;margin-bottom:12px;">
                        <div>
                            <label style="font-size:12px;font-weight:600;color:#546e7a;display:block;margin-bottom:4px;">Filter Kelas</label>
                            <select id="kelasFilter" class="form-control" onchange="applyFilter()">
                                <option value="">-- Semua Kelas --</option>
                                @foreach ($kelass as $k)
                                    <option value="{{ $k->id }}">{{ $k->nama_kelas }}</option>
                                @endforeach
                            </select>
                        </div>
                        <div>
                            <label style="font-size:12px;font-weight:600;color:#546e7a;display:block;margin-bottom:4px;">Cari Nama Siswa</label>
                            <input type="text" id="siswaSearch" class="form-control" placeholder="Ketik nama siswa..." oninput="applyFilter()">
                        </div>
                    </div>
                    <div style="font-size:12px;color:#94a3b8;margin-bottom:8px;" id="siswaCounter"></div>
                    {{-- List: pakai table agar tidak terpengaruh CSS global yang override flex/block --}}
                    <div style="border:1.5px solid #e0e0e0;border-radius:8px;overflow:hidden;max-height:300px;overflow-y:auto;">
                        <table style="width:100%;border-collapse:collapse;" id="siswaTable">
                            <tbody>
                                @forelse ($siswas as $s)
                                    <tr id="siswaOpt_{{ $s->id }}"
                                        data-nama="{{ strtolower($s->nama_siswa) }}"
                                        data-kelas="{{ $s->kelas_id ?? '' }}"
                                        data-siswa-nama="{{ $s->nama_siswa }}"
                                        onclick="selectRow(this)"
                                        style="cursor:pointer;border-bottom:1px solid #f1f5f9;transition:background .15s;">
                                        <td style="width:40px;padding:12px 6px 12px 16px;vertical-align:middle;">
                                            <input type="radio" name="siswa_id" value="{{ $s->id }}"
                                                style="accent-color:var(--primary);width:16px;height:16px;
                                                cursor:pointer;margin:0;pointer-events:none;">
                                        </td>
                                        <td style="padding:12px 8px;vertical-align:middle;">
                                            <div style="font-size:14px;font-weight:600;color:#263238;line-height:1.3;">{{ $s->nama_siswa }}</div>
                                        </td>
                                        <td style="padding:12px 16px 12px 8px;vertical-align:middle;">
                                            <span style="font-size:12px;color:#90a4ae;">{{ $s->kelas->nama_kelas ?? '—' }}</span>
                                        </td>
                                    </tr>
                                @empty
                                    <tr>
                                        <td colspan="3" style="padding:24px;text-align:center;color:#94a3b8;font-size:13px;">
                                            <i class="ri-user-line" style="font-size:24px;display:block;margin-bottom:6px;"></i>
                                            Semua siswa sudah menjadi anggota
                                        </td>
                                    </tr>
                                @endforelse
                            </tbody>
                        </table>
                        <div id="noResult" style="display:none;padding:20px;text-align:center;color:#94a3b8;font-size:13px;">
                            <i class="ri-search-line" style="font-size:22px;display:block;margin-bottom:4px;"></i>
                            Tidak ada siswa yang cocok
                        </div>
                    </div>
                </div>
                <div class="modal-footer" style="justify-content:space-between;align-items:center;">
                    <span id="selectedLabel" style="font-size:13px;color:var(--primary);font-weight:600;"></span>
                    <div style="display:flex;gap:8px;">
                        <button type="button" class="btn btn-secondary" onclick="closeTambahModal()">Batal</button>
                        <button type="submit" class="btn btn-primary"><i class="ri-user-add-line"></i> Tambahkan</button>
                    </div>
                </div>
            </form>
        </div>
    </div>

    {{-- ── MODAL HAPUS ANGGOTA ── --}}
    <div class="modal-overlay" id="hapusAnggotaModal">
        <div class="modal-box">
            <div class="modal-header">
                <span class="modal-title">Hapus Anggota</span>
                <button class="modal-close" onclick="closeHapusModal()"><i class="ri-close-line"></i></button>
            </div>
            <div class="modal-body">
                <div class="modal-icon danger"><i class="ri-error-warning-line"></i></div>
                <p style="text-align:center;color:var(--text-muted);margin-top:12px;">
                    Hapus <strong id="hapusNama" style="color:#37474f;"></strong> dari anggota organisasi ini?
                </p>
            </div>
            <div class="modal-footer">
                <button type="button" class="btn btn-secondary" onclick="closeHapusModal()">Batal</button>
                <form id="hapusForm" method="POST" style="display:inline;">
                    @csrf @method('DELETE')
                    <button type="submit" class="btn btn-danger"><i class="ri-delete-bin-line"></i> Hapus</button>
                </form>
            </div>
        </div>
    </div>

@endsection

@push('scripts')
<script>
    // ── Tambah Anggota ──────────────────────────────
    function openTambahModal() {
        document.getElementById('siswaSearch').value = '';
        document.getElementById('kelasFilter').value = '';
        document.getElementById('selectedLabel').textContent = '';
        // Reset semua row
        document.querySelectorAll('[id^="siswaOpt_"]').forEach(el => {
            el.style.background = '';
            const r = el.querySelector('input[type=radio]');
            if (r) r.checked = false;
        });
        applyFilter();
        document.getElementById('tambahAnggotaModal').classList.add('active');
    }
    function closeTambahModal() {
        document.getElementById('tambahAnggotaModal').classList.remove('active');
    }

    function applyFilter() {
        const q     = document.getElementById('siswaSearch').value.toLowerCase();
        const kelas = document.getElementById('kelasFilter').value;
        const rows  = document.querySelectorAll('[id^="siswaOpt_"]');
        let vis = 0;
        rows.forEach(el => {
            const matchNama  = el.dataset.nama.includes(q);
            const matchKelas = !kelas || el.dataset.kelas === kelas;
            const show = matchNama && matchKelas;
            el.style.display = show ? 'table-row' : 'none';
            if (show) vis++;
        });
        document.getElementById('siswaCounter').textContent =
            vis + ' siswa' + (q || kelas ? ' ditemukan' : ' tersedia');
        document.getElementById('noResult').style.display = vis === 0 ? '' : 'none';
    }

    function selectRow(div) {
        // Uncheck semua, highlight baris ini
        document.querySelectorAll('[id^="siswaOpt_"]').forEach(el => {
            el.style.background = '';
            el.querySelector('input[type=radio]').checked = false;
        });
        div.style.background = '#f0fdf4';
        div.querySelector('input[type=radio]').checked = true;
        const nama = div.dataset.siswaNama || '';
        document.getElementById('selectedLabel').textContent = '✓ Dipilih: ' + nama;
    }

    // ── Hapus Anggota ───────────────────────────────
    function showHapusModal(id, nama) {
        document.getElementById('hapusNama').textContent = nama;
        document.getElementById('hapusForm').action =
            "{{ route('Organisasi.anggota.destroy', [$Organisasi->id, '']) }}/" + id;
        document.getElementById('hapusAnggotaModal').classList.add('active');
    }
    function closeHapusModal() {
        document.getElementById('hapusAnggotaModal').classList.remove('active');
    }

    // Backdrop click
    ['tambahAnggotaModal','hapusAnggotaModal'].forEach(id => {
        document.getElementById(id).addEventListener('click', function(e) {
            if (e.target === this) this.classList.remove('active');
        });
    });

    document.addEventListener('DOMContentLoaded', applyFilter);
</script>
@endpush