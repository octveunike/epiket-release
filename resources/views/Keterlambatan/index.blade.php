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
            <label class="form-label">Tanggal</label>
            <input type="date" name="tanggal" class="form-control" value="{{ $tanggal }}">
        </div>
        <div class="form-group" style="margin-bottom:0;min-width:180px;">
            <label class="form-label">Kelas</label>
            <select name="kelas_id" class="form-control">
                <option value="">Semua Kelas</option>
                @foreach ($kelas as $k)
                    <option value="{{ $k->id }}" {{ request('kelas_id') == $k->id ? 'selected' : '' }}>
                        {{ $k->nama_kelas }}
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
    <div class="card-title">
        <i class="ri-time-line"></i> Data Keterlambatan
        @if ($keterlambatan->total() > 0)
            <span class="ab-ipill ab-ipill-a" style="margin-left:8px;">{{ $keterlambatan->total() }} siswa</span>
        @endif
    </div>
    <div class="table-responsive">
        <table>
            <thead>
                <tr>
                    <th class="col-no">No</th>
                    <th>Nama Siswa</th>
                    <th>Kelas</th>
                    <th class="col-center">Waktu Masuk</th>
                    <th>Alasan</th>
                    <th>Periode</th>
                    <th class="col-center">Aksi</th>
                </tr>
            </thead>
            <tbody>
                @forelse ($keterlambatan as $kt)
                    <tr>
                        <td class="col-no">{{ $keterlambatan->firstItem() + $loop->index }}</td>
                        <td><strong>{{ $kt->siswa->nama_siswa ?? '—' }}</strong></td>
                        <td>{{ $kt->absensi->kelas->nama_kelas ?? '—' }}</td>
                        <td class="col-center" style="font-weight:600;color:var(--primary);">
                            {{ \Carbon\Carbon::parse($kt->waktu_masuk)->format('H:i') }}
                        </td>
                        <td class="text-muted-sm">{{ $kt->alasan ?: '—' }}</td>
                        <td class="text-muted-sm">{{ $kt->periodeAkademik->nama_periode ?? '—' }}</td>
                        <td class="col-center">
                            <button type="button" class="btn btn-sm btn-danger"
                                onclick="showDeleteModal({{ $kt->id }})">
                                <i class="ri-delete-bin-line"></i> Hapus
                            </button>
                        </td>
                    </tr>
                @empty
                    <tr>
                        <td colspan="7" class="td-empty">
                            <i class="ri-time-line" style="font-size:32px;display:block;margin-bottom:8px;"></i>
                            Tidak ada keterlambatan pada tanggal ini.
                        </td>
                    </tr>
                @endforelse
            </tbody>
        </table>
    </div>

    @if ($keterlambatan->hasPages())
        <div style="padding:12px 16px;">
            {{ $keterlambatan->withQueryString()->links() }}
        </div>
    @endif
</div>

{{-- Modal hapus konsisten dengan Kelas index --}}
<div class="confirm-overlay" id="deleteModal">
    <div class="confirm-box">
        <div class="confirm-icon">!</div>
        <h3>Are you sure?</h3>
        <p>Data keterlambatan ini akan dihapus.<br>Status absensi siswa akan dikembalikan ke <strong>Hadir</strong>.</p>
        <div class="confirm-actions">
            <form id="delete-form" method="POST" style="display:inline;">
                @csrf @method('DELETE')
                <button type="submit" class="btn btn-danger">Yes, delete it!</button>
            </form>
            <button onclick="closeDeleteModal()" class="btn btn-secondary">Cancel</button>
        </div>
    </div>
</div>

@endsection

@push('scripts')
<script>
function showDeleteModal(id) {
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