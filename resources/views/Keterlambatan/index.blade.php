@extends('layouts.app')

@section('content')

<div class="page-header">
    <div>
        <div class="breadcrumb">Admin / <span class="breadcrumb-link">Keterlambatan</span></div>
        <h2>Data Keterlambatan</h2>
    </div>
    <a href="{{ route('Keterlambatan.create') }}" class="btn btn-primary">
        <i class="ri-add-line"></i> Catat Keterlambatan
    </a>
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
    <form method="GET" action="{{ route('Keterlambatan.index') }}" style="display:flex;align-items:flex-end;gap:12px;flex-wrap:wrap;">

        <div class="form-group" style="margin-bottom:0;min-width:160px;">
            <label class="form-label">Dari Tanggal</label>
            <input type="date" name="dari" class="form-control"
                value="{{ request('dari', \Carbon\Carbon::now()->subDays(7)->format('Y-m-d')) }}">
        </div>

        <div class="form-group" style="margin-bottom:0;min-width:160px;">
            <label class="form-label">Sampai Tanggal</label>
            <input type="date" name="sampai" class="form-control"
                value="{{ request('sampai', \Carbon\Carbon::now()->format('Y-m-d')) }}">
        </div>

        <div class="form-group" style="margin-bottom:0;min-width:180px;">
            <label class="form-label">Kelas</label>
            @if ($scopeKelas)
                <input type="hidden" name="kelas_id" value="{{ $scopeKelas->id }}">
                <input type="text" class="form-control" value="{{ $scopeKelas->nama_kelas }}" readonly tabindex="-1">
            @else
                <select name="kelas_id" class="form-control">
                    <option value="">Semua Kelas</option>
                    @foreach ($kelas as $k)
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
        <a href="{{ route('Keterlambatan.index') }}" class="btn btn-secondary" style="margin-bottom:0;">
            <i class="ri-refresh-line"></i> Reset
        </a>
    </form>
</div>

<div class="card">
    <div class="table-responsive">
        <table id="tableKeterlambatan" class="dt-table"
            data-destroy-url="{{ route('Keterlambatan.destroy', '') }}">
            <thead>
                <tr>
                    <th class="col-no">No</th>
                    <th>Tanggal</th>
                    <th>Nama Siswa</th>
                    <th>Kelas</th>
                    <th class="col-center">Waktu Masuk</th>
                    <th>Alasan</th>
                    <th class="col-center">Aksi</th>
                    <th>Update Terakhir</th>
                </tr>
            </thead>
            <tbody>
                @foreach ($keterlambatan as $kt)
                    <tr>
                        <td class="col-no">{{ $loop->iteration }}</td>
                        <td><strong>{{ \Carbon\Carbon::parse($kt->absensi->tanggal)->locale('id')->translatedFormat('l, d F Y') }}</strong></td>
                        <td><strong>{{ $kt->siswa->nama_siswa ?? '—' }}</strong></td>
                        <td>{{ $kt->absensi->kelas->nama_kelas ?? '—' }}</td>
                        <td class="col-center" style="font-weight:600;color:#ef4444;">
                            {{ \Carbon\Carbon::parse($kt->waktu_masuk)->format('H:i') }}
                        </td>
                        <td>{{ $kt->alasan ?: '—' }}</td>
                        <td class="col-center" style="white-space:nowrap;">
                            <button type="button" class="btn btn-sm btn-danger"
                                onclick="showDeleteModal({{ $kt->id }}, '{{ $kt->siswa->nama_siswa ?? '' }}')">
                                <i class="ri-delete-bin-line"></i> Hapus
                            </button>
                        </td>
                        <td>{{ $kt->userUpdate->nama ?? $kt->userInput->nama ?? 'Auto' }}</td>
                    </tr>
                @endforeach
            </tbody>
        </table>
    </div>
</div>

{{-- Delete Modal --}}
<div class="confirm-overlay" id="deleteModal">
    <div class="confirm-box">
        <div class="confirm-icon">!</div>
        <h3>Hapus Keterlambatan?</h3>
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

@endsection

@push('scripts')
<script>
function showDeleteModal(id, nama) {
    document.getElementById('delete-form').action = "{{ route('Keterlambatan.destroy', '') }}/" + id;
    document.getElementById('deleteModal').classList.add('show');
}
function closeDeleteModal() {
    document.getElementById('deleteModal').classList.remove('show');
}
document.getElementById('deleteModal').addEventListener('click', function(e) {
    if (e.target === this) closeDeleteModal();
});
</script>
@endpush