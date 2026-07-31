@extends('dashboard.template')
@section('content')
<div class="pagetitle">
    <h1>Tambah Peminjaman</h1>
    <nav><ol class="breadcrumb">
        <li class="breadcrumb-item"><a href="{{ route('dashboard') }}">Home</a></li>
        <li class="breadcrumb-item"><a href="{{ route('peminjaman.index') }}">Data Peminjaman</a></li>
        <li class="breadcrumb-item active">Tambah</li>
    </ol></nav>
</div>
<section class="section">
    <div class="row"><div class="col-lg-10 mx-auto">
        <div class="card shadow-sm"><div class="card-body pt-4">
            <h5 class="card-title">Form Tambah Peminjaman</h5>

            @if ($errors->any())
                <div class="alert alert-danger alert-dismissible fade show">
                    <strong><i class="bi bi-exclamation-triangle-fill me-1"></i> Terjadi kesalahan:</strong>
                    <ul class="mb-0 mt-1">@foreach ($errors->all() as $error)<li>{{ $error }}</li>@endforeach</ul>
                    <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
                </div>
            @endif

            <form action="{{ route('peminjaman.store') }}" method="POST" class="row g-3">
                @csrf

                {{-- Peminjam (Otomatis User Login) --}}
                <div class="col-md-6">
                    <label class="form-label fw-semibold">Peminjam</label>
                    <input type="text" class="form-control bg-light" value="{{ auth()->user()->name }}" readonly disabled>
                </div>

                {{-- Tujuan Pinjam --}}
                <div class="col-md-6">
                    <label for="pinjam_id" class="form-label fw-semibold">Tujuan Pinjam <span class="text-danger">*</span></label>
                    <select class="form-select @error('pinjam_id') is-invalid @enderror" id="pinjam_id" name="pinjam_id" required>
                        <option value="">-- Pilih Tujuan --</option>
                        @foreach ($pinjams as $pinjam)
                            <option value="{{ $pinjam->id }}" {{ old('pinjam_id') == $pinjam->id ? 'selected' : '' }}>{{ $pinjam->tujuan_pinjam }}</option>
                        @endforeach
                    </select>
                    @error('pinjam_id')<div class="invalid-feedback">{{ $message }}</div>@enderror
                </div>

                {{-- Barang --}}
                <div class="col-md-6">
                    <label for="barang_id" class="form-label fw-semibold">Barang <span class="text-muted fw-normal">(opsional)</span></label>
                    <select class="form-select @error('barang_id') is-invalid @enderror" id="barang_id" name="barang_id">
                        <option value="">-- Pilih Barang --</option>
                        @foreach ($barangs as $barang)
                            <option value="{{ $barang->id }}" {{ old('barang_id') == $barang->id ? 'selected' : '' }}>{{ $barang->kode_barang }} - {{ $barang->nama_barang }}</option>
                        @endforeach
                    </select>
                    @error('barang_id')<div class="invalid-feedback">{{ $message }}</div>@enderror
                </div>

                {{-- Jumlah Pinjam --}}
                <div class="col-md-6">
                    <label for="jumlah_pinjam" class="form-label fw-semibold">Jumlah Pinjam Barang <span class="text-danger">*</span></label>
                    <input type="number" class="form-control @error('jumlah_pinjam') is-invalid @enderror" 
                        id="jumlah_pinjam" name="jumlah_pinjam" min="1" value="{{ old('jumlah_pinjam', 1) }}" required>
                    @error('jumlah_pinjam')<div class="invalid-feedback">{{ $message }}</div>@enderror
                </div>

                {{-- Ruangan --}}
                <div class="col-md-6">
                    <label for="ruangan_id" class="form-label fw-semibold">Ruangan <span class="text-muted fw-normal">(opsional)</span></label>
                    <select class="form-select @error('ruangan_id') is-invalid @enderror" id="ruangan_id" name="ruangan_id">
                        <option value="">-- Pilih Ruangan --</option>
                        @foreach ($ruangans as $ruangan)
                            <option value="{{ $ruangan->id }}" {{ old('ruangan_id') == $ruangan->id ? 'selected' : '' }}>{{ $ruangan->kode_ruangan }} - {{ $ruangan->nama_ruangan }}</option>
                        @endforeach
                    </select>
                    @error('ruangan_id')<div class="invalid-feedback">{{ $message }}</div>@enderror
                    <div class="form-text text-muted small mt-1">
                        <i class="bi bi-info-circle me-1"></i> Ruangan yang sedang aktif dipinjam/diajukan tidak akan muncul di sini.
                    </div>
                </div>
                
                <div class="col-12 mt-0">
                    <div class="alert alert-info py-2 px-3 mb-0" style="font-size: 0.85rem;">
                        <i class="bi bi-info-circle me-1"></i> Anda bisa memilih <strong>Barang saja</strong>, <strong>Ruangan saja</strong>, atau keduanya sekaligus.
                    </div>
                </div>

                {{-- Tanggal & Jam Peminjaman --}}
                <div class="col-md-6">
                    <label class="form-label fw-semibold">Tanggal Peminjaman <span class="text-danger">*</span></label>
                    <input type="date" class="form-control @error('tgl_peminjaman') is-invalid @enderror"
                        id="tgl_peminjaman" name="tgl_peminjaman" value="{{ old('tgl_peminjaman', date('Y-m-d')) }}" required>
                    @error('tgl_peminjaman')<div class="invalid-feedback">{{ $message }}</div>@enderror
                </div>
                <div class="col-md-6">
                    <label class="form-label fw-semibold">Jam Peminjaman <span class="text-danger">*</span></label>
                    <input type="time" class="form-control @error('jam_peminjaman') is-invalid @enderror"
                        name="jam_peminjaman" value="{{ old('jam_peminjaman', date('H:i')) }}" required>
                    @error('jam_peminjaman')<div class="invalid-feedback">{{ $message }}</div>@enderror
                </div>

                {{-- Tanggal & Jam Pengembalian --}}
                <div class="col-md-6">
                    <label class="form-label fw-semibold">Tanggal Pengembalian <span class="text-danger">*</span></label>
                    <input type="date" class="form-control @error('tgl_pengembalian') is-invalid @enderror"
                        id="tgl_pengembalian" name="tgl_pengembalian" value="{{ old('tgl_pengembalian', date('Y-m-d')) }}" required>
                    @error('tgl_pengembalian')<div class="invalid-feedback">{{ $message }}</div>@enderror
                </div>
                <div class="col-md-6">
                    <label class="form-label fw-semibold">Jam Pengembalian <span class="text-danger">*</span></label>
                    <input type="time" class="form-control @error('jam_pengembalian') is-invalid @enderror"
                        name="jam_pengembalian" value="{{ old('jam_pengembalian') }}" required>
                    @error('jam_pengembalian')<div class="invalid-feedback">{{ $message }}</div>@enderror
                </div>

                {{-- Keterangan --}}
                <div class="col-12">
                    <label for="keterangan" class="form-label fw-semibold">Keterangan</label>
                    <textarea class="form-control" id="keterangan" name="keterangan" rows="2"
                        placeholder="Catatan tambahan (opsional)">{{ old('keterangan') }}</textarea>
                </div>

                <div class="col-12 d-flex gap-2 mt-3">
                    <button type="submit" class="btn btn-primary px-4"><i class="bi bi-send me-1"></i> Submit Peminjaman</button>
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
