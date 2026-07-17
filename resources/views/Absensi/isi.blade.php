@extends('layouts.app')

@section('content')

<div class="page-header">
    <div>
        <div class="breadcrumb">
            Admin /
            <a href="{{ route('Absensi.index') }}" class="breadcrumb-link">Data Absensi</a>
            / Isi Absensi
        </div>
        <h2>Isi Absensi</h2>
    </div>
    <div style="display:flex;gap:8px;">
        <a href="{{ route('Absensi.index') }}" class="btn btn-secondary btn-sm">
            <i class="ri-arrow-left-line"></i> Kembali
        </a>
        <button class="btn btn-primary" onclick="simpan()">
            <i class="ri-save-line"></i> Simpan Absensi
        </button>
    </div>
</div>

{{-- Info Bar --}}
<div class="ab-infobar">
    <div class="ab-infobar-left">
        <i class="ri-school-line"></i>
        <strong>{{ $absensi->kelas->nama_kelas ?? '—' }}</strong>
        <span class="ab-infobar-sep">·</span>
        <span>{{ \Carbon\Carbon::parse($absensi->tanggal)->translatedFormat('d F Y') }}</span>
        <span class="ab-infobar-sep">·</span>
        <span>{{ $absensi->periodeAkademik->nama_periode ?? '—' }}</span>
    </div>
    <div class="ab-infobar-pills">
        <span class="ab-ipill ab-ipill-h"><span id="iHadir">{{ $siswa->count() }}</span> Hadir</span>
        <span class="ab-ipill ab-ipill-a"><span id="iAbsen">0</span> Absen</span>
        <span class="ab-ipill ab-ipill-t">{{ $siswa->count() }} Total</span>
    </div>
</div>

{{-- Add Card — di atas --}}
<div class="add-card" id="addCard">
    <div class="add-card-header">
        <div class="add-card-title"><i class="ri-user-add-line"></i> Tambah Ketidakhadiran</div>
        <button class="btn-add-toggle" id="toggleAddBtn" onclick="toggleAddForm()">
            <i class="ri-add-line"></i> Tambah
        </button>
    </div>

    <div class="add-form-body" id="addFormBody">

        <div class="form-grid" style="margin-top:12px;margin-bottom:0;">
            <div class="form-group" style="margin-bottom:0;">
                <label class="form-label">Siswa <span class="required">*</span></label>
                <select class="form-control form-control-sm" id="siswaEl">
                    <option value="">-- Pilih Siswa --</option>
                    @foreach ($siswa as $s)
                        <option value="{{ $s->id }}">{{ $s->nama_siswa }}</option>
                    @endforeach
                </select>
            </div>
            <div class="form-group" style="margin-bottom:0;">
                <label class="form-label">Status <span class="required">*</span></label>
                <div class="st-pills" id="stPills">
                    <div class="st-pill sp-I" onclick="selectStatus('I',this)">Izin</div>
                    <div class="st-pill sp-S" onclick="selectStatus('S',this)">Sakit</div>
                    <div class="st-pill sp-A" onclick="selectStatus('A',this)">Alpha</div>
                </div>
            </div>
        </div>

        {{-- Toggle seharian + range jam inline — di-disable saat Alpha --}}
        <div style="display:flex;align-items:center;gap:12px;margin:10px 0 0;flex-wrap:wrap;" id="toggleFullDayRow">
            <div style="display:flex;align-items:center;gap:10px;cursor:pointer;" onclick="toggleFullDay()">
                <label class="tgl" onclick="event.stopPropagation()">
                    <input type="checkbox" id="fdCheck" checked onchange="toggleFullDay()">
                    <span class="tsl"></span>
                </label>
                <span style="font-size:13px;font-weight:500;color:#374151;" id="fdSubLbl">Tidak Hadir Seharian</span>
            </div>

            {{-- Dropdown jam — muncul inline saat per jam --}}
            <div id="jamInline" style="display:none;align-items:center;gap:6px;flex:1;min-width:0;">
                <span style="font-size:12px;color:#64748b;white-space:nowrap;">Jam</span>
                <select class="form-control form-control-sm" id="jamDari" style="min-width:0;flex:1;">
                    <option value="">-- Pilih --</option>
                    @foreach ($jam as $j)
                        <option value="{{ $j->id }}" data-idx="{{ $loop->index }}">
                            {{ $j->jam_ke }} ({{ \Carbon\Carbon::parse($j->waktu_mulai)->format('H:i') }})
                        </option>
                    @endforeach
                </select>
                <span style="font-size:12px;color:#64748b;white-space:nowrap;">s/d</span>
                <select class="form-control form-control-sm" id="jamSampai" style="min-width:0;flex:1;">
                    <option value="">-- Pilih --</option>
                    @foreach ($jam as $j)
                        <option value="{{ $j->id }}" data-idx="{{ $loop->index }}">
                            {{ $j->jam_ke }} ({{ \Carbon\Carbon::parse($j->waktu_selesai)->format('H:i') }})
                        </option>
                    @endforeach
                </select>
            </div>
        </div>

        <div class="form-grid" style="margin-top:10px;margin-bottom:0;">
            <div class="form-group" style="margin-bottom:0;">
                <label class="form-label">Keterangan Tambahan</label>
                <textarea class="form-control form-control-sm" id="ketEl" rows="3"
                    placeholder="Mis: sakit demam, meninggalkan kelas, keperluan keluarga…"
                    style="resize:none;height:72px;"></textarea>
            </div>
            <div class="form-group" style="margin-bottom:0;">
                <label class="form-label">Lampiran <small style="color:#94a3b8;">(opsional)</small></label>
                <input type="file" id="fileEl" style="display:none;" accept=".jpg,.jpeg,.png,.pdf" onchange="handleFile(this)">
                <div id="fileEmpty" onclick="document.getElementById('fileEl').click()" style="cursor:pointer;">
                    <button type="button" class="btn-upload" style="min-height:72px;">
                        <i class="ri-upload-cloud-line"></i>
                        <span>Upload Lampiran</span>
                        <span class="bu-hint">JPG, PNG, PDF · Maks 5 MB</span>
                    </button>
                </div>
                <div id="filePreview" style="display:none;" class="file-preview-row">
                    <div class="file-preview-icon"><i class="ri-file-line"></i></div>
                    <div style="flex:1;min-width:0;">
                        <div class="file-preview-name" id="fileName">-</div>
                        <div style="font-size:11px;color:#6ee7b7;">Terpilih</div>
                    </div>
                    <button type="button" class="file-remove-btn" onclick="clearFile()">
                        <i class="ri-delete-bin-line"></i>
                    </button>
                </div>
            </div>
        </div>

        <div style="display:flex;justify-content:flex-end;gap:8px;margin-top:12px;padding-top:10px;border-top:1px solid var(--border);">
            <button class="btn-cancel-txt" onclick="cancelAdd()">Batal</button>
            <button class="btn btn-primary btn-sm" onclick="addEntry()">
                <i class="ri-add-circle-line"></i> Tambahkan
            </button>
        </div>

    </div>
</div>

{{-- Tabel — hanya yang ada catatan --}}
<div class="card" id="entryCard" style="display:none;">
    <div class="table-responsive">
        <table>
            <thead>
                <tr>
                    <th class="col-no">No</th>
                    <th>Nama Siswa</th>
                    <th class="col-center">Status</th>
                    <th class="col-center">Waktu Izin</th>
                    <th>Keterangan</th>
                    <th class="col-center">Update Terakhir</th>
                    <th class="col-center">Aksi</th>
                </tr>
            </thead>
            <tbody id="entryTbody"></tbody>
        </table>
    </div>
</div>

<div class="empty-state" id="emptyState">
    <i class="ri-user-unfollow-line"></i>
    <p>Semua siswa hadir.<br>Tambahkan jika ada ketidakhadiran.</p>
</div>

{{-- Form POST --}}
<form id="absensiForm" method="POST"
    action="{{ route('Absensi.storeDetail', $absensi->id) }}"
    enctype="multipart/form-data"
    style="display:none;">
    @csrf
    <div id="f_detail_container"></div>
