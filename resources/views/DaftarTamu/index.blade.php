@extends('layouts.app')

@section('content')

<div class="page-header">
    <div>
        <div class="breadcrumb">
            Admin / 
            <a href="{{ route('DaftarTamu.index') }}" class="breadcrumb-link">Daftar Tamu</a>
        </div>
        <h2>Data Daftar Tamu</h2>
    </div>

    <a href="{{ route('DaftarTamu.create') }}" class="btn btn-primary">
        <i class="ri-add-line"></i> Tambah Tamu
    </a>
</div>

@if (session('success'))
    <div class="alert alert-success" id="success-alert">
        <i class="ri-checkbox-circle-line"></i> {{ session('success') }}
    </div>
@endif

@if (session('error'))
    <div class="alert alert-danger">
        <i class="ri-error-warning-line"></i> {{ session('error') }}
    </div>
@endif

<div class="card">

    <div class="table-responsive">
        <table id="tableDaftarTamu"
               class="dt-table"
               data-destroy-url="{{ route('DaftarTamu.destroy', '') }}">

            <thead>
                <tr>
                    <th>No</th>
                    <th>Tanggal</th>
                    <th>Nama</th>
                    <th>Lembaga / Organisasi</th>
                    <th>Orang Dituju</th>
                    <th>Tujuan</th>
                    <th>Aksi</th>
                </tr>
            </thead>

            <tbody>
                @forelse ($data as $val)
                    <tr>
                        <td>{{ $loop->iteration }}</td>

                        <td data-order="{{ $val->tanggal_kunjungan }}">
                            {{ \Carbon\Carbon::parse($val->tanggal_kunjungan)->format('d M Y') }}
                        </td>

                        <td>{{ $val->nama ?? '-' }}</td>
                        <td>{{ $val->lembaga_organisasi ?? '-' }}</td>
                        <td>{{ $val->orang_yang_dituju ?? '-' }}</td>
                        <td>{{ $val->tujuan_kunjungan ?? '-' }}</td>

                        <td style="text-align:center; white-space:nowrap;">
                            <a href="{{ route('DaftarTamu.edit', $val->id) }}" class="btn btn-sm btn-primary">
                                <i class="ri-edit-2-line"></i> Edit
                            </a>

                            <button type="button"
                                    class="btn btn-sm btn-danger"
                                    onclick="showDeleteModal({{ $val->id }})">
                                <i class="ri-delete-bin-line"></i> Hapus
                            </button>
                        </td>
                    </tr>
                @empty
                @endforelse
            </tbody>

        </table>
    </div>
</div>

{{-- DELETE MODAL --}}
<div class="confirm-overlay" id="deleteModal">
    <div class="confirm-box">
        <div class="confirm-icon">!</div>
        <h3>Hapus Data?</h3>
        <p>Data yang dihapus tidak dapat dikembalikan.</p>

        <div class="confirm-actions">
            <form id="delete-form" method="POST">
                @csrf
                @method('DELETE')
                <button type="submit" class="btn btn-danger">
                    Ya, Hapus
                </button>
            </form>

            <button type="button"
                    onclick="closeDeleteModal()"
                    class="btn btn-secondary">
                Batal
            </button>
        </div>
    </div>
</div>

@endsection