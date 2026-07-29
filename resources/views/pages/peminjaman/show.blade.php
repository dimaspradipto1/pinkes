@extends('dashboard.template')
@section('content')
<div class="pagetitle">
    <h1>Detail Peminjaman</h1>
    <nav><ol class="breadcrumb">
        <li class="breadcrumb-item"><a href="{{ route('dashboard') }}">Home</a></li>
        <li class="breadcrumb-item"><a href="{{ route('peminjaman.index') }}">Data Peminjaman</a></li>
        <li class="breadcrumb-item active">Detail</li>
    </ol></nav>
</div>
<section class="section">
    <div class="row"><div class="col-lg-9 mx-auto">
        <div class="card shadow-sm"><div class="card-body pt-4">

            <div class="d-flex justify-content-between align-items-center mb-4">
                <h5 class="card-title mb-0">Detail Peminjaman #{{ $peminjaman->id }}</h5>
                <div class="d-flex gap-2">
                    @php
                        $statusMap = [
                            'pending'  => ['bg-warning text-dark', 'Menunggu Verifikasi'],
                            'validasi' => ['bg-primary text-white', 'Divalidasi'],
                            'ditolak'  => ['bg-danger text-white', 'Ditolak'],
                            'selesai'  => ['bg-success text-white', 'Selesai'],
                        ];
                        [$sCls, $sLabel] = $statusMap[$peminjaman->status] ?? ['bg-secondary text-white', $peminjaman->status];
                    @endphp
                    <span class="badge {{ $sCls }} fs-6 px-3 py-2">{{ $sLabel }}</span>
                    @if(Auth::user()->isUser() && $peminjaman->status === 'pending')
                    <a href="{{ route('peminjaman.edit', $peminjaman->id) }}" class="btn btn-warning btn-sm text-white">
                        <i class="bi bi-pencil-square me-1"></i> Edit
                    </a>
                    @elseif(Auth::user()->isAdmin())
                    <a href="{{ route('peminjaman.edit', $peminjaman->id) }}" class="btn btn-warning btn-sm text-white">
                        <i class="bi bi-pencil-square me-1"></i> Edit
                    </a>
                    @endif
                    <a href="{{ route('peminjaman.index') }}" class="btn btn-secondary btn-sm">
                        <i class="bi bi-arrow-left me-1"></i> Kembali
                    </a>
                </div>
            </div>

            <div class="row g-3">
                <div class="col-md-6">
                    <div class="p-3 bg-light rounded-3">
                        <div class="text-muted small mb-1">Peminjam</div>
                        <div class="fw-semibold">{{ $peminjaman->user?->name ?? '-' }}</div>
                        @if($peminjaman->user?->jabatan_nama)
                        <div class="text-muted small">{{ $peminjaman->user->jabatan_nama }}</div>
                        @endif
                    </div>
                </div>
                <div class="col-md-6">
                    <div class="p-3 bg-light rounded-3">
                        <div class="text-muted small mb-1">Tujuan Pinjam</div>
                        <div class="fw-semibold">{{ $peminjaman->pinjam?->tujuan_pinjam ?? '-' }}</div>
                    </div>
                </div>
                <div class="col-md-6">
                    <div class="p-3 bg-light rounded-3">
                        <div class="text-muted small mb-1">Barang</div>
                        <div class="fw-semibold">{{ $peminjaman->barang?->nama_barang ?? 'Tidak ada Barang' }}</div>
                        @if($peminjaman->barang_id)<div class="text-muted small">{{ $peminjaman->barang->kode_barang }}</div>@endif
                    </div>
                </div>
                <div class="col-md-6">
                    <div class="p-3 bg-light rounded-3">
                        <div class="text-muted small mb-1">Ruangan</div>
                        <div class="fw-semibold">{{ $peminjaman->ruangan?->nama_ruangan ?? 'Tidak ada Ruangan' }}</div>
                        @if($peminjaman->ruangan_id)<div class="text-muted small">{{ $peminjaman->ruangan->kode_ruangan }}</div>@endif
                    </div>
                </div>
                <div class="col-md-3">
                    <div class="p-3 bg-light rounded-3">
                        <div class="text-muted small mb-1">Tanggal Pinjam</div>
                        <div class="fw-semibold">{{ \Carbon\Carbon::parse($peminjaman->tgl_peminjaman)->format('d M Y') }}</div>
                    </div>
                </div>
                <div class="col-md-3">
                    <div class="p-3 bg-light rounded-3">
                        <div class="text-muted small mb-1">Jam Pinjam</div>
                        <div class="fw-semibold">{{ $peminjaman->jam_peminjaman }}</div>
                    </div>
                </div>
                <div class="col-md-3">
                    <div class="p-3 bg-light rounded-3">
                        <div class="text-muted small mb-1">Tanggal Kembali</div>
                        <div class="fw-semibold">{{ \Carbon\Carbon::parse($peminjaman->tgl_pengembalian)->format('d M Y') }}</div>
                    </div>
                </div>
                <div class="col-md-3">
                    <div class="p-3 bg-light rounded-3">
                        <div class="text-muted small mb-1">Jam Kembali</div>
                        <div class="fw-semibold">{{ $peminjaman->jam_pengembalian }}</div>
                    </div>
                </div>
                <div class="col-12">
                    <div class="p-3 bg-light rounded-3">
                        <div class="text-muted small mb-1">Keterangan</div>
                        <div>{{ $peminjaman->keterangan ?: '-' }}</div>
                    </div>
                </div>
            </div>

            {{-- Admin action buttons --}}
            @if(Auth::user()->isAdmin())
            <hr class="mt-4">
            <div class="d-flex gap-2 flex-wrap">
                <strong class="me-2 align-self-center">Aksi Admin:</strong>
                @if($peminjaman->status === 'pending')
                <form action="{{ route('peminjaman.verifikasi', $peminjaman->id) }}" method="POST" style="display:inline;">
                    @csrf
                    <button type="submit" class="btn btn-success" onclick="return confirm('Verifikasi peminjaman ini?')">
                        <i class="bi bi-check-circle me-1"></i> Verifikasi
                    </button>
                </form>
                @endif
                @if(in_array($peminjaman->status, ['pending','validasi']))
                <form action="{{ route('peminjaman.tolak', $peminjaman->id) }}" method="POST" style="display:inline;">
                    @csrf
                    <button type="submit" class="btn btn-danger" onclick="return confirm('Tolak peminjaman ini?')">
                        <i class="bi bi-x-circle me-1"></i> Tolak
                    </button>
                </form>
                @endif
                @if($peminjaman->status === 'validasi')
                <form action="{{ route('peminjaman.selesai', $peminjaman->id) }}" method="POST" style="display:inline;">
                    @csrf
                    <button type="submit" class="btn btn-secondary" onclick="return confirm('Tandai selesai?')">
                        <i class="bi bi-flag-fill me-1"></i> Tandai Selesai
                    </button>
                </form>
                @endif
            </div>
            @endif

        </div></div>
    </div></div>
</section>
@endsection
