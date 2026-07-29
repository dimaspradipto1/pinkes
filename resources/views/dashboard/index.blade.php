@extends('dashboard.template')

@section('content')
<div class="pagetitle">
    <h1>Dashboard</h1>
    <nav>
        <ol class="breadcrumb">
            <li class="breadcrumb-item"><a href="{{ route('dashboard') }}">Home</a></li>
            <li class="breadcrumb-item active">Dashboard</li>
        </ol>
    </nav>
</div>

<style>
    /* ── Stat Cards ───────────────────────────────── */
    .stat-card {
        border: none;
        border-radius: 16px;
        overflow: hidden;
        position: relative;
        transition: transform .25s ease, box-shadow .25s ease;
        box-shadow: 0 4px 20px rgba(0,0,0,.08);
    }
    .stat-card:hover {
        transform: translateY(-4px);
        box-shadow: 0 12px 32px rgba(0,0,0,.14);
    }
    .stat-card .card-body { padding: 1.5rem 1.5rem 1.2rem; }
    .stat-card .stat-icon {
        width: 56px; height: 56px;
        border-radius: 14px;
        display: flex; align-items: center; justify-content: center;
        font-size: 1.6rem;
        flex-shrink: 0;
    }
    .stat-card .stat-label { font-size: .8rem; font-weight: 600; letter-spacing: .04em; text-transform: uppercase; opacity: .75; }
    .stat-card .stat-value { font-size: 2.2rem; font-weight: 700; line-height: 1.1; }
    .stat-card .stat-sub  { font-size: .78rem; margin-top: .3rem; opacity: .7; }

    /* colour skins */
    .skin-blue   { background: linear-gradient(135deg, #4f8ef7 0%, #1a5ff0 100%); color:#fff; }
    .skin-green  { background: linear-gradient(135deg, #3ecf8e 0%, #1faa6b 100%); color:#fff; }
    .skin-orange { background: linear-gradient(135deg, #ff9a3c 0%, #e0670d 100%); color:#fff; }
    .skin-purple { background: linear-gradient(135deg, #a78bfa 0%, #7c3aed 100%); color:#fff; }

    .skin-blue   .stat-icon { background: rgba(255,255,255,.22); color:#fff; }
    .skin-green  .stat-icon { background: rgba(255,255,255,.22); color:#fff; }
    .skin-orange .stat-icon { background: rgba(255,255,255,.22); color:#fff; }
    .skin-purple .stat-icon { background: rgba(255,255,255,.22); color:#fff; }

    /* ── Chart Card ───────────────────────────────── */
    .chart-card {
        border: none;
        border-radius: 16px;
        box-shadow: 0 4px 20px rgba(0,0,0,.07);
    }
    .chart-card .card-header {
        background: transparent;
        border-bottom: 1px solid #f0f0f0;
        padding: 1.15rem 1.5rem;
    }
    .chart-card .card-title {
        font-size: 1rem;
        font-weight: 700;
        margin: 0;
        color: #333;
    }
    .chart-card .card-body { padding: 1.25rem 1.5rem 1.5rem; }

    /* ── Recent Table ─────────────────────────────── */
    .recent-card {
        border: none;
        border-radius: 16px;
        box-shadow: 0 4px 20px rgba(0,0,0,.07);
    }
    .recent-card thead th {
        background: #f8f9fc;
        font-size: .78rem;
        font-weight: 700;
        letter-spacing: .05em;
        text-transform: uppercase;
        color: #6c757d;
        border: none;
        padding: .85rem 1rem;
    }
    .recent-card tbody tr { transition: background .15s; }
    .recent-card tbody tr:hover { background: #f4f7ff; }
    .recent-card tbody td {
        vertical-align: middle;
        padding: .75rem 1rem;
        border-bottom: 1px solid #f2f2f2;
        font-size: .88rem;
    }
    .recent-card tbody tr:last-child td { border-bottom: none; }

    .avatar-sm {
        width: 38px; height: 38px;
        border-radius: 10px;
        object-fit: cover;
    }

    /* ── Quick Links ──────────────────────────────── */
    .quick-link {
        border: none;
        border-radius: 14px;
        transition: transform .2s, box-shadow .2s;
        box-shadow: 0 3px 14px rgba(0,0,0,.07);
        text-decoration: none;
    }
    .quick-link:hover { transform: translateY(-3px); box-shadow: 0 8px 22px rgba(0,0,0,.13); }
    .quick-link .card-body { padding: 1.3rem 1rem; }
    .quick-link i { font-size: 1.8rem; }

    /* ── Welcome Banner ───────────────────────────── */
    .welcome-banner {
        background: linear-gradient(135deg, #1a5ff0 0%, #7c3aed 100%);
        border-radius: 16px;
        color: #fff;
        padding: 1.8rem 2rem;
        position: relative;
        overflow: hidden;
        box-shadow: 0 8px 28px rgba(79,143,247,.35);
        margin-bottom: 1.5rem;
    }
    .welcome-banner::before {
        content: '';
        position: absolute; top: -40px; right: -40px;
        width: 180px; height: 180px;
        background: rgba(255,255,255,.08);
        border-radius: 50%;
    }
    .welcome-banner::after {
        content: '';
        position: absolute; bottom: -60px; right: 80px;
        width: 240px; height: 240px;
        background: rgba(255,255,255,.05);
        border-radius: 50%;
    }
    .welcome-banner h4 { font-weight: 700; font-size: 1.35rem; margin-bottom: .3rem; }
    .welcome-banner p  { opacity: .85; margin: 0; font-size: .92rem; }
    .welcome-banner .date-badge {
        background: rgba(255,255,255,.2);
        border-radius: 30px;
        padding: .4rem 1rem;
        font-size: .82rem;
        display: inline-flex; align-items: center; gap: .4rem;
        margin-bottom: .8rem;
    }
</style>

<section class="section dashboard">

    <div class="welcome-banner">
        <div class="date-badge"><i class="bi bi-calendar3"></i> {{ now()->translatedFormat('l, d F Y') }}</div>
        <h4>Selamat datang, {{ Auth::user()->name }}! 👋</h4>
        <p>Sistem Informasi Peminjaman Ruangan dan Barang <strong>Dinas Kesehatan Kota Batam</strong>.</p>
    </div>

    {{-- ── Stat Cards ──────────────────────────────────── --}}
    <div class="row g-3 mb-4">
      @if(Auth::user()->isAdmin())
        <div class="col-6 col-lg-3">
            <div class="card stat-card skin-blue h-100">
                <div class="card-body d-flex align-items-center gap-3">
                    <div class="stat-icon"><i class="bi bi-archive-fill"></i></div>
                    <div>
                        <div class="stat-label">Total Barang</div>
                        <div class="stat-value">{{ $totalBarang }}</div>
                        <div class="stat-sub">item terdaftar</div>
                    </div>
                </div>
            </div>
        </div>
        <div class="col-6 col-lg-3">
            <div class="card stat-card skin-green h-100">
                <div class="card-body d-flex align-items-center gap-3">
                    <div class="stat-icon"><i class="bi bi-building"></i></div>
                    <div>
                        <div class="stat-label">Total Ruangan</div>
                        <div class="stat-value">{{ $totalRuangan }}</div>
                        <div class="stat-sub">ruangan aktif</div>
                    </div>
                </div>
            </div>
        </div>
        <div class="col-6 col-lg-3">
            <div class="card stat-card skin-orange h-100">
                <div class="card-body d-flex align-items-center gap-3">
                    <div class="stat-icon"><i class="bi bi-journal-check"></i></div>
                    <div>
                        <div class="stat-label">Total Peminjaman</div>
                        <div class="stat-value">{{ $totalPeminjaman }}</div>
                        <div class="stat-sub">transaksi</div>
                    </div>
                </div>
            </div>
        </div>
        <div class="col-6 col-lg-3">
            <div class="card stat-card skin-purple h-100">
                <div class="card-body d-flex align-items-center gap-3">
                    <div class="stat-icon"><i class="bi bi-hourglass-split"></i></div>
                    <div>
                        <div class="stat-label">Menunggu Verifikasi</div>
                        <div class="stat-value">{{ $totalMenunggu }}</div>
                        <div class="stat-sub">perlu tindakan</div>
                    </div>
                </div>
            </div>
        </div>
      @else
        <div class="col-6 col-lg-3">
            <div class="card stat-card skin-blue h-100">
                <div class="card-body d-flex align-items-center gap-3">
                    <div class="stat-icon"><i class="bi bi-journal-check"></i></div>
                    <div>
                        <div class="stat-label">Total Saya</div>
                        <div class="stat-value">{{ $myTotal }}</div>
                        <div class="stat-sub">peminjaman saya</div>
                    </div>
                </div>
            </div>
        </div>
        <div class="col-6 col-lg-3">
            <div class="card stat-card skin-orange h-100">
                <div class="card-body d-flex align-items-center gap-3">
                    <div class="stat-icon"><i class="bi bi-hourglass-split"></i></div>
                    <div>
                        <div class="stat-label">Menunggu</div>
                        <div class="stat-value">{{ $myPending }}</div>
                        <div class="stat-sub">belum diverifikasi</div>
                    </div>
                </div>
            </div>
        </div>
        <div class="col-6 col-lg-3">
            <div class="card stat-card skin-green h-100">
                <div class="card-body d-flex align-items-center gap-3">
                    <div class="stat-icon"><i class="bi bi-check-circle-fill"></i></div>
                    <div>
                        <div class="stat-label">Divalidasi</div>
                        <div class="stat-value">{{ $myValidasi }}</div>
                        <div class="stat-sub">sedang berjalan</div>
                    </div>
                </div>
            </div>
        </div>
        <div class="col-6 col-lg-3">
            <div class="card stat-card skin-purple h-100">
                <div class="card-body d-flex align-items-center gap-3">
                    <div class="stat-icon"><i class="bi bi-flag-fill"></i></div>
                    <div>
                        <div class="stat-label">Selesai</div>
                        <div class="stat-value">{{ $mySelesai }}</div>
                        <div class="stat-sub">sudah dikembalikan</div>
                    </div>
                </div>
            </div>
        </div>
      @endif
    </div>

    <div class="row g-3">
        {{-- ── Chart Peminjaman Bulanan ─────────────────── --}}
        <div class="col-lg-8">
            <div class="card chart-card h-100">
                <div class="card-header d-flex align-items-center justify-content-between">
                    <h5 class="card-title"><i class="bi bi-bar-chart-fill text-primary me-2"></i>Tren Peminjaman (12 Bulan)</h5>
                </div>
                <div class="card-body">
                    <div id="peminjamanChart"></div>
                </div>
            </div>
        </div>

        {{-- ── Quick Links ──────────────────────────────── --}}
        <div class="col-lg-4">
            <div class="card chart-card h-100">
                <div class="card-header">
                    <h5 class="card-title"><i class="bi bi-lightning-charge-fill text-warning me-2"></i>Menu Cepat</h5>
                </div>
                <div class="card-body d-flex flex-column gap-2">
                    @if(Auth::user()->isAdmin())
                    <a href="{{ route('barang.index') }}" class="card quick-link text-decoration-none">
                        <div class="card-body d-flex align-items-center gap-3">
                            <div class="stat-icon skin-blue rounded-3" style="width:46px;height:46px;font-size:1.3rem;"><i class="bi bi-archive-fill text-white"></i></div>
                            <div><div class="fw-700" style="font-weight:700;">Data Barang</div><div class="text-muted" style="font-size:.8rem;">Kelola inventaris barang</div></div>
                            <i class="bi bi-chevron-right ms-auto text-muted"></i>
                        </div>
                    </a>
                    <a href="{{ route('ruangan.index') }}" class="card quick-link text-decoration-none">
                        <div class="card-body d-flex align-items-center gap-3">
                            <div class="stat-icon skin-green rounded-3" style="width:46px;height:46px;font-size:1.3rem;"><i class="bi bi-building text-white"></i></div>
                            <div><div class="fw-700" style="font-weight:700;">Data Ruangan</div><div class="text-muted" style="font-size:.8rem;">Kelola data ruangan</div></div>
                            <i class="bi bi-chevron-right ms-auto text-muted"></i>
                        </div>
                    </a>
                    @endif
                    <a href="{{ route('peminjaman.index') }}" class="card quick-link text-decoration-none">
                        <div class="card-body d-flex align-items-center gap-3">
                            <div class="stat-icon skin-orange rounded-3" style="width:46px;height:46px;font-size:1.3rem;"><i class="bi bi-journal-check text-white"></i></div>
                            <div><div class="fw-700" style="font-weight:700;">Peminjaman</div><div class="text-muted" style="font-size:.8rem;">Lihat & kelola peminjaman</div></div>
                            <i class="bi bi-chevron-right ms-auto text-muted"></i>
                        </div>
                    </a>
                    <a href="{{ route('rekapitulasi.index') }}" class="card quick-link text-decoration-none">
                        <div class="card-body d-flex align-items-center gap-3">
                            <div class="stat-icon skin-purple rounded-3" style="width:46px;height:46px;font-size:1.3rem;"><i class="bi bi-file-earmark-bar-graph text-white"></i></div>
                            <div><div class="fw-700" style="font-weight:700;">Rekapitulasi</div><div class="text-muted" style="font-size:.8rem;">Lihat data rekapitulasi</div></div>
                            <i class="bi bi-chevron-right ms-auto text-muted"></i>
                        </div>
                    </a>
                </div>
            </div>
        </div>

        {{-- ── Recent Peminjaman Table ───────────────── --}}
        <div class="col-12">
            <div class="card recent-card">
                <div class="card-header d-flex align-items-center justify-content-between border-bottom-0 pb-0" style="border:none;padding:1.3rem 1.5rem .8rem;">
                    <h5 style="font-size:1rem;font-weight:700;margin:0;color:#333;">
                        <i class="bi bi-clock-history text-primary me-2"></i>Peminjaman Terbaru
                    </h5>
                    <a href="{{ route('peminjaman.index') }}" class="btn btn-sm btn-outline-primary">
                        Lihat Semua <i class="bi bi-arrow-right ms-1"></i>
                    </a>
                </div>
                <div class="card-body p-0">
                    <div class="table-responsive">
                        <table class="table mb-0">
                            <thead>
                                <tr>
                                    <th>#</th>
                                    <th>Peminjam</th>
                                    <th>Barang</th>
                                    <th>Ruangan</th>
                                    <th>Tgl Pinjam</th>
                                    <th>Tgl Kembali</th>
                                    <th>Aksi</th>
                                </tr>
                            </thead>
                            <tbody>
                                @forelse($recentPeminjaman as $i => $p)
                                <tr>
                                    <td class="text-muted fw-bold">{{ $i + 1 }}</td>
                                    <td>
                                        <div class="d-flex align-items-center gap-2">
                                            <img src="https://ui-avatars.com/api/?name={{ urlencode($p->user?->name ?? 'P') }}&background=4f8ef7&color=fff&size=38&bold=true&rounded=true"
                                                 class="avatar-sm" alt="foto">
                                            <span class="fw-600">{{ $p->user?->name ?? '-' }}</span>
                                        </div>
                                    </td>
                                    <td>{{ $p->barang?->nama_barang ?? '-' }}</td>
                                    <td>{{ $p->ruangan?->nama_ruangan ?? '-' }}</td>
                                    <td class="text-nowrap">
                                        <span class="badge rounded-pill" style="background:#e8f0fe;color:#1a5ff0;font-weight:600;font-size:.78rem;">
                                            {{ $p->tgl_peminjaman ? \Carbon\Carbon::parse($p->tgl_peminjaman)->translatedFormat('d M Y') : '-' }}
                                        </span>
                                    </td>
                                    <td class="text-nowrap">
                                        <span class="badge rounded-pill" style="background:#fef3e2;color:#e0670d;font-weight:600;font-size:.78rem;">
                                            {{ $p->tgl_pengembalian ? \Carbon\Carbon::parse($p->tgl_pengembalian)->translatedFormat('d M Y') : '-' }}
                                        </span>
                                    </td>
                                    <td>
                                        <a href="{{ route('peminjaman.show', $p->id) }}"
                                           class="btn btn-sm btn-info text-white" title="Detail">
                                            <i class="bi bi-eye"></i>
                                        </a>
                                    </td>
                                </tr>
                                @empty
                                <tr>
                                    <td colspan="7" class="text-center text-muted py-4">
                                        <i class="bi bi-inbox fs-3 d-block mb-2"></i>Belum ada data peminjaman.
                                    </td>
                                </tr>
                                @endforelse
                            </tbody>
                        </table>
                    </div>
                </div>
            </div>
        </div>
    </div>
</section>
@endsection

@push('scripts')
<script>
document.addEventListener('DOMContentLoaded', function () {
    const labels = @json($bulanLabels);
    const data   = @json($bulanData);

    new ApexCharts(document.querySelector('#peminjamanChart'), {
        series: [{
            name: 'Peminjaman',
            data: data
        }],
        chart: {
            type: 'bar',
            height: 320,
            toolbar: { show: false },
            fontFamily: 'Nunito, sans-serif',
        },
        plotOptions: {
            bar: {
                borderRadius: 8,
                columnWidth: '50%',
                distributed: false,
            }
        },
        dataLabels: { enabled: false },
        colors: ['#4f8ef7'],
        fill: {
            type: 'gradient',
            gradient: {
                shade: 'light',
                type: 'vertical',
                shadeIntensity: 0.4,
                gradientToColors: ['#a78bfa'],
                inverseColors: false,
                opacityFrom: 1,
                opacityTo: 0.85,
                stops: [0, 100]
            }
        },
        xaxis: {
            categories: labels,
            labels: {
                style: { fontSize: '12px', fontWeight: 600, colors: '#6c757d' }
            },
            axisBorder: { show: false },
            axisTicks: { show: false }
        },
        yaxis: {
            labels: {
                style: { fontSize: '12px', colors: '#6c757d' },
                formatter: val => Math.floor(val)
            }
        },
        grid: {
            borderColor: '#f2f2f2',
            strokeDashArray: 4,
        },
        tooltip: {
            y: { formatter: val => val + ' peminjaman' }
        },
    }).render();
});
</script>
@endpush