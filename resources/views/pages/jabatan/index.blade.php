@extends('dashboard.template')

@section('content')
    <div class="pagetitle">
        <h1>Data Jabatan</h1>
        <nav>
            <ol class="breadcrumb">
                <li class="breadcrumb-item"><a href="{{ route('dashboard') }}">Home</a></li>
                <li class="breadcrumb-item active">Data Jabatan</li>
            </ol>
        </nav>
    </div>

    <section class="section">
        <div class="row">
            <div class="col-lg-12">
                <div class="card">
                    <div class="card-body">
                        <h5 class="card-title">Data Jabatan</h5>
                        <a href="{{ route('jabatan.create') }}" class="btn btn-primary mb-3">
                            <i class="bi bi-plus-circle me-1"></i> Tambah Jabatan
                        </a>
                        {{ $dataTable->table() }}
                    </div>
                </div>
            </div>
        </div>
    </section>
@endsection

@push('scripts')
    {{ $dataTable->scripts(attributes: ['type' => 'module']) }}
@endpush