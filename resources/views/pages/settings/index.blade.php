@extends('dashboard.template')
@section('content')
<div class="pagetitle">
    <h1>Pengaturan Pimpinan</h1>
    <nav><ol class="breadcrumb">
        <li class="breadcrumb-item"><a href="{{ route('dashboard') }}">Home</a></li>
        <li class="breadcrumb-item active">Pengaturan Pimpinan</li>
    </ol></nav>
</div>
<section class="section">
    <div class="row"><div class="col-lg-7 mx-auto">
        <div class="card shadow-sm">
            <div class="card-body pt-4">
                <h5 class="card-title">Kustomisasi Penandatangan Laporan</h5>
                <p class="text-muted small mb-4">Ubah nama, NIP, dan jabatan pimpinan yang akan ditampilkan pada tanda tangan rekapitulasi PDF.</p>

                @if($errors->any())
                    <div class="alert alert-danger">
                        <ul class="mb-0">@foreach($errors->all() as $e)<li>{{ $e }}</li>@endforeach</ul>
                    </div>
                @endif

                <form action="{{ route('settings.update') }}" method="POST">
                    @csrf
                    <div class="row g-3">
                        <div class="col-12">
                            <label class="form-label fw-semibold">Nama Pimpinan / Penandatangan</label>
                            <input type="text" name="pimpinan_nama" class="form-control @error('pimpinan_nama') is-invalid @enderror" 
                                placeholder="Contoh: Rina Wijaya, S.Sos" value="{{ old('pimpinan_nama', $nama) }}">
                            @error('pimpinan_nama')<div class="invalid-feedback">{{ $message }}</div>@enderror
                        </div>
                        <div class="col-12">
                            <label class="form-label fw-semibold">NIP Pimpinan / Penandatangan</label>
                            <input type="text" name="pimpinan_nip" class="form-control @error('pimpinan_nip') is-invalid @enderror" 
                                placeholder="Contoh: 198501012010012001" value="{{ old('pimpinan_nip', $nip) }}">
                            @error('pimpinan_nip')<div class="invalid-feedback">{{ $message }}</div>@enderror
                        </div>
                        <div class="col-12">
                            <label class="form-label fw-semibold">Jabatan Pimpinan / Penandatangan</label>
                            <input type="text" name="pimpinan_jabatan" class="form-control @error('pimpinan_jabatan') is-invalid @enderror" 
                                placeholder="Contoh: Kepala Sub Bagian Umum dan Kepegawaian" value="{{ old('pimpinan_jabatan', $jabatan) }}">
                            @error('pimpinan_jabatan')<div class="invalid-feedback">{{ $message }}</div>@enderror
                        </div>
                        <div class="col-12 d-flex gap-2">
                            <button type="submit" class="btn btn-primary px-4">
                                <i class="bi bi-save me-1"></i> Simpan Pengaturan
                            </button>
                            <a href="{{ route('rekapitulasi.index') }}" class="btn btn-secondary px-4">
                                <i class="bi bi-file-earmark-bar-graph me-1"></i> Rekapitulasi
                            </a>
                        </div>
                    </div>
                </form>
            </div>
        </div>
    </div></div>
</section>
@endsection
