@extends('dashboard.template')
@section('content')
<div class="pagetitle">
    <h1>Detail Ruangan</h1>
    <nav><ol class="breadcrumb">
        <li class="breadcrumb-item"><a href="{{ route('dashboard') }}">Home</a></li>
        <li class="breadcrumb-item"><a href="{{ route('ruangan.index') }}">Data Ruangan</a></li>
        <li class="breadcrumb-item active">Detail</li>
    </ol></nav>
</div>
<section class="section">
    <div class="row"><div class="col-lg-8 mx-auto">
        <div class="card shadow-sm"><div class="card-body pt-4">

            <div class="d-flex justify-content-between align-items-center mb-4">
                <h5 class="card-title mb-0">Detail Ruangan</h5>
                <div class="d-flex gap-2">
                    <a href="{{ route('ruangan.edit', $ruangan->id) }}" class="btn btn-warning btn-sm text-white">
                        <i class="bi bi-pencil-square me-1"></i> Edit
                    </a>
                    <a href="{{ route('ruangan.index') }}" class="btn btn-secondary btn-sm">
                        <i class="bi bi-arrow-left me-1"></i> Kembali
                    </a>
                </div>
            </div>

            <div class="row g-3 mb-4">
                <div class="col-md-4">
                    <div class="p-3 bg-light rounded-3">
                        <div class="text-muted small mb-1">Kode Ruangan</div>
                        <div class="fw-semibold fs-6">{{ $ruangan->kode_ruangan }}</div>
                    </div>
                </div>
                <div class="col-md-8">
                    <div class="p-3 bg-light rounded-3">
                        <div class="text-muted small mb-1">Nama Ruangan</div>
                        <div class="fw-semibold fs-6">{{ $ruangan->nama_ruangan }}</div>
                    </div>
                </div>
                <div class="col-12">
                    <div class="p-3 bg-light rounded-3">
                        <div class="text-muted small mb-1">Keterangan</div>
                        <div>{{ $ruangan->keterangan ?: '-' }}</div>
                    </div>
                </div>
                <div class="col-md-6">
                    <div class="p-3 bg-light rounded-3">
                        <div class="text-muted small mb-1">Dibuat</div>
                        <div>{{ $ruangan->created_at->format('d M Y, H:i') }}</div>
                    </div>
                </div>
                <div class="col-md-6">
                    <div class="p-3 bg-light rounded-3">
                        <div class="text-muted small mb-1">Terakhir Diperbarui</div>
                        <div>{{ $ruangan->updated_at->format('d M Y, H:i') }}</div>
                    </div>
                </div>
            </div>

            <hr>
            <h6 class="fw-semibold mb-3">
                <i class="bi bi-images me-1 text-primary"></i> Galeri Foto
                <span class="badge bg-primary ms-1">{{ $ruangan->fotos->count() }}</span>
            </h6>

            @if ($ruangan->fotos->count() > 0)
                <div class="row g-3">
                    @foreach ($ruangan->fotos as $index => $foto)
                        <div class="col-6 col-md-3">
                            <div style="position:relative;overflow:hidden;border-radius:12px;border:2px solid {{ $index === 0 ? '#0d6efd' : '#e2e8f0' }};aspect-ratio:1/1;">
                                <img src="{{ asset('storage/' . $foto->nama_foto) }}"
                                    alt="Foto {{ $index + 1 }}"
                                    style="width:100%;height:100%;object-fit:cover;cursor:zoom-in;"
                                    onclick="openLightbox('{{ asset('storage/' . $foto->nama_foto) }}')"
                                    onerror="this.style.display='none'">
                                @if ($index === 0)
                                    <div style="position:absolute;top:6px;left:6px;background:#0d6efd;color:#fff;font-size:10px;padding:2px 8px;border-radius:4px;font-weight:600;">Utama</div>
                                @endif
                            </div>
                        </div>
                    @endforeach
                </div>
            @else
                <div class="text-center py-4 text-muted">
                    <i class="bi bi-image" style="font-size:2.5rem;"></i>
                    <p class="mt-2 mb-0">Belum ada foto untuk ruangan ini.</p>
                    <a href="{{ route('ruangan.edit', $ruangan->id) }}" class="btn btn-sm btn-outline-primary mt-2">
                        <i class="bi bi-upload me-1"></i> Upload Foto
                    </a>
                </div>
            @endif
        </div></div>
    </div></div>
</section>

<div id="lightbox" onclick="closeLightbox()"
    style="display:none;position:fixed;inset:0;background:rgba(0,0,0,.85);z-index:9999;align-items:center;justify-content:center;cursor:zoom-out;">
    <img id="lightbox-img" src="" alt="Preview" style="max-width:90vw;max-height:90vh;border-radius:12px;">
</div>
@endsection
@push('scripts')
<script>
function openLightbox(src) { document.getElementById('lightbox-img').src = src; document.getElementById('lightbox').style.display = 'flex'; }
function closeLightbox() { document.getElementById('lightbox').style.display = 'none'; }
document.addEventListener('keydown', e => { if (e.key === 'Escape') closeLightbox(); });
</script>
@endpush
