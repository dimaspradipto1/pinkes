@extends('dashboard.template')
@section('content')
<div class="pagetitle">
    <h1>Edit Peminjaman</h1>
    <nav><ol class="breadcrumb">
        <li class="breadcrumb-item"><a href="{{ route('dashboard') }}">Home</a></li>
        <li class="breadcrumb-item"><a href="{{ route('peminjaman.index') }}">Data Peminjaman</a></li>
        <li class="breadcrumb-item active">Edit</li>
    </ol></nav>
</div>
<section class="section">
    <div class="row"><div class="col-lg-10 mx-auto">
        <div class="card shadow-sm"><div class="card-body pt-4">
            <h5 class="card-title">Form Edit Peminjaman</h5>

            @if ($errors->any())
                <div class="alert alert-danger alert-dismissible fade show">
                    <strong><i class="bi bi-exclamation-triangle-fill me-1"></i> Terjadi kesalahan:</strong>
                    <ul class="mb-0 mt-1">@foreach ($errors->all() as $error)<li>{{ $error }}</li>@endforeach</ul>
                    <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
                </div>
            @endif

            <form action="{{ route('peminjaman.update', $peminjaman->id) }}" method="POST" class="row g-3">
                @csrf @method('PUT')

                <div class="col-md-6">
                    <label class="form-label fw-semibold">Peminjam</label>
                    <input type="text" class="form-control bg-light" value="{{ $peminjaman->user?->name }}" readonly disabled>
                </div>

                <div class="col-md-6">
                    <label class="form-label fw-semibold">Tujuan Pinjam <span class="text-danger">*</span></label>
                    <select class="form-select @error('pinjam_id') is-invalid @enderror" name="pinjam_id" required>
                        <option value="">-- Pilih Tujuan --</option>
                        @foreach ($pinjams as $pinjam)
                            <option value="{{ $pinjam->id }}" {{ old('pinjam_id', $peminjaman->pinjam_id) == $pinjam->id ? 'selected' : '' }}>{{ $pinjam->tujuan_pinjam }}</option>
                        @endforeach
                    </select>
                    @error('pinjam_id')<div class="invalid-feedback">{{ $message }}</div>@enderror
                </div>

                <div class="col-md-6">
                    <label class="form-label fw-semibold">Barang <span class="text-muted fw-normal">(opsional)</span></label>
                    <select class="form-select @error('barang_id') is-invalid @enderror" name="barang_id">
                        <option value="">-- Pilih Barang --</option>
                        @foreach ($barangs as $barang)
                            <option value="{{ $barang->id }}" {{ old('barang_id', $peminjaman->barang_id) == $barang->id ? 'selected' : '' }}>{{ $barang->kode_barang }} - {{ $barang->nama_barang }}</option>
                        @endforeach
                    </select>
                    @error('barang_id')<div class="invalid-feedback">{{ $message }}</div>@enderror
                </div>

                <div class="col-md-6">
                    <label class="form-label fw-semibold">Ruangan <span class="text-muted fw-normal">(opsional)</span></label>
                    <select class="form-select @error('ruangan_id') is-invalid @enderror" name="ruangan_id">
                        <option value="">-- Pilih Ruangan --</option>
                        @foreach ($ruangans as $ruangan)
                            <option value="{{ $ruangan->id }}" {{ old('ruangan_id', $peminjaman->ruangan_id) == $ruangan->id ? 'selected' : '' }}>{{ $ruangan->kode_ruangan }} - {{ $ruangan->nama_ruangan }}</option>
                        @endforeach
                    </select>
                    @error('ruangan_id')<div class="invalid-feedback">{{ $message }}</div>@enderror
                </div>

                <div class="col-12 mt-0">
                    <div class="alert alert-info py-2 px-3 mb-0" style="font-size: 0.85rem;">
                        <i class="bi bi-info-circle me-1"></i> Anda bisa memilih <strong>Barang saja</strong>, <strong>Ruangan saja</strong>, atau keduanya sekaligus.
                    </div>
                </div>

                <div class="col-md-6">
                    <label class="form-label fw-semibold">Tanggal Peminjaman <span class="text-danger">*</span></label>
                    <input type="date" class="form-control @error('tgl_peminjaman') is-invalid @enderror"
                        id="tgl_peminjaman" name="tgl_peminjaman" value="{{ old('tgl_peminjaman', $peminjaman->tgl_peminjaman) }}" required>
                    @error('tgl_peminjaman')<div class="invalid-feedback">{{ $message }}</div>@enderror
                </div>
                <div class="col-md-6">
                    <label class="form-label fw-semibold">Jam Peminjaman <span class="text-danger">*</span></label>
                    <input type="time" class="form-control @error('jam_peminjaman') is-invalid @enderror"
                        name="jam_peminjaman" value="{{ old('jam_peminjaman', $peminjaman->jam_peminjaman) }}" required>
                    @error('jam_peminjaman')<div class="invalid-feedback">{{ $message }}</div>@enderror
                </div>

                <div class="col-md-6">
                    <label class="form-label fw-semibold">Tanggal Pengembalian <span class="text-danger">*</span></label>
                    <input type="date" class="form-control @error('tgl_pengembalian') is-invalid @enderror"
                        id="tgl_pengembalian" name="tgl_pengembalian" value="{{ old('tgl_pengembalian', $peminjaman->tgl_pengembalian) }}" required>
                    @error('tgl_pengembalian')<div class="invalid-feedback">{{ $message }}</div>@enderror
                </div>
                <div class="col-md-6">
                    <label class="form-label fw-semibold">Jam Pengembalian <span class="text-danger">*</span></label>
                    <input type="time" class="form-control @error('jam_pengembalian') is-invalid @enderror"
                        name="jam_pengembalian" value="{{ old('jam_pengembalian', $peminjaman->jam_pengembalian) }}" required>
                    @error('jam_pengembalian')<div class="invalid-feedback">{{ $message }}</div>@enderror
                </div>

                <div class="col-12">
                    <label class="form-label fw-semibold">Keterangan</label>
                    <textarea class="form-control" name="keterangan" rows="2">{{ old('keterangan', $peminjaman->keterangan) }}</textarea>
                </div>

                <div class="col-12 d-flex gap-2 mt-3">
                    <button type="submit" class="btn btn-primary px-4"><i class="bi bi-save me-1"></i> Update Data</button>
                    <a href="{{ route('peminjaman.index') }}" class="btn btn-secondary px-4"><i class="bi bi-arrow-left me-1"></i> Kembali</a>
                </div>
            </form>
        </div></div>
    </div></div>
</section>
@endsection
@push('scripts')
<script>
// Sync Tanggal Pengembalian agar tidak sebelum Tanggal Peminjaman
const tglPinjam = document.getElementById('tgl_peminjaman');
const tglKembali = document.getElementById('tgl_pengembalian');

if (tglPinjam && tglKembali) {
    tglPinjam.addEventListener('change', function() {
        tglKembali.min = this.value;
        if (tglKembali.value < this.value) tglKembali.value = this.value;
    });
    tglKembali.min = tglPinjam.value;
}
</script>
@endpush
