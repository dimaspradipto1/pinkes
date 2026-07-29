@extends('dashboard.template')

@section('content')
    <div class="pagetitle">
        <h1>Edit Barang</h1>
        <nav>
            <ol class="breadcrumb">
                <li class="breadcrumb-item"><a href="{{ route('dashboard') }}">Home</a></li>
                <li class="breadcrumb-item"><a href="{{ route('barang.index') }}">Data Barang</a></li>
                <li class="breadcrumb-item active">Edit</li>
            </ol>
        </nav>
    </div>

    <section class="section">
        <div class="row">
            <div class="col-lg-8 mx-auto">
                <div class="card shadow-sm">
                    <div class="card-body pt-4">
                        <h5 class="card-title">Form Edit Barang</h5>

                        @if ($errors->any())
                            <div class="alert alert-danger alert-dismissible fade show" role="alert">
                                <strong><i class="bi bi-exclamation-triangle-fill me-1"></i> Terjadi kesalahan:</strong>
                                <ul class="mb-0 mt-1">
                                    @foreach ($errors->all() as $error)
                                        <li>{{ $error }}</li>
                                    @endforeach
                                </ul>
                                <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
                            </div>
                        @endif

                        <form action="{{ route('barang.update', $barang->id) }}" method="POST"
                            enctype="multipart/form-data" class="row g-3">
                            @csrf
                            @method('PUT')

                            <div class="col-md-4">
                                <label for="kode_barang" class="form-label fw-semibold">
                                    Kode Barang <span class="text-danger">*</span>
                                </label>
                                <input type="text"
                                    class="form-control @error('kode_barang') is-invalid @enderror"
                                    id="kode_barang" name="kode_barang"
                                    value="{{ old('kode_barang', $barang->kode_barang) }}"
                                    placeholder="Contoh: BRG-001" required>
                                @error('kode_barang')
                                    <div class="invalid-feedback">{{ $message }}</div>
                                @enderror
                            </div>

                            <div class="col-md-8">
                                <label for="nama_barang" class="form-label fw-semibold">
                                    Nama Barang <span class="text-danger">*</span>
                                </label>
                                <input type="text"
                                    class="form-control @error('nama_barang') is-invalid @enderror"
                                    id="nama_barang" name="nama_barang"
                                    value="{{ old('nama_barang', $barang->nama_barang) }}"
                                    placeholder="Nama lengkap barang" required>
                                @error('nama_barang')
                                    <div class="invalid-feedback">{{ $message }}</div>
                                @enderror
                            </div>

                            <div class="col-md-4">
                                <label for="jumlah" class="form-label fw-semibold">
                                    Jumlah <span class="text-danger">*</span>
                                </label>
                                <input type="number"
                                    class="form-control @error('jumlah') is-invalid @enderror"
                                    id="jumlah" name="jumlah"
                                    value="{{ old('jumlah', $barang->jumlah) }}"
                                    min="1" required>
                                @error('jumlah')
                                    <div class="invalid-feedback">{{ $message }}</div>
                                @enderror
                            </div>

                            <div class="col-md-8">
                                <label for="keterangan" class="form-label fw-semibold">Keterangan</label>
                                <textarea class="form-control @error('keterangan') is-invalid @enderror"
                                    id="keterangan" name="keterangan" rows="2"
                                    placeholder="Deskripsi tambahan (opsional)">{{ old('keterangan', $barang->keterangan) }}</textarea>
                                @error('keterangan')
                                    <div class="invalid-feedback">{{ $message }}</div>
                                @enderror
                            </div>

                            {{-- Foto yang sudah ada --}}
                            @if ($barang->fotos->count() > 0)
                                <div class="col-12">
                                    <label class="form-label fw-semibold">Foto Saat Ini</label>
                                    <div class="d-flex flex-wrap gap-2" id="foto-grid">
                                        @foreach ($barang->fotos as $index => $foto)
                                            <div id="foto-item-{{ $foto->id }}" style="position:relative;">
                                                <img src="{{ asset('storage/' . $foto->nama_foto) }}"
                                                    alt="Foto {{ $index + 1 }}"
                                                    style="width:100px;height:100px;object-fit:cover;border-radius:10px;border:2px solid {{ $index === 0 ? '#0d6efd' : '#dee2e6' }};">
                                                @if ($index === 0)
                                                    <span style="position:absolute;top:4px;left:4px;background:#0d6efd;color:#fff;font-size:10px;padding:2px 6px;border-radius:4px;">Utama</span>
                                                @endif
                                                <button type="button"
                                                    onclick="hapusFoto({{ $foto->id }}, '{{ route('fotobarang.destroy', $foto->id) }}')"
                                                    class="btn btn-danger btn-sm py-0 px-1"
                                                    style="position:absolute;top:4px;right:4px;font-size:11px;line-height:1.6;">
                                                    <i class="bi bi-x"></i>
                                                </button>
                                            </div>
                                        @endforeach
                                    </div>
                                </div>
                            @endif

                            {{-- Upload foto baru --}}
                            <div class="col-12">
                                <label for="foto_barang" class="form-label fw-semibold">
                                    Ganti Semua Foto
                                    <span class="text-muted fw-normal">(opsional)</span>
                                </label>
                                <input type="file"
                                    class="form-control @error('foto_barang.*') is-invalid @enderror"
                                    id="foto_barang" name="foto_barang[]"
                                    accept="image/jpg,image/jpeg,image/png,image/webp"
                                    multiple onchange="previewFotos(this)">
                                @error('foto_barang.*')
                                    <div class="text-danger small mt-1">{{ $message }}</div>
                                @enderror
                                <div class="form-text text-muted">
                                    <i class="bi bi-info-circle me-1"></i>
                                    Jika diisi, semua foto lama akan <strong>dihapus</strong> dan diganti foto baru.
                                    Foto pertama akan jadi foto utama. Kosongkan jika tidak ingin mengganti.
                                </div>
                                <div id="preview-container" class="d-flex flex-wrap gap-2 mt-3"></div>
                            </div>

                            <div class="col-12 d-flex gap-2 mt-3">
                                <button type="submit" class="btn btn-primary px-4">
                                    <i class="bi bi-save me-1"></i> Update
                                </button>
                                <a href="{{ route('barang.index') }}" class="btn btn-secondary px-4">
                                    <i class="bi bi-arrow-left me-1"></i> Kembali
                                </a>
                            </div>
                        </form>
                    </div>
                </div>
            </div>
        </div>
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
                    wrap.innerHTML = `
                        <img src="${e.target.result}" alt="Preview ${index + 1}"
                            style="width:100px;height:100px;object-fit:cover;border-radius:10px;border:2px solid ${index === 0 ? '#0d6efd' : '#dee2e6'};">
                        ${index === 0 ? '<span style="position:absolute;top:4px;left:4px;background:#0d6efd;color:#fff;font-size:10px;padding:2px 6px;border-radius:4px;">Utama</span>' : ''}
                    `;
                    container.appendChild(wrap);
                };
                reader.readAsDataURL(file);
            });
        }
    }

    function hapusFoto(id, url) {
        if (!confirm('Yakin ingin menghapus foto ini?')) return;

        fetch(url, {
            method: 'POST',
            headers: {
                'X-CSRF-TOKEN': '{{ csrf_token() }}',
                'X-HTTP-Method-Override': 'DELETE',
                'Accept': 'application/json',
            },
            body: new URLSearchParams({ _method: 'DELETE' }),
        })
        .then(res => {
            if (res.ok || res.redirected) {
                const el = document.getElementById('foto-item-' + id);
                if (el) el.remove();
            } else {
                alert('Gagal menghapus foto. Silakan coba lagi.');
            }
        })
        .catch(() => alert('Terjadi kesalahan jaringan.'));
    }
</script>
@endpush

