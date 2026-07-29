@extends('dashboard.template')
@section('content')
<div class="pagetitle">
    <h1>Tambah Ruangan</h1>
    <nav><ol class="breadcrumb">
        <li class="breadcrumb-item"><a href="{{ route('dashboard') }}">Home</a></li>
        <li class="breadcrumb-item"><a href="{{ route('ruangan.index') }}">Data Ruangan</a></li>
        <li class="breadcrumb-item active">Tambah</li>
    </ol></nav>
</div>
<section class="section">
    <div class="row"><div class="col-lg-8 mx-auto">
        <div class="card shadow-sm"><div class="card-body pt-4">
            <h5 class="card-title">Form Tambah Ruangan</h5>

            @if ($errors->any())
                <div class="alert alert-danger alert-dismissible fade show">
                    <strong><i class="bi bi-exclamation-triangle-fill me-1"></i> Terjadi kesalahan:</strong>
                    <ul class="mb-0 mt-1">@foreach ($errors->all() as $error)<li>{{ $error }}</li>@endforeach</ul>
                    <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
                </div>
            @endif

            <form action="{{ route('ruangan.store') }}" method="POST" enctype="multipart/form-data" class="row g-3">
                @csrf

                <div class="col-md-4">
                    <label for="kode_ruangan" class="form-label fw-semibold">Kode Ruangan <span class="text-danger">*</span></label>
                    <input type="text" class="form-control @error('kode_ruangan') is-invalid @enderror"
                        id="kode_ruangan" name="kode_ruangan" value="{{ old('kode_ruangan') }}"
                        placeholder="Contoh: RNG-001" required>
                    @error('kode_ruangan')<div class="invalid-feedback">{{ $message }}</div>@enderror
                </div>

                <div class="col-md-6">
                    <label for="nama_ruangan" class="form-label fw-semibold">Nama Ruangan <span class="text-danger">*</span></label>
                    <input type="text" class="form-control @error('nama_ruangan') is-invalid @enderror"
                        id="nama_ruangan" name="nama_ruangan" value="{{ old('nama_ruangan') }}"
                        placeholder="Nama lengkap ruangan" required>
                    @error('nama_ruangan')<div class="invalid-feedback">{{ $message }}</div>@enderror
                </div>

                <div class="col-md-2">
                    <label for="jumlah" class="form-label fw-semibold">Jumlah <span class="text-danger">*</span></label>
                    <input type="number" class="form-control @error('jumlah') is-invalid @enderror"
                        id="jumlah" name="jumlah" value="{{ old('jumlah', 1) }}"
                        min="0" required>
                    @error('jumlah')<div class="invalid-feedback">{{ $message }}</div>@enderror
                </div>

                <div class="col-12">
                    <label for="keterangan" class="form-label fw-semibold">Keterangan</label>
                    <textarea class="form-control @error('keterangan') is-invalid @enderror"
                        id="keterangan" name="keterangan" rows="2"
                        placeholder="Deskripsi tambahan (opsional)">{{ old('keterangan') }}</textarea>
                    @error('keterangan')<div class="invalid-feedback">{{ $message }}</div>@enderror
                </div>

                <div class="col-12">
                    <label for="foto_ruangan" class="form-label fw-semibold">Foto Ruangan <span class="text-muted fw-normal">(opsional)</span></label>
                    <input type="file" class="form-control @error('foto_ruangan.*') is-invalid @enderror"
                        id="foto_ruangan" name="foto_ruangan[]"
                        accept="image/jpg,image/jpeg,image/png,image/webp"
                        multiple onchange="previewFotos(this)">
                    @error('foto_ruangan.*')<div class="text-danger small mt-1">{{ $message }}</div>@enderror
                    <div class="form-text text-muted">
                        <i class="bi bi-info-circle me-1"></i>Pilih satu atau lebih foto. Format: JPG, PNG, WEBP. Foto pertama jadi foto utama.
                    </div>
                    <div id="preview-container" class="d-flex flex-wrap gap-2 mt-3"></div>
                </div>

                <div class="col-12 d-flex gap-2 mt-3">
                    <button type="submit" class="btn btn-primary px-4"><i class="bi bi-save me-1"></i> Simpan</button>
                    <a href="{{ route('ruangan.index') }}" class="btn btn-secondary px-4"><i class="bi bi-arrow-left me-1"></i> Kembali</a>
                </div>
            </form>
        </div></div>
    </div></div>
</section>
@endsection
@push('scripts')
<script>
function previewFotos(input) {
    const container = document.getElementById('preview-container');
    container.innerHTML = '';
    if (input.files && input.files.length > 0) {
        Array.from(input.files).forEach((file, index) => {
            const reader = new FileReader();
            reader.onload = e => {
                const wrap = document.createElement('div');
                wrap.style.cssText = 'position:relative;';
                wrap.innerHTML = `<img src="${e.target.result}" style="width:100px;height:100px;object-fit:cover;border-radius:10px;border:2px solid ${index === 0 ? '#0d6efd' : '#dee2e6'};">
                    ${index === 0 ? '<span style="position:absolute;top:4px;left:4px;background:#0d6efd;color:#fff;font-size:10px;padding:2px 6px;border-radius:4px;">Utama</span>' : ''}`;
                container.appendChild(wrap);
            };
            reader.readAsDataURL(file);
        });
    }
}
</script>
@endpush