</form>

@endsection

@push('scripts')
@php
$jamDataArr = $jam->values()->map(fn($j, $i) => [
    'id'            => $j->id,
    'idx'           => $i,
    'jam_ke'        => $j->jam_ke,
    'waktu_mulai'   => \Carbon\Carbon::parse($j->waktu_mulai)->format('H:i'),
    'waktu_selesai' => \Carbon\Carbon::parse($j->waktu_selesai)->format('H:i'),
    'label'         => 'Jam '.$j->jam_ke.' ('.\Carbon\Carbon::parse($j->waktu_mulai)->format('H:i').')',
])->values();

$siswaDataArr = $siswa->map(fn($s) => ['id' => $s->id, 'nama_siswa' => $s->nama_siswa])->values();

// Rentang waktu seharian dari tabel jam_absensi (dinamis)
$jamPertama      = $jam->sortBy('jam_ke')->first();
$jamTerakhir     = $jam->sortByDesc('jam_ke')->first();
$waktuSehariData = [
    'mulai'   => $jamPertama  ? \Carbon\Carbon::parse($jamPertama->waktu_mulai)->format('H:i')   : '06:30',
    'selesai' => $jamTerakhir ? \Carbon\Carbon::parse($jamTerakhir->waktu_selesai)->format('H:i') : '09:30',
];

$statusMap = [1 => 'I', 2 => 'S', 3 => 'A', 4 => 'D', 5 => 'T'];

$preEntries = $sudahTercatat->map(function ($d) use ($statusMap, $waktuSehariData, $sumberBySiswa) {
    // Hitung waktuDisplay untuk per jam dari relasi jams
    if ($d->is_full_day) {
        $waktuDisplay = $waktuSehariData['mulai'] . ' – ' . $waktuSehariData['selesai'];
    } else {
        $jamsSorted = $d->jams->filter(fn($j) => $j->jam)->sortBy('jam.jam_ke');
        $jamAwal    = $jamsSorted->first()?->jam;
        $jamAkhir   = $jamsSorted->last()?->jam;
        $waktuDisplay = ($jamAwal && $jamAkhir)
            ? \Carbon\Carbon::parse($jamAwal->waktu_mulai)->format('H:i') . ' – ' . \Carbon\Carbon::parse($jamAkhir->waktu_selesai)->format('H:i')
            : 'Per Jam';
    }

    $sumber = $sumberBySiswa[(int) $d->siswa_id] ?? ['kind' => 'manual', 'user' => '—'];

    return [
        'siswaId'       => $d->siswa_id,
        'nama'          => $d->siswa->nama_siswa ?? '—',
        'status'        => $d->status_absensi_id ? ($statusMap[$d->status_absensi_id] ?? null) : null,
        'statusDisplay' => $d->status_absensi_id ? ($statusMap[$d->status_absensi_id] ?? null) : null,
        'isFullDay'     => (bool) $d->is_full_day,
        'tipe'          => null,
        'jamsIds'       => [],
        'jamLabel'      => '',
        'waktuDisplay'  => $waktuDisplay,
        'keterangan'    => $d->keterangan ?? '',
        'lampiranNama'  => '',
        // Lock only rows auto-created from approved Dispensasi (status_absensi_id = 4 / Dispen).
        // Manually-entered rows stay editable on re-edit so Ketua Kelas can delete them.
        'locked'        => (int) $d->status_absensi_id === 4,
        'sumberKind'    => $sumber['kind'],
        'sumberUser'    => $sumber['user'],
    ];
})->values();
@endphp
<script>
const jamData      = {!! json_encode($jamDataArr) !!};
const waktuSehari  = {!! json_encode($waktuSehariData) !!};
const allSiswa     = {!! json_encode($siswaDataArr) !!};
const currentUser  = {!! json_encode($currentUserNama) !!};
let entries        = {!! json_encode($preEntries) !!};
let curStatus      = '';

document.addEventListener('DOMContentLoaded', () => {
    renderEntryList();
    updatePills();
});

// ── Add form ──────────────────────────────────────────────
function toggleAddForm() {
    const body   = document.getElementById('addFormBody');
    const isOpen = body.classList.toggle('on');
    document.getElementById('addCard').classList.toggle('focused', isOpen);
    document.getElementById('toggleAddBtn').innerHTML = isOpen
        ? '<i class="ri-close-line"></i> Tutup'
        : '<i class="ri-add-line"></i> Tambah';
    if (isOpen) document.getElementById('emptyState').style.display = 'none';
    else if (!entries.length) document.getElementById('emptyState').style.display = 'block';
}

function cancelAdd() {
    document.getElementById('addFormBody').classList.remove('on');
    document.getElementById('addCard').classList.remove('focused');
    document.getElementById('toggleAddBtn').innerHTML = '<i class="ri-add-line"></i> Tambah';
    resetForm();
    if (!entries.length) document.getElementById('emptyState').style.display = 'block';
}

function resetForm() {
    document.getElementById('siswaEl').value = '';
    curStatus = '';
    document.querySelectorAll('#stPills .st-pill').forEach(p => p.classList.remove('sel'));
    const toggleRow = document.getElementById('toggleFullDayRow');
    toggleRow.style.opacity       = '';
    toggleRow.style.pointerEvents = '';
    document.getElementById('fdCheck').checked         = true;
    document.getElementById('fdSubLbl').textContent    = 'Tidak Hadir Seharian';
    document.getElementById('jamInline').style.display = 'none';
    document.getElementById('jamDari').value           = '';
    document.getElementById('jamSampai').value         = '';
    document.getElementById('ketEl').value             = '';
    clearFile();
}

// ── Pilih status ──────────────────────────────────────────
function selectStatus(s, el) {
    curStatus = s;
    document.querySelectorAll('#stPills .st-pill').forEach(p => p.classList.remove('sel'));
    el.classList.add('sel');
    const toggleRow = document.getElementById('toggleFullDayRow');
    const fdCheck   = document.getElementById('fdCheck');
    if (s === 'A') {
        toggleRow.style.opacity       = '0.4';
        toggleRow.style.pointerEvents = 'none';
        fdCheck.checked = true;
        document.getElementById('jamInline').style.display = 'none';
        document.getElementById('fdSubLbl').textContent    = 'Tidak Hadir Seharian';
    } else {
        toggleRow.style.opacity       = '';
        toggleRow.style.pointerEvents = '';
    }
}

// ── Toggle seharian ───────────────────────────────────────
function toggleFullDay() {
    const cb   = document.getElementById('fdCheck');
    if (event && event.target !== cb) cb.checked = !cb.checked;
    const perJam = !cb.checked;
    document.getElementById('jamInline').style.display = perJam ? 'flex' : 'none';
    document.getElementById('fdSubLbl').textContent    = cb.checked ? 'Tidak Hadir Seharian' : 'Tidak Hadir di Waktu :';
    if (cb.checked) {
        document.getElementById('jamDari').value   = '';
        document.getElementById('jamSampai').value = '';
    }
}

// ── File upload ───────────────────────────────────────────
function handleFile(input) {
    if (!input.files.length) return;
    if (input.files[0].size > 5 * 1024 * 1024) { alert('Ukuran file maks 5 MB!'); input.value = ''; return; }
    document.getElementById('fileName').textContent     = input.files[0].name;
    document.getElementById('fileEmpty').style.display  = 'none';
    document.getElementById('filePreview').style.display = 'flex';
}
function clearFile() {
    document.getElementById('fileEl').value               = '';
    document.getElementById('fileEmpty').style.display    = 'flex';
    document.getElementById('filePreview').style.display  = 'none';
}

