<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>SIPINJAM — Sistem Informasi Peminjaman Ruangan dan Barang Dinas Kesehatan Kota Batam</title>
    <meta name="description" content="Portal Peminjaman Ruangan dan Barang Dinas Kesehatan Kota Batam. Cek ketersediaan ruangan dan barang secara real-time.">

    {{-- Fonts --}}
    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
    <link href="https://fonts.googleapis.com/css2?family=Plus+Jakarta+Sans:wght@300;400;500;600;700;800&display=swap" rel="stylesheet">

    {{-- Bootstrap Icons --}}
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.11.3/font/bootstrap-icons.min.css">

    <style>
        *, *::before, *::after { box-sizing: border-box; margin: 0; padding: 0; }

        :root {
            --green-500: #16a34a;
            --green-600: #15803d;
            --green-700: #166534;
            --green-50:  #f0fdf4;
            --green-100: #dcfce7;
            --teal-400:  #2dd4bf;
            --blue-500:  #3b82f6;
            --gray-50:   #f9fafb;
            --gray-100:  #f3f4f6;
            --gray-200:  #e5e7eb;
            --gray-500:  #6b7280;
            --gray-700:  #374151;
            --gray-900:  #111827;
            --radius-xl: 20px;
            --radius-lg: 14px;
            --radius-md: 10px;
            --shadow-sm: 0 1px 3px rgba(0,0,0,.08);
            --shadow-md: 0 4px 16px rgba(0,0,0,.10);
            --shadow-lg: 0 10px 36px rgba(0,0,0,.13);
            --font: 'Plus Jakarta Sans', sans-serif;
        }

        body {
            font-family: var(--font);
            background: var(--gray-50);
            color: var(--gray-700);
            min-height: 100vh;
        }

        /* ─── NAVBAR ────────────────────────────────── */
        .navbar {
            position: sticky; top: 0; z-index: 100;
            background: rgba(255,255,255,.92);
            backdrop-filter: blur(12px);
            border-bottom: 1px solid var(--gray-200);
            padding: .9rem 0;
            box-shadow: var(--shadow-sm);
        }
        .navbar-inner {
            max-width: 1200px; margin: 0 auto;
            padding: 0 1.5rem;
            display: flex; align-items: center; justify-content: space-between; gap: 1rem;
        }
        .brand {
            display: flex; align-items: center; gap: .75rem; text-decoration: none;
        }
        .brand-logo {
            width: 44px; height: 44px;
            background: linear-gradient(135deg, var(--green-500), var(--teal-400));
            border-radius: 12px;
            display: flex; align-items: center; justify-content: center;
            color: #fff; font-size: 1.3rem;
            flex-shrink: 0;
        }
        .brand-text strong {
            display: block; font-size: 1.05rem; font-weight: 800; color: var(--gray-900); line-height: 1.1;
        }
        .brand-text span {
            font-size: .72rem; color: var(--gray-500); font-weight: 500;
        }
        .nav-actions { display: flex; align-items: center; gap: .75rem; }
        .btn-login {
            background: linear-gradient(135deg, var(--green-500), var(--teal-400));
            color: #fff; border: none; border-radius: 50px;
            padding: .5rem 1.4rem; font-weight: 700; font-size: .88rem;
            cursor: pointer; text-decoration: none;
            transition: opacity .2s, transform .2s;
            display: inline-flex; align-items: center; gap: .4rem;
        }
        .btn-login:hover { opacity: .9; transform: translateY(-1px); color: #fff; }

        /* ─── HERO ──────────────────────────────────── */
        .hero {
            background: linear-gradient(135deg, var(--green-700) 0%, var(--green-500) 50%, var(--teal-400) 100%);
            color: #fff;
            padding: 5rem 1.5rem 4rem;
            text-align: center;
            position: relative; overflow: hidden;
        }
        .hero::before {
            content: '';
            position: absolute; inset: 0;
            background: url("data:image/svg+xml,%3Csvg width='60' height='60' viewBox='0 0 60 60' xmlns='http://www.w3.org/2000/svg'%3E%3Cg fill='none' fill-rule='evenodd'%3E%3Cg fill='%23ffffff' fill-opacity='0.04'%3E%3Ccircle cx='30' cy='30' r='20'/%3E%3C/g%3E%3C/g%3E%3C/svg%3E");
        }
        .hero-badge {
            display: inline-flex; align-items: center; gap: .4rem;
            background: rgba(255,255,255,.18); border: 1px solid rgba(255,255,255,.3);
            border-radius: 50px; padding: .38rem 1rem;
            font-size: .8rem; font-weight: 600; margin-bottom: 1.4rem;
            backdrop-filter: blur(8px);
        }
        .hero h1 {
            font-size: clamp(1.8rem, 4vw, 2.8rem);
            font-weight: 800; line-height: 1.2; margin-bottom: .9rem;
        }
        .hero p {
            font-size: 1.05rem; opacity: .88; max-width: 560px; margin: 0 auto 2rem;
            line-height: 1.7;
        }
        .hero-stats {
            display: flex; justify-content: center; gap: 2.5rem; flex-wrap: wrap;
        }
        .hero-stat { text-align: center; }
        .hero-stat .num { font-size: 2rem; font-weight: 800; line-height: 1; }
        .hero-stat .lbl { font-size: .78rem; opacity: .75; font-weight: 500; margin-top: .2rem; }

        /* ─── TABS ──────────────────────────────────── */
        .section-wrapper {
            max-width: 1200px; margin: 0 auto; padding: 0 1.5rem 4rem;
        }
        .tabs-wrap {
            display: flex; gap: .6rem; margin: 2.2rem 0 1.5rem;
            border-bottom: 2px solid var(--gray-200);
            padding-bottom: 0;
        }
        .tab-btn {
            background: none; border: none; cursor: pointer;
            font-family: var(--font); font-size: .9rem; font-weight: 600;
            padding: .65rem 1.4rem 1rem;
            color: var(--gray-500); position: relative;
            transition: color .2s;
        }
        .tab-btn::after {
            content: ''; position: absolute; bottom: -2px; left: 0; right: 0;
            height: 3px; border-radius: 3px 3px 0 0;
            background: var(--green-500);
            transform: scaleX(0); transition: transform .25s;
        }
        .tab-btn.active { color: var(--green-600); }
        .tab-btn.active::after { transform: scaleX(1); }
        .tab-btn:hover { color: var(--green-600); }
        .tab-badge {
            display: inline-flex; align-items: center; justify-content: center;
            background: var(--green-100); color: var(--green-600);
            border-radius: 50px; font-size: .7rem; font-weight: 700;
            padding: .1rem .5rem; margin-left: .4rem;
        }

        /* ─── SEARCH ────────────────────────────────── */
        .search-box {
            position: relative; max-width: 400px; margin-bottom: 1.5rem;
        }
        .search-box i {
            position: absolute; left: 1rem; top: 50%; transform: translateY(-50%);
            color: var(--gray-500); font-size: .95rem;
        }
        .search-box input {
            width: 100%; padding: .65rem 1rem .65rem 2.6rem;
            border: 1.5px solid var(--gray-200); border-radius: 50px;
            font-family: var(--font); font-size: .9rem;
            background: #fff; outline: none;
            transition: border-color .2s, box-shadow .2s;
        }
        .search-box input:focus {
            border-color: var(--green-500);
            box-shadow: 0 0 0 3px rgba(22,163,74,.12);
        }

        /* ─── TAB PANELS ────────────────────────────── */
        .tab-panel { display: none; animation: fadeIn .3s ease; }
        .tab-panel.active { display: block; }
        @keyframes fadeIn { from { opacity:0; transform:translateY(6px); } to { opacity:1; transform:none; } }

        /* ─── GRID ──────────────────────────────────── */
        .grid {
            display: grid;
            grid-template-columns: repeat(auto-fill, minmax(270px, 1fr));
            gap: 1.25rem;
        }

        /* ─── CARD ──────────────────────────────────── */
        .item-card {
            background: #fff; border-radius: var(--radius-xl);
            box-shadow: var(--shadow-sm);
            overflow: hidden;
            transition: transform .25s, box-shadow .25s;
            border: 1px solid var(--gray-100);
        }
        .item-card:hover {
            transform: translateY(-5px);
            box-shadow: var(--shadow-lg);
        }
        .card-img-wrap {
            height: 180px; overflow: hidden; position: relative;
            background: linear-gradient(135deg, var(--green-50), #e0f2fe);
        }
        .card-img-wrap img {
            width: 100%; height: 100%; object-fit: cover;
            transition: transform .4s;
        }
        .item-card:hover .card-img-wrap img { transform: scale(1.06); }
        .card-img-placeholder {
            width: 100%; height: 100%;
            display: flex; flex-direction: column; align-items: center; justify-content: center;
            gap: .5rem; color: var(--green-500);
        }
        .card-img-placeholder i { font-size: 3rem; opacity: .5; }
        .card-img-placeholder span { font-size: .75rem; color: var(--gray-500); font-weight: 500; }

        .kode-badge {
            position: absolute; top: .7rem; left: .7rem;
            background: rgba(255,255,255,.9); backdrop-filter: blur(6px);
            color: var(--green-700); font-size: .72rem; font-weight: 700;
            padding: .25rem .65rem; border-radius: 50px;
            border: 1px solid var(--green-100);
        }

        .card-body { padding: 1.1rem 1.25rem 1.3rem; }
        .card-title {
            font-size: .97rem; font-weight: 700; color: var(--gray-900);
            margin-bottom: .5rem; line-height: 1.3;
        }
        .card-desc {
            font-size: .82rem; color: var(--gray-500); line-height: 1.5;
            margin-bottom: .8rem;
            display: -webkit-box; -webkit-line-clamp: 2;
            -webkit-box-orient: vertical; overflow: hidden;
        }
        .card-footer-info {
            display: flex; align-items: center; justify-content: space-between;
            padding-top: .75rem;
            border-top: 1px solid var(--gray-100);
        }
        .avail-badge {
            display: inline-flex; align-items: center; gap: .35rem;
            border-radius: 50px; padding: .28rem .8rem;
            font-size: .78rem; font-weight: 700;
        }
        .avail-badge.tersedia {
            background: var(--green-100); color: var(--green-600);
        }
        .avail-badge.tersedia::before {
            content: ''; width: 7px; height: 7px; border-radius: 50%;
            background: var(--green-500);
            animation: pulse 1.8s infinite;
        }
        .avail-badge.habis {
            background: #fee2e2; color: #dc2626;
        }
        .avail-badge.habis::before {
            content: ''; width: 7px; height: 7px; border-radius: 50%;
            background: #dc2626;
        }
        @keyframes pulse {
            0%,100% { opacity:1; transform:scale(1); }
            50%      { opacity:.5; transform:scale(1.4); }
        }
        .jumlah-info {
            font-size: .8rem; color: var(--gray-500); font-weight: 600;
            display: flex; align-items: center; gap: .3rem;
        }

        /* ─── EMPTY STATE ───────────────────────────── */
        .empty-state {
            text-align: center; padding: 4rem 1rem; color: var(--gray-500);
        }
        .empty-state i { font-size: 3.5rem; opacity: .3; display: block; margin-bottom: 1rem; }
        .empty-state p { font-size: .95rem; }

        /* ─── FOOTER ────────────────────────────────── */
        footer {
            background: var(--gray-900); color: #fff;
            padding: 2.5rem 1.5rem; text-align: center;
        }
        footer .ft-logo {
            display: inline-flex; align-items: center; gap: .6rem;
            margin-bottom: .8rem;
        }
        footer .ft-logo-icon {
            width: 36px; height: 36px;
            background: linear-gradient(135deg, var(--green-500), var(--teal-400));
            border-radius: 10px;
            display: flex; align-items: center; justify-content: center;
            font-size: 1.1rem;
        }
        footer strong { font-size: 1.05rem; }
        footer p { font-size: .82rem; opacity: .5; margin-top: .4rem; }

        /* ─── RESPONSIVE ────────────────────────────── */
        @media (max-width: 600px) {
            .hero { padding: 3.5rem 1rem 3rem; }
            .hero-stats { gap: 1.5rem; }
            .grid { grid-template-columns: 1fr 1fr; gap: .85rem; }
            .card-img-wrap { height: 140px; }
        }
        @media (max-width: 400px) {
            .grid { grid-template-columns: 1fr; }
        }
    </style>
</head>
<body>

{{-- ─── NAVBAR ──────────────────────────────────────── --}}
<nav class="navbar">
    <div class="navbar-inner">
        <a href="{{ route('index') }}" class="brand">
            <div class="brand-logo"><i class="bi bi-hospital-fill"></i></div>
            <div class="brand-text">
                <strong>SIPINJAM</strong>
                <span>Dinkes Kota Batam</span>
            </div>
        </a>
        <div class="nav-actions">
            <a href="{{ route('login') }}" class="btn-login">
                <i class="bi bi-box-arrow-in-right"></i> Masuk
            </a>
        </div>
    </div>
</nav>

{{-- ─── HERO ──────────────────────────────────────────── --}}
<section class="hero">
    <div class="hero-badge">
        <i class="bi bi-activity"></i> Portal Peminjaman Publik
    </div>
    <h1>Sistem Informasi Peminjaman Ruangan dan Barang<br>Dinas Kesehatan Kota Batam</h1>
    <p>Cek ketersediaan <strong>ruangan</strong> dan <strong>barang</strong> inventaris secara real-time. Transparansi data untuk pelayanan yang lebih baik.</p>
    <div class="hero-stats">
        <div class="hero-stat">
            <div class="num">{{ $barangs->count() }}</div>
            <div class="lbl">Total Barang</div>
        </div>
        <div class="hero-stat" style="border-left:1px solid rgba(255,255,255,.25);padding-left:2.5rem;">
            <div class="num">{{ $ruangans->count() }}</div>
            <div class="lbl">Total Ruangan</div>
        </div>
        <div class="hero-stat" style="border-left:1px solid rgba(255,255,255,.25);padding-left:2.5rem;">
            <div class="num">{{ $barangs->where('jumlah', '>', 0)->count() + $ruangans->where('jumlah', '>', 0)->count() }}</div>
            <div class="lbl">Tersedia</div>
        </div>
    </div>
</section>

{{-- ─── CONTENT ────────────────────────────────────────── --}}
<div class="section-wrapper">

    {{-- Tabs --}}
    <div class="tabs-wrap">
        <button class="tab-btn active" onclick="switchTab('ruangan', this)">
            <i class="bi bi-building me-1"></i> Ruangan
            <span class="tab-badge">{{ $ruangans->count() }}</span>
        </button>
        <button class="tab-btn" onclick="switchTab('barang', this)">
            <i class="bi bi-archive-fill me-1"></i> Barang
            <span class="tab-badge">{{ $barangs->count() }}</span>
        </button>
    </div>

    {{-- ── TAB RUANGAN ──────────────────────────────── --}}
    <div id="panel-ruangan" class="tab-panel active">
        <div class="search-box">
            <i class="bi bi-search"></i>
            <input type="text" id="searchRuangan" placeholder="Cari ruangan…" oninput="filterCards('ruangan')">
        </div>
        <div class="grid" id="grid-ruangan">
            @forelse($ruangans as $r)
            @php
                $foto = $r->foto_ruangan && $r->foto_ruangan !== '-'
                    ? asset('storage/' . $r->foto_ruangan)
                    : ($r->fotos->first()?->nama_foto ? asset('storage/' . $r->fotos->first()->nama_foto) : null);
                $tersediaR = ($r->jumlah ?? 0) > 0;
            @endphp
            <div class="item-card" data-name="{{ strtolower($r->nama_ruangan) }}">
                <div class="card-img-wrap">
                    @if($foto)
                        <img src="{{ $foto }}" alt="{{ $r->nama_ruangan }}"
                             onerror="this.style.display='none';this.nextElementSibling.style.display='flex';">
                        <div class="card-img-placeholder" style="display:none;">
                            <i class="bi bi-building"></i>
                            <span>Foto tidak tersedia</span>
                        </div>
                    @else
                        <div class="card-img-placeholder">
                            <i class="bi bi-building"></i>
                            <span>Belum ada foto</span>
                        </div>
                    @endif
                    <span class="kode-badge">{{ $r->kode_ruangan }}</span>
                </div>
                <div class="card-body">
                    <div class="card-title">{{ $r->nama_ruangan }}</div>
                    <div class="card-desc">{{ $r->keterangan ?: 'Tidak ada keterangan.' }}</div>
                    <div class="card-footer-info">
                        @if($tersediaR)
                            <span class="avail-badge tersedia">Tersedia</span>
                        @else
                            <span class="avail-badge habis">Tidak Tersedia</span>
                        @endif
                        <span class="jumlah-info">
                            <i class="bi bi-building-fill"></i>
                            {{ $r->jumlah ?? 0 }} Unit
                        </span>
                    </div>
                </div>
            </div>
            @empty
            <div class="empty-state" style="grid-column:1/-1;">
                <i class="bi bi-building-x"></i>
                <p>Belum ada data ruangan.</p>
            </div>
            @endforelse
        </div>
    </div>

    {{-- ── TAB BARANG ───────────────────────────────── --}}
    <div id="panel-barang" class="tab-panel">
        <div class="search-box">
            <i class="bi bi-search"></i>
            <input type="text" id="searchBarang" placeholder="Cari barang…" oninput="filterCards('barang')">
        </div>
        <div class="grid" id="grid-barang">
            @forelse($barangs as $b)
            @php
                $foto = $b->foto_barang && $b->foto_barang !== '-'
                    ? asset('storage/' . $b->foto_barang)
                    : ($b->fotos->first()?->foto_barang ? asset('storage/' . $b->fotos->first()->foto_barang) : null);
                $tersedia = ($b->jumlah ?? 0) > 0;
            @endphp
            <div class="item-card" data-name="{{ strtolower($b->nama_barang) }}">
                <div class="card-img-wrap">
                    @if($foto)
                        <img src="{{ $foto }}" alt="{{ $b->nama_barang }}"
                             onerror="this.style.display='none';this.nextElementSibling.style.display='flex';">
                        <div class="card-img-placeholder" style="display:none;">
                            <i class="bi bi-archive"></i>
                            <span>Foto tidak tersedia</span>
                        </div>
                    @else
                        <div class="card-img-placeholder">
                            <i class="bi bi-archive"></i>
                            <span>Belum ada foto</span>
                        </div>
                    @endif
                    <span class="kode-badge">{{ $b->kode_barang }}</span>
                </div>
                <div class="card-body">
                    <div class="card-title">{{ $b->nama_barang }}</div>
                    <div class="card-desc">{{ $b->keterangan ?: 'Tidak ada keterangan.' }}</div>
                    <div class="card-footer-info">
                        @if($tersedia)
                            <span class="avail-badge tersedia">Tersedia</span>
                        @else
                            <span class="avail-badge habis">Tidak Tersedia</span>
                        @endif
                        <span class="jumlah-info">
                            <i class="bi bi-boxes"></i>
                            {{ $b->jumlah ?? 0 }} unit
                        </span>
                    </div>
                </div>
            </div>
            @empty
            <div class="empty-state" style="grid-column:1/-1;">
                <i class="bi bi-archive-x"></i>
                <p>Belum ada data barang.</p>
            </div>
            @endforelse
        </div>
    </div>

</div>

{{-- ─── FOOTER ──────────────────────────────────────────── --}}
<footer>
    <div class="ft-logo">
        <div class="ft-logo-icon"><i class="bi bi-hospital-fill text-white"></i></div>
        <strong>PINKES</strong>
    </div>
    <p>Sistem Informasi Peminjaman Ruangan dan Barang Dinas Kesehatan Kota Batam &copy; {{ date('Y') }}. All rights reserved.</p>
</footer>

<script>
    function switchTab(tab, btn) {
        document.querySelectorAll('.tab-btn').forEach(b => b.classList.remove('active'));
        document.querySelectorAll('.tab-panel').forEach(p => p.classList.remove('active'));
        btn.classList.add('active');
        document.getElementById('panel-' + tab).classList.add('active');
    }

    function filterCards(type) {
        const q = document.getElementById('search' + (type === 'ruangan' ? 'Ruangan' : 'Barang')).value.toLowerCase();
        document.querySelectorAll('#grid-' + type + ' .item-card').forEach(card => {
            card.style.display = card.dataset.name.includes(q) ? '' : 'none';
        });
    }
</script>
</body>
</html>
