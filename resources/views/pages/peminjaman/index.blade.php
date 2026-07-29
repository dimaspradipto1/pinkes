@extends('dashboard.template')
@section('content')
<div class="pagetitle">
    <h1>Data Peminjaman</h1>
    <nav><ol class="breadcrumb">
        <li class="breadcrumb-item"><a href="{{ route('dashboard') }}">Home</a></li>
        <li class="breadcrumb-item active">Data Peminjaman</li>
    </ol></nav>
</div>
<section class="section">
    <div class="row"><div class="col-lg-12">
        <div class="card"><div class="card-body">
            <div class="d-flex justify-content-between align-items-center mb-3">
                <h5 class="card-title mb-0">Data Peminjaman</h5>
                <a href="{{ route('peminjaman.create') }}" class="btn btn-primary">
                    <i class="bi bi-plus-circle me-1"></i> Tambah Peminjaman
                </a>
            </div>

            {{-- Legend status --}}
            <div class="d-flex flex-wrap gap-2 mb-3">
                <span class="badge bg-warning text-dark"><i class="bi bi-hourglass-split me-1"></i> Menunggu Verifikasi</span>
                <span class="badge bg-primary"><i class="bi bi-check-circle me-1"></i> Divalidasi</span>
                <span class="badge bg-danger"><i class="bi bi-x-circle me-1"></i> Ditolak</span>
                <span class="badge bg-success"><i class="bi bi-flag-fill me-1"></i> Selesai</span>
            </div>

            {{ $dataTable->table() }}
        </div></div>
    </div></div>
</section>
@endsection
@push('scripts')
    {{ $dataTable->scripts(attributes: ['type' => 'module']) }}
@endpush
