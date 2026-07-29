@extends('dashboard.template')
@section('content')
<div class="pagetitle">
    <h1>My Profile</h1>
    <nav><ol class="breadcrumb">
        <li class="breadcrumb-item"><a href="{{ route('dashboard') }}">Home</a></li>
        <li class="breadcrumb-item active">My Profile</li>
    </ol></nav>
</div>
<section class="section">
    <div class="row"><div class="col-lg-8 mx-auto">
        <div class="card shadow-sm">
            <div class="card-body pt-4">
                <div class="d-flex align-items-center gap-4 mb-4">
                    <img src="https://ui-avatars.com/api/?name={{ urlencode($user->name) }}&background=0d6efd&color=fff&size=80&rounded=true&bold=true"
                         alt="Avatar" class="rounded-circle" style="width:80px;height:80px;">
                    <div>
                        <h5 class="mb-1">{{ $user->name }}</h5>
                        <span class="badge bg-secondary">{{ ucfirst($user->role) }}</span>
                        @if($user->jabatan_nama)
                        <div class="text-muted small mt-1">{{ $user->jabatan_nama }}</div>
                        @endif
                    </div>
                </div>

                @if(session('success'))
                    <div class="alert alert-success">{{ session('success') }}</div>
                @endif

                <form action="{{ route('profile.update') }}" method="POST">
                    @csrf
                    <div class="row g-3">
                        <div class="col-md-6">
                            <label class="form-label fw-semibold">Nama Lengkap <span class="text-danger">*</span></label>
                            <input type="text" name="name" class="form-control @error('name') is-invalid @enderror"
                                value="{{ old('name', $user->name) }}" required>
                            @error('name')<div class="invalid-feedback">{{ $message }}</div>@enderror
                        </div>
                        <div class="col-md-6">
                            <label class="form-label fw-semibold">Email</label>
                            <input type="email" class="form-control bg-light" value="{{ $user->email }}" readonly disabled>
                            <div class="form-text">Email tidak dapat diubah.</div>
                        </div>
                        <div class="col-md-6">
                            <label class="form-label fw-semibold">NIP</label>
                            <input type="text" name="nip" class="form-control @error('nip') is-invalid @enderror"
                                value="{{ old('nip', $user->nip) }}" placeholder="Nomor Induk Pegawai">
                            @error('nip')<div class="invalid-feedback">{{ $message }}</div>@enderror
                        </div>
                        <div class="col-md-6">
                            <label class="form-label fw-semibold">Jabatan</label>
                            <input type="text" name="jabatan_nama" class="form-control @error('jabatan_nama') is-invalid @enderror"
                                value="{{ old('jabatan_nama', $user->jabatan_nama) }}" placeholder="Contoh: Kepala Bidang Kesehatan">
                            @error('jabatan_nama')<div class="invalid-feedback">{{ $message }}</div>@enderror
                        </div>
                        <div class="col-md-6">
                            <label class="form-label fw-semibold">Pangkat / Golongan</label>
                            <input type="text" name="pangkat_golongan" class="form-control @error('pangkat_golongan') is-invalid @enderror"
                                value="{{ old('pangkat_golongan', $user->pangkat_golongan) }}" placeholder="Contoh: Penata Tk. I / III-d">
                            @error('pangkat_golongan')<div class="invalid-feedback">{{ $message }}</div>@enderror
                        </div>
                        <div class="col-12">
                            <button type="submit" class="btn btn-primary px-4">
                                <i class="bi bi-save me-1"></i> Simpan Perubahan
                            </button>
                        </div>
                    </div>
                </form>

                <hr class="mt-4">
                <p class="text-muted small">
                    Untuk mengubah password, kunjungi <a href="{{ route('account.setting') }}">Account Settings</a>.
                </p>
            </div>
        </div>
    </div></div>
</section>
@endsection