// ── Tambahkan entry ───────────────────────────────────────
function addEntry() {
    const siswaEl = document.getElementById('siswaEl');
    const siswaId = parseInt(siswaEl.value);
    if (!siswaId)   { alert('Pilih siswa terlebih dahulu!'); return; }
    if (!curStatus) { alert('Pilih status terlebih dahulu!'); return; }
    if (entries.find(e => e.siswaId === siswaId)) { alert('Siswa sudah ditambahkan!'); return; }

    const isFullDay = document.getElementById('fdCheck').checked;
    let jamsIds      = [];
    let jamLabel     = '';
    let waktuDisplay = waktuSehari.mulai + ' – ' + waktuSehari.selesai; // default seharian

    if (!isFullDay && curStatus !== 'A') {
        const dariId   = document.getElementById('jamDari').value;
        const sampaiId = document.getElementById('jamSampai').value;
        if (!dariId || !sampaiId) { alert('Pilih jam dari dan sampai!'); return; }

        const dariObj   = jamData.find(j => j.id == dariId);
        const sampaiObj = jamData.find(j => j.id == sampaiId);
        if (sampaiObj.idx < dariObj.idx) { alert('Jam sampai tidak boleh lebih awal dari jam mulai!'); return; }

        jamsIds      = jamData.filter(j => j.idx >= dariObj.idx && j.idx <= sampaiObj.idx).map(j => j.id);
        jamLabel     = dariObj.label + '–' + sampaiObj.label;
        waktuDisplay = dariObj.waktu_mulai + ' – ' + sampaiObj.waktu_selesai;
    }

    const keteranganFinal = document.getElementById('ketEl').value.trim();
    const isPerJam        = (!isFullDay && curStatus !== 'A');
    const statusSimpan    = curStatus;

    entries.push({
        siswaId,
        nama         : siswaEl.options[siswaEl.selectedIndex].text,
        status       : statusSimpan,
        statusDisplay: curStatus,
        isFullDay    : curStatus === 'A' ? true : isFullDay,
        jamsIds,
        jamLabel,
        waktuDisplay,
        keterangan   : keteranganFinal,
        lampiranNama : document.getElementById('fileEl').files[0]?.name ?? '',
        locked       : false,
        sumberKind   : 'manual',
        sumberUser   : currentUser,
    });

    cancelAdd();
    renderEntryList();
    updatePills();
}

// ── Hapus entry ───────────────────────────────────────────
function deleteEntry(siswaId) {
    entries = entries.filter(e => e.siswaId !== siswaId);
    renderEntryList();
    refreshSiswaSelect();
    updatePills();
}

// ── Update counter pills ──────────────────────────────────
function updatePills() {
    const absenCount = entries.filter(e =>
        ['A', 'I', 'S'].includes(e.statusDisplay ?? e.status) && e.isFullDay
    ).length;
    document.getElementById('iHadir').textContent = allSiswa.length - absenCount;
    document.getElementById('iAbsen').textContent = absenCount;
}

// ── Refresh dropdown siswa ────────────────────────────────
function refreshSiswaSelect() {
    const sel      = document.getElementById('siswaEl');
    const addedIds = entries.map(e => e.siswaId);
    Array.from(sel.options).forEach(o => {
        if (o.value) o.disabled = addedIds.includes(parseInt(o.value));
    });
}

