@extends('dashboard.template')
@section('content')
<div class="pagetitle">
    <h1>Account Settings</h1>
    <nav><ol class="breadcrumb">
        <li class="breadcrumb-item"><a href="{{ route('dashboard') }}">Home</a></li>
        <li class="breadcrumb-item active">Account Settings</li>
    </ol></nav>
</div>
<section class="section">
    <div class="row"><div class="col-lg-7 mx-auto">
        <div class="card shadow-sm">
            <div class="card-body pt-4">
                <h5 class="card-title">Ganti Password</h5>
                <p class="text-muted small mb-4">Gunakan password yang kuat dan unik untuk keamanan akun Anda.</p>

                @if($errors->any())
                    <div class="alert alert-danger">
                        <ul class="mb-0">@foreach($errors->all() as $e)<li>{{ $e }}</li>@endforeach</ul>
                    </div>
                @endif

                <form action="{{ route('account.password') }}" method="POST">
                    @csrf
                    <div class="row g-3">
                        <div class="col-12">
                            <label class="form-label fw-semibold">Password Lama <span class="text-danger">*</span></label>
                            <input type="password" name="current_password" class="form-control @error('current_password') is-invalid @enderror" required>
                            @error('current_password')<div class="invalid-feedback">{{ $message }}</div>@enderror
                        </div>
                        <div class="col-12">
                            <label class="form-label fw-semibold">Password Baru <span class="text-danger">*</span></label>
                            <input type="password" name="password" class="form-control @error('password') is-invalid @enderror" required minlength="8">
                            <div class="form-text">Minimal 8 karakter.</div>
                            @error('password')<div class="invalid-feedback">{{ $message }}</div>@enderror
                        </div>
                        <div class="col-12">
                            <label class="form-label fw-semibold">Konfirmasi Password Baru <span class="text-danger">*</span></label>
                            <input type="password" name="password_confirmation" class="form-control" required>
                        </div>
                        <div class="col-12 d-flex gap-2">
                            <button type="submit" class="btn btn-primary px-4">
                                <i class="bi bi-shield-lock me-1"></i> Ubah Password
                            </button>
                            <a href="{{ route('profile.show') }}" class="btn btn-secondary px-4">
                                <i class="bi bi-person me-1"></i> My Profile
                            </a>
                        </div>
                    </div>
                </form>
            </div>
        </div>
    </div></div>
</section>
@endsection
