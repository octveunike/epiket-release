@extends('layouts.app')

@section('content')

    @php
        $tgl = \Carbon\Carbon::parse($tanggal);
    @endphp

    <div class="page-header">
        <div>
            <div class="breadcrumb">
                Admin /
                <a href="{{ route('Absensi.index') }}" style="color:var(--primary);">Data Absensi</a>
                / {{ $tgl->format('d/m/Y') }}
            </div>
            <h2>Absensi {{ $tgl->translatedFormat('l, d F Y') }}</h2>
        </div>
        <a href="{{ route('Absensi.index') }}" class="btn btn-secondary">
            <i class="ri-arrow-left-line"></i> Kembali
        </a>
    </div>

    @if (session('success'))
        <div class="alert alert-success" id="success-alert">
            <i class="ri-checkbox-circle-line"></i> {{ session('success') }}
        </div>
        <script>setTimeout(()=>document.getElementById('success-alert').remove(), 3000);</script>
    @endif

    {{-- Ringkasan per kelas --}}
    @foreach ($absensiList as $absensi)
        @php
            $details = $absensi->details;
            $hadir   = $details->where('status_absensi_id', 1)->count();
            $izin    = $details->where('status_absensi_id', 2)->count();
            $sakit   = $details->where('status_absensi_id', 3)->count();
            $alpha   = $details->where('status_absensi_id', 4)->count();
            $total   = $details->count();
        @endphp

        <div class="card" style="margin-bottom:20px;">

            {{-- Header kelas --}}
            <div style="display:flex;align-items:center;justify-content:space-between;margin-bottom:16px;">
                <div style="display:flex;align-items:center;gap:12px;">
                    <div style="width:40px;height:40px;border-radius:10px;background:#e8f5e9;
                        display:flex;align-items:center;justify-content:center;font-size:20px;color:var(--primary);">
                        <i class="ri-school-line"></i>
                    </div>
                    <div>
                        <div style="font-size:16px;font-weight:700;color:#37474f;">
                            {{ $absensi->kelas->nama_kelas ?? 'Semua Kelas' }}
                        </div>
                        <div style="font-size:12px;color:var(--text-muted);">
                            {{ $absensi->periodeAkademik->nama_periode ?? '—' }}
                            &nbsp;·&nbsp;
                            @if ($absensi->status_validasi_id == 2)
                                <span style="color:#10b981;font-weight:600;">✓ Terverifikasi</span>
                            @else
                                <span style="color:#f59e0b;font-weight:600;">Belum Diverifikasi</span>
                            @endif
                        </div>
                    </div>
                </div>
            </div>

            {{-- Stat pills --}}
            <div style="display:grid;grid-template-columns:repeat(4,1fr);gap:10px;margin-bottom:16px;">
                <div style="background:#d1fae5;border-radius:10px;padding:12px;text-align:center;">
                    <div style="font-size:22px;font-weight:700;color:#065f46;">{{ $hadir }}</div>
                    <div style="font-size:11px;font-weight:600;color:#065f46;margin-top:2px;">Hadir</div>
                </div>
                <div style="background:#e0f2fe;border-radius:10px;padding:12px;text-align:center;">
                    <div style="font-size:22px;font-weight:700;color:#075985;">{{ $izin }}</div>
                    <div style="font-size:11px;font-weight:600;color:#075985;margin-top:2px;">Izin</div>
                </div>
                <div style="background:#fef3c7;border-radius:10px;padding:12px;text-align:center;">
                    <div style="font-size:22px;font-weight:700;color:#92400e;">{{ $sakit }}</div>
                    <div style="font-size:11px;font-weight:600;color:#92400e;margin-top:2px;">Sakit</div>
                </div>
                <div style="background:#fee2e2;border-radius:10px;padding:12px;text-align:center;">
                    <div style="font-size:22px;font-weight:700;color:#991b1b;">{{ $alpha }}</div>
                    <div style="font-size:11px;font-weight:600;color:#991b1b;margin-top:2px;">Alpha</div>
                </div>
            </div>

            {{-- Tabel siswa --}}
            <div class="table-responsive">
                <table>
                    <thead>
                        <tr>
                            <th style="width:5%;text-align:center;">No</th>
                            <th>Nama Siswa</th>
                            <th style="text-align:center;">Status</th>
                            <th style="text-align:center;">Seharian</th>
                            <th>Jam Tidak Hadir</th>
                            <th>Keterangan</th>
                        </tr>
                    </thead>
                    <tbody>
                        @forelse ($details as $d)
                            <tr>
                                <td style="text-align:center;">{{ $loop->iteration }}</td>
                                <td><strong>{{ $d->siswa->nama_siswa ?? '—' }}</strong></td>
                                <td style="text-align:center;">
                                    @php $s = $d->status_absensi_id; @endphp
                                    @if ($s == 1)
                                        <span class="badge badge-success">Hadir</span>
                                    @elseif ($s == 2)
                                        @if(!$d->is_full_day)
                                            <span class="badge badge-success">Hadir</span>
                                            <span class="badge badge-info" style="margin-left:4px;">Izin</span>
                                        @else
                                            <span class="badge badge-info">Izin</span>
                                        @endif
                                    @elseif ($s == 3)
                                        @if(!$d->is_full_day)
                                            <span class="badge badge-success">Hadir</span>
                                            <span class="badge badge-warning" style="margin-left:4px;">Sakit</span>
                                        @else
                                            <span class="badge badge-warning">Sakit</span>
                                        @endif
                                    @elseif ($s == 4)
                                        @if(!$d->is_full_day)
                                            <span class="badge badge-success">Hadir</span>
                                            <span class="badge badge-danger" style="margin-left:4px;">Alpha</span>
                                        @else
                                            <span class="badge badge-danger">Alpha</span>
                                        @endif
                                    @else
                                        <span class="badge">—</span>
                                    @endif
                                </td>
                                <td style="text-align:center;">
                                    @if ($d->is_full_day)
                                        <span style="color:var(--primary);font-weight:600;font-size:12px;">✓ Seharian</span>
                                    @else
                                        <span style="color:#f59e0b;font-weight:600;font-size:12px;">Per Jam</span>
                                    @endif
                                </td>
                                <td>
                                    @if (!$d->is_full_day && $d->jams->count())
                                        <div style="display:flex;flex-wrap:wrap;gap:4px;">
                                            @foreach ($d->jams as $j)
                                                <span style="background:#fff3cd;border:1px solid #fde68a;color:#92400e;
                                                    font-size:11px;font-weight:600;padding:2px 8px;border-radius:10px;">
                                                    Jam {{ $j->jam->jam_ke ?? '?' }}
                                                </span>
                                            @endforeach
                                        </div>
                                    @else
                                        <span style="color:var(--text-muted);font-size:12px;">—</span>
                                    @endif
                                </td>
                                <td style="color:var(--text-muted);font-size:13px;">
                                    {{ $d->keterangan ?? '—' }}
                                </td>
                            </tr>
                        @empty
                            <tr>
                                <td colspan="6" style="text-align:center;color:var(--text-muted);padding:24px;">
                                    Tidak ada data kehadiran untuk kelas ini
                                </td>
                            </tr>
                        @endforelse
                    </tbody>
                </table>
            </div>
        </div>
    @endforeach

@endsection