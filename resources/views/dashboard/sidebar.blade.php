    <!-- ======= Sidebar ======= -->
  <aside id="sidebar" class="sidebar">

    <ul class="sidebar-nav" id="sidebar-nav">

      <li class="nav-item">
        <a class="nav-link {{ Route::is('dashboard') ? '' : 'collapsed' }}" href="{{ route('dashboard') }}">
          <i class="bi bi-grid"></i>
          <span>Dashboard</span>
        </a>
      </li><!-- End Dashboard Nav -->

      {{-- Menu Transaksi (semua role) --}}
      <li class="nav-heading">Transaksi</li>

      <li class="nav-item">
        <a class="nav-link {{ Route::is('peminjaman.*') ? '' : 'collapsed' }}" href="{{ route('peminjaman.index') }}">
          <i class="bi bi-journal-check"></i>
          <span>Data Peminjaman</span>
        </a>
      </li>

      {{-- Laporan / Rekapitulasi (semua role) --}}
      <li class="nav-heading">Laporan</li>

      <li class="nav-item">
        <a class="nav-link {{ Route::is('rekapitulasi.*') ? '' : 'collapsed' }}" href="{{ route('rekapitulasi.index') }}">
          <i class="bi bi-file-earmark-bar-graph"></i>
          <span>Rekapitulasi Peminjaman</span>
        </a>
      </li>

      {{-- Menu Master & Pengaturan: hanya Admin & Staf --}}
      @if (Auth::user()->isAdmin())
        <li class="nav-heading">Data Master</li>

        <li class="nav-item">
          <a class="nav-link {{ Route::is('barang.*') ? '' : 'collapsed' }}" data-bs-target="#daftar-barang-nav" data-bs-toggle="collapse" href="#">
            <i class="bi bi-box-seam"></i><span>Data Barang</span><i class="bi bi-chevron-down ms-auto"></i>
          </a>
          <ul id="daftar-barang-nav" class="nav-content collapse {{ Route::is('barang.*') ? 'show' : '' }}" data-bs-parent="#sidebar-nav">
            <li>
              <a href="{{ route('barang.index') }}" class="{{ Route::is('barang.*') ? 'active' : '' }}">
                <i class="bi bi-circle"></i><span>Barang</span>
              </a>
            </li>
          </ul>
        </li>

        <li class="nav-item">
          <a class="nav-link {{ Route::is('ruangan.*') ? '' : 'collapsed' }}" data-bs-target="#daftar-ruangan-nav" data-bs-toggle="collapse" href="#">
            <i class="bi bi-door-open"></i><span>Data Ruangan</span><i class="bi bi-chevron-down ms-auto"></i>
          </a>
          <ul id="daftar-ruangan-nav" class="nav-content collapse {{ Route::is('ruangan.*') ? 'show' : '' }}" data-bs-parent="#sidebar-nav">
            <li>
              <a href="{{ route('ruangan.index') }}" class="{{ Route::is('ruangan.*') ? 'active' : '' }}">
                <i class="bi bi-circle"></i><span>Ruangan</span>
              </a>
            </li>
          </ul>
        </li>

        <li class="nav-heading">Pengaturan</li>

        <li class="nav-item">
          <a class="nav-link {{ Route::is('jabatan.*') ? '' : 'collapsed' }}" href="{{ route('jabatan.index') }}">
            <i class="bi bi-award"></i>
            <span>Daftar Jabatan</span>
          </a>
        </li>
        <li class="nav-item">
          <a class="nav-link {{ Route::is('pinjam.*') ? '' : 'collapsed' }}" href="{{ route('pinjam.index') }}">
            <i class="bi bi-card-checklist"></i>
            <span>Tujuan Pinjam</span>
          </a>
        </li>
        <li class="nav-item">
          <a class="nav-link {{ Route::is('settings.*') ? '' : 'collapsed' }}" href="{{ route('settings.index') }}">
            <i class="bi bi-person-gear"></i>
            <span>Pengaturan Pimpinan</span>
          </a>
        </li>

        {{-- Hanya Admin yang dapat mengelola data pengguna (Staf tidak bisa) --}}
        @if (Auth::user()->role === 'admin')
          <li class="nav-item">
            <a class="nav-link {{ Route::is('user.*') ? '' : 'collapsed' }}" href="{{ route('user.index') }}">
              <i class="bi bi-people"></i>
              <span>Daftar Pengguna</span>
            </a>
          </li>
        @endif
      @endif

    </ul>

  </aside><!-- End Sidebar-->