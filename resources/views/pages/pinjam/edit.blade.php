@extends('dashboard.template')

@section('content')
    <div class="pagetitle">
        <h1>Edit Tujuan Pinjam</h1>
        <nav>
            <ol class="breadcrumb">
                <li class="breadcrumb-item"><a href="{{ route('dashboard') }}">Home</a></li>
                <li class="breadcrumb-item"><a href="{{ route('pinjam.index') }}">Daftar Tujuan Pinjam</a></li>
                <li class="breadcrumb-item active">Edit</li>
            </ol>
        </nav>
    </div>

    <section class="section">
        <div class="row">
            <div class="col-lg-6 mx-auto">
                <div class="card shadow-sm">
                    <div class="card-body pt-4">
                        <h5 class="card-title">Form Edit Tujuan Pinjam</h5>

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

                        <form action="{{ route('pinjam.update', $pinjam->id) }}" method="POST" class="row g-3">
                            @csrf
                            @method('PUT')

                            <div class="col-12">
                                <label for="tujuan_pinjam" class="form-label fw-semibold">
                                    Tujuan Pinjam <span class="text-danger">*</span>
                                </label>
                                <input type="text"
                                    class="form-control @error('tujuan_pinjam') is-invalid @enderror"
                                    id="tujuan_pinjam" name="tujuan_pinjam"
                                    value="{{ old('tujuan_pinjam', $pinjam->tujuan_pinjam) }}"
                                    placeholder="Contoh: Kebutuhan Dinas, Acara Resmi, dll."
                                    required>
                                @error('tujuan_pinjam')
                                    <div class="invalid-feedback">{{ $message }}</div>
                                @enderror
                            </div>

                            <div class="col-12 d-flex gap-2 mt-3">
                                <button type="submit" class="btn btn-primary px-4">
                                    <i class="bi bi-save me-1"></i> Update
                                </button>
                                <a href="{{ route('pinjam.index') }}" class="btn btn-secondary px-4">
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