// ── Render tabel ──────────────────────────────────────────
function renderEntryList() {
    const tbody   = document.getElementById('entryTbody');
    const card    = document.getElementById('entryCard');
    const emptyEl = document.getElementById('emptyState');
    tbody.innerHTML = '';

    if (!entries.length) {
        card.style.display    = 'none';
        emptyEl.style.display = 'block';
        return;
    }

    card.style.display    = '';
    emptyEl.style.display = 'none';

    const bdStyle = {
        I: 'background:#e0f2fe;color:#075985;',
        S: 'background:#fef3c7;color:#92400e;',
        A: 'background:#fee2e2;color:#991b1b;',
        D: 'background:#e8f5e9;color:#1b5e20;',
        T: 'background:#fff3cd;color:#92400e;',
        H: 'background:#d1fae5;color:#065f46;',
    };
    const stLbl = { H:'Hadir', I:'Izin', S:'Sakit', A:'Alpha', D:'Dispen', T:'Terlambat' };

    entries.forEach((e, i) => {
        let statusCell = '';

        if (e.statusDisplay === 'T') {
            statusCell = `
                <span style="${bdStyle.H}padding:2px 9px;border-radius:20px;font-size:11px;font-weight:700;">Hadir</span>
                <span style="${bdStyle.T}padding:2px 9px;border-radius:20px;font-size:11px;font-weight:700;margin-left:4px;">Terlambat</span>`;
        } else if (!e.isFullDay) {
            const d = e.statusDisplay;
            if (d && d !== 'H') {
                const subLbl   = stLbl[d] ?? '';
                const subStyle = bdStyle[d] ?? '';
                statusCell = `
                    <span style="${bdStyle.H}padding:2px 9px;border-radius:20px;font-size:11px;font-weight:700;">Hadir</span>
                    <span style="${subStyle}padding:2px 9px;border-radius:20px;font-size:11px;font-weight:700;margin-left:4px;">${subLbl}</span>`;
            } else {
                statusCell = `<span style="${bdStyle.H}padding:2px 9px;border-radius:20px;font-size:11px;font-weight:700;">Hadir</span>`;
            }
        } else {
            const d = e.statusDisplay ?? 'H';
            statusCell = `<span style="${bdStyle[d]??''}padding:2px 9px;border-radius:20px;font-size:11px;font-weight:700;">${stLbl[d]??d}</span>`;
        }

        // Tampilkan rentang waktu: seharian dari DB, per jam dari pilihan
        const waktuStr = e.isFullDay
            ? `${waktuSehari.mulai} – ${waktuSehari.selesai}`
            : (e.waktuDisplay ?? 'Per Jam');

        const waktu = `<span style="font-size:12px;color:var(--text-muted);font-weight:600;">${waktuStr}</span>`;

        const sk = e.sumberKind ?? 'manual';
        const sn = e.sumberUser || '—';
        const sumberCell = sk === 'dispensasi'
            ? `<span style="font-size:12px;color:#374151;">Approval by ${sn}</span>`
            : `<span style="font-size:12px;color:#374151;">${sn}</span>`;

        const aksiCell = e.locked
            ? `<span style="color:var(--text-muted);font-size:12px;">—</span>`
            : `<button class="btn btn-sm btn-danger" onclick="deleteEntry(${e.siswaId})"><i class="ri-delete-bin-line"></i></button>`;

        const tr = document.createElement('tr');
        tr.innerHTML = `
            <td class="col-no">${i + 1}</td>
            <td><strong>${e.nama}</strong></td>
            <td class="col-center">${statusCell}</td>
            <td class="col-center">${waktu}</td>
            <td class="text-muted-sm">${e.keterangan || '—'}</td>
            <td class="col-center">${sumberCell}</td>
            <td class="col-center">${aksiCell}</td>
        `;
        tbody.appendChild(tr);
    });

    refreshSiswaSelect();
}

// ── Simpan (submit form) ──────────────────────────────────
function simpan() {
    const container = document.getElementById('f_detail_container');
    container.innerHTML = '';
    let idx = 0;
    const stMap = { I:1, S:2, A:3, D:4, T:5 };

    entries.forEach(e => {
        if (e.locked) return;
        addHidden(container, `detail[${idx}][siswa_id]`,    e.siswaId);
        addHidden(container, `detail[${idx}][is_full_day]`, e.isFullDay ? 1 : 0);
        addHidden(container, `detail[${idx}][keterangan]`,  e.keterangan);
        if (e.status !== null) {
            addHidden(container, `detail[${idx}][status_absensi_id]`, stMap[e.status] ?? '');
        }
        e.jamsIds.forEach(jamId => addHidden(container, `detail[${idx}][jams][]`, jamId));
        idx++;
    });

    document.getElementById('absensiForm').submit();
}

function addHidden(parent, name, value) {
    const inp = document.createElement('input');
    inp.type  = 'hidden';
    inp.name  = name;
    inp.value = value ?? '';
    parent.appendChild(inp);
}
</script>
@endpush