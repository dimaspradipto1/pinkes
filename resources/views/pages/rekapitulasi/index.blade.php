@extends('dashboard.template')
@section('content')
<div class="pagetitle">
    <h1>Rekapitulasi Peminjaman</h1>
    <nav><ol class="breadcrumb">
        <li class="breadcrumb-item"><a href="{{ route('dashboard') }}">Home</a></li>
        <li class="breadcrumb-item active">Rekapitulasi</li>
    </ol></nav>
</div>
<section class="section">
    <div class="row"><div class="col-lg-12">

        {{-- Filter Card --}}
        <div class="card shadow-sm mb-4">
            <div class="card-body pt-4">
                <form action="{{ route('rekapitulasi.index') }}" method="GET" id="filter-form" class="row g-3 align-items-end">
                    <div class="col-md-3">
                        <label for="tgl_mulai" class="form-label fw-bold">Dari Tanggal</label>
                        <input type="date" name="tgl_mulai" id="tgl_mulai" class="form-control" value="{{ request('tgl_mulai') }}">
                    </div>
                    <div class="col-md-3">
                        <label for="tgl_selesai" class="form-label fw-bold">Sampai Tanggal</label>
                        <input type="date" name="tgl_selesai" id="tgl_selesai" class="form-control" value="{{ request('tgl_selesai') }}">
                    </div>
                    <div class="col-md-3">
                        <label for="jenis" class="form-label fw-bold">Jenis</label>
                        <select name="jenis" id="jenis" class="form-select">
                            <option value="semua" {{ $jenis === 'semua' ? 'selected' : '' }}>Semua</option>
                            <option value="barang"  {{ $jenis === 'barang'  ? 'selected' : '' }}>Barang Saja</option>
                            <option value="ruangan" {{ $jenis === 'ruangan' ? 'selected' : '' }}>Ruangan Saja</option>
                        </select>
                    </div>
                    <div class="col-md-3 d-flex gap-2">
                        <button type="submit" class="btn btn-primary flex-grow-1">
                            <i class="bi bi-filter"></i> Filter
                        </button>
                        <a href="{{ route('rekapitulasi.index') }}" class="btn btn-secondary">
                            <i class="bi bi-arrow-clockwise"></i>
                        </a>
                        @if(Auth::user()->isAdmin())
                        <button type="button" onclick="exportPdf()" class="btn btn-danger">
                            <i class="bi bi-file-earmark-pdf"></i> PDF
                        </button>
                        @endif
                    </div>

                    {{-- Opsi Kustomisasi Tanda Tangan (Hanya Admin) --}}
                    @if(Auth::user()->isAdmin())
                    <div class="col-12 mt-3 pt-2 border-top">
                        <div class="row g-3">
                            <div class="col-md-4">
                                <label for="nama_pimpinan" class="form-label fw-semibold small text-muted">Nama Pimpinan / Penandatangan</label>
                                <input type="text" name="nama_pimpinan" id="nama_pimpinan" class="form-control form-control-sm"
                                    placeholder="Contoh: Rina Wijaya, S.Sos" value="{{ request('nama_pimpinan') }}">
                            </div>
                            <div class="col-md-4">
                                <label for="nip_pimpinan" class="form-label fw-semibold small text-muted">NIP Pimpinan / Penandatangan</label>
                                <input type="text" name="nip_pimpinan" id="nip_pimpinan" class="form-control form-control-sm"
                                    placeholder="Contoh: 198501012010012001" value="{{ request('nip_pimpinan') }}">
                            </div>
                            <div class="col-md-4">
                                <label for="jabatan_pimpinan" class="form-label fw-semibold small text-muted">Jabatan Pimpinan / Penandatangan</label>
                                <input type="text" name="jabatan_pimpinan" id="jabatan_pimpinan" class="form-control form-control-sm"
                                    placeholder="Contoh: Kepala Sub Bagian Umum dan Kepegawaian" value="{{ request('jabatan_pimpinan') }}">
                            </div>
                        </div>
                    </div>
                    @endif
                </form>
            </div>
        </div>

        {{-- Data Table --}}
        <div class="card shadow-sm">
            <div class="card-body pt-4">
                <div class="d-flex justify-content-between align-items-center mb-3">
                    <h5 class="card-title mb-0">
                        Data Rekapitulasi
                        <span class="badge bg-secondary ms-2">{{ $peminjamen->count() }} data</span>
                    </h5>
                </div>
                <div class="table-responsive">
                    <table class="table table-hover table-bordered datatable">
                        <thead class="table-light">
                            <tr class="text-center align-middle">
                                <th width="50">No</th>
                                @if(Auth::user()->isAdmin())
                                <th>Peminjam</th>
                                @endif
                                <th>Jenis</th>
                                <th>Barang / Ruangan</th>
                                <th>Tgl Pinjam</th>
                                <th>Tgl Kembali</th>
                                <th>Tujuan</th>
                                <th>Status</th>
                            </tr>
                        </thead>
                        <tbody>
                            @forelse ($peminjamen as $item)
                                <tr>
                                    <td class="text-center">{{ $loop->iteration }}</td>
                                    @if(Auth::user()->isAdmin())
                                    <td>{{ $item->user?->name ?? '-' }}</td>
                                    @endif
                                    <td class="text-center">
                                        @if($item->barang_id)
                                            <span class="badge bg-info text-dark">Barang</span>
                                        @else
                                            <span class="badge bg-secondary">Ruangan</span>
                                        @endif
                                    </td>
                                    <td>
                                        @if($item->barang_id)
                                            <small class="text-muted">{{ $item->barang?->kode_barang }}</small><br>
                                            {{ $item->barang?->nama_barang ?? 'Barang Dihapus' }}
                                        @else
                                            <small class="text-muted">{{ $item->ruangan?->kode_ruangan }}</small><br>
                                            {{ $item->ruangan?->nama_ruangan ?? 'Ruangan Dihapus' }}
                                        @endif
                                    </td>
                                    <td class="text-center">
                                        {{ \Carbon\Carbon::parse($item->tgl_peminjaman)->format('d/m/Y') }}<br>
                                        <small class="text-muted">{{ $item->jam_peminjaman }}</small>
                                    </td>
                                    <td class="text-center">
                                        {{ \Carbon\Carbon::parse($item->tgl_pengembalian)->format('d/m/Y') }}<br>
                                        <small class="text-muted">{{ $item->jam_pengembalian }}</small>
                                    </td>
                                    <td>{{ $item->pinjam?->tujuan_pinjam ?? '-' }}</td>
                                    <td class="text-center">
                                        @php $sMap = ['pending'=>['warning','text-dark','Menunggu'],'validasi'=>['primary','text-white','Divalidasi'],'ditolak'=>['danger','text-white','Ditolak'],'selesai'=>['success','text-white','Selesai']]; $s = $sMap[$item->status] ?? ['secondary','text-white',$item->status]; @endphp
                                        <span class="badge bg-{{ $s[0] }} {{ $s[1] }}">{{ $s[2] }}</span>
                                    </td>
                                </tr>
                            @empty
                                <tr><td colspan="{{ Auth::user()->isAdmin() ? 8 : 7 }}" class="text-center py-4 text-muted">
                                    <i class="bi bi-inbox fs-3 d-block mb-2"></i>Tidak ada data untuk filter yang dipilih.
                                </td></tr>
                            @endforelse
                        </tbody>
                    </table>
                </div>
            </div>
        </div>
    </div></div>
</section>
@endsection

@push('scripts')
@if(Auth::user()->isAdmin())
<script>
function exportPdf() {
    const form = document.getElementById('filter-form');
    if (!form) return;
    
    const originalAction = form.action;
    const originalTarget = form.target;
    
    form.action = "{{ route('rekapitulasi.pdf') }}";
    form.target = "_blank";
    form.submit();
    
    // Restore original action & target
    form.action = originalAction;
    form.target = originalTarget;
}
</script>
@endif
@endpush
