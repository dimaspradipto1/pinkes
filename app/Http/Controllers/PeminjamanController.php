<?php

namespace App\Http\Controllers;

use App\DataTables\PeminjamanDataTable;
use App\Models\Peminjaman;
use App\Models\Barang;
use App\Models\Ruangan;
use App\Models\Pinjam;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use RealRashid\SweetAlert\Facades\Alert;

class PeminjamanController extends Controller
{
    public function index(PeminjamanDataTable $dataTable)
    {
        return $dataTable->render('pages.peminjaman.index');
    }

    public function create()
    {
        $barangs  = Barang::orderBy('nama_barang')->get();
        $ruangans = Ruangan::orderBy('nama_ruangan')->get();
        $pinjams  = Pinjam::orderBy('tujuan_pinjam')->get();
        return view('pages.peminjaman.create', compact('barangs', 'ruangans', 'pinjams'));
    }

    public function store(Request $request)
    {
        $request->validate([
            'pinjam_id'        => 'required|exists:pinjams,id',
            'barang_id'        => 'nullable|exists:barangs,id',
            'ruangan_id'       => 'nullable|exists:ruangans,id',
            'tgl_peminjaman'   => 'required|date',
            'jam_peminjaman'   => 'required',
            'tgl_pengembalian' => 'required|date|after_or_equal:tgl_peminjaman',
            'jam_pengembalian' => 'required',
            'keterangan'       => 'nullable|string',
        ], [
            'pinjam_id.required'              => 'Tujuan pinjam wajib dipilih.',
            'tgl_peminjaman.required'         => 'Tanggal peminjaman wajib diisi.',
            'jam_peminjaman.required'         => 'Jam peminjaman wajib diisi.',
            'tgl_pengembalian.required'       => 'Tanggal pengembalian wajib diisi.',
            'tgl_pengembalian.after_or_equal' => 'Tanggal pengembalian tidak boleh sebelum tanggal peminjaman.',
            'jam_pengembalian.required'       => 'Jam pengembalian wajib diisi.',
        ]);

        if (!$request->barang_id && !$request->ruangan_id) {
            Alert::error('Gagal', 'Pilih minimal satu Barang atau Ruangan yang ingin dipinjam.');
            return back()->withInput();
        }

        // Simpan dengan status pending — stok belum dikurangi
        Peminjaman::create([
            'user_id'          => Auth::id(),
            'barang_id'        => $request->barang_id,
            'ruangan_id'       => $request->ruangan_id,
            'pinjam_id'        => $request->pinjam_id,
            'tgl_peminjaman'   => $request->tgl_peminjaman,
            'jam_peminjaman'   => $request->jam_peminjaman,
            'tgl_pengembalian' => $request->tgl_pengembalian,
            'jam_pengembalian' => $request->jam_pengembalian,
            'keterangan'       => $request->keterangan,
            'status'           => 'pending',
        ]);

        Alert::success('Berhasil', 'Permohonan peminjaman berhasil disubmit. Menunggu verifikasi admin.');
        return redirect()->route('peminjaman.index');
    }

    public function show(Peminjaman $peminjaman)
    {
        // User biasa hanya bisa lihat milik sendiri
        if (Auth::user()->isUser() && $peminjaman->user_id !== Auth::id()) {
            abort(403);
        }
        $peminjaman->load(['user', 'barang', 'ruangan', 'pinjam']);
        return view('pages.peminjaman.show', compact('peminjaman'));
    }

    public function edit(Peminjaman $peminjaman)
    {
        // User biasa hanya bisa edit milik sendiri dan hanya jika pending
        if (Auth::user()->isUser()) {
            if ($peminjaman->user_id !== Auth::id()) abort(403);
            if ($peminjaman->status !== 'pending') {
                Alert::error('Tidak Bisa', 'Data peminjaman yang sudah diverifikasi tidak dapat diedit.');
                return redirect()->route('peminjaman.index');
            }
        }

        $barangs  = Barang::orderBy('nama_barang')->get();
        $ruangans = Ruangan::orderBy('nama_ruangan')->get();
        $pinjams  = Pinjam::orderBy('tujuan_pinjam')->get();
        return view('pages.peminjaman.edit', compact('peminjaman', 'barangs', 'ruangans', 'pinjams'));
    }

    public function update(Request $request, Peminjaman $peminjaman)
    {
        // Guard: user hanya bisa update jika pending
        if (Auth::user()->isUser()) {
            if ($peminjaman->user_id !== Auth::id()) abort(403);
            if ($peminjaman->status !== 'pending') {
                Alert::error('Tidak Bisa', 'Data peminjaman yang sudah diverifikasi tidak dapat diedit.');
                return redirect()->route('peminjaman.index');
            }
        }

        $request->validate([
            'pinjam_id'        => 'required|exists:pinjams,id',
            'barang_id'        => 'nullable|exists:barangs,id',
            'ruangan_id'       => 'nullable|exists:ruangans,id',
            'tgl_peminjaman'   => 'required|date',
            'jam_peminjaman'   => 'required',
            'tgl_pengembalian' => 'required|date|after_or_equal:tgl_peminjaman',
            'jam_pengembalian' => 'required',
            'keterangan'       => 'nullable|string',
        ]);

        if (!$request->barang_id && !$request->ruangan_id) {
            Alert::error('Gagal', 'Pilih minimal satu Barang atau Ruangan yang ingin dipinjam.');
            return back()->withInput();
        }

        $peminjaman->update([
            'barang_id'        => $request->barang_id,
            'ruangan_id'       => $request->ruangan_id,
            'pinjam_id'        => $request->pinjam_id,
            'tgl_peminjaman'   => $request->tgl_peminjaman,
            'jam_peminjaman'   => $request->jam_peminjaman,
            'tgl_pengembalian' => $request->tgl_pengembalian,
            'jam_pengembalian' => $request->jam_pengembalian,
            'keterangan'       => $request->keterangan,
        ]);

        Alert::success('Berhasil', 'Data peminjaman berhasil diperbarui.');
        return redirect()->route('peminjaman.index');
    }

    /**
     * ADMIN: Verifikasi peminjaman — cek konflik jadwal, kurangi stok
     */
    public function verifikasi(Peminjaman $peminjaman)
    {
        if (!Auth::user()->isAdmin()) abort(403);
        if ($peminjaman->status !== 'pending') {
            Alert::error('Gagal', 'Hanya peminjaman berstatus pending yang bisa diverifikasi.');
            return back();
        }

        // Cek konflik jadwal (overlap waktu, bukan sekadar berurutan)
        if ($peminjaman->barang_id) {
            $konflik = Peminjaman::where('barang_id', $peminjaman->barang_id)
                ->where('status', 'validasi')
                ->where('tgl_peminjaman', '<=', $peminjaman->tgl_pengembalian)
                ->where('tgl_pengembalian', '>=', $peminjaman->tgl_peminjaman)
                ->where(function ($q) use ($peminjaman) {
                    // Overlap waktu sejati: bukan hanya berurutan (jam mulai = jam selesai orang lain OK)
                    $q->whereRaw("TIME(jam_peminjaman) < TIME(?)", [$peminjaman->jam_pengembalian])
                      ->whereRaw("TIME(jam_pengembalian) > TIME(?)", [$peminjaman->jam_peminjaman]);
                })
                ->exists();

            if ($konflik) {
                Alert::error('Konflik Jadwal', 'Barang sedang digunakan pada waktu yang sama. Tidak dapat diverifikasi.');
                return back();
            }

            // Cek stok
            $barang = Barang::find($peminjaman->barang_id);
            if ($barang && $barang->jumlah < 1) {
                Alert::error('Stok Habis', 'Stok barang ' . $barang->nama_barang . ' sedang habis.');
                return back();
            }
            if ($barang) $barang->decrement('jumlah');
        }

        if ($peminjaman->ruangan_id) {
            $konflik = Peminjaman::where('ruangan_id', $peminjaman->ruangan_id)
                ->where('status', 'validasi')
                ->where('tgl_peminjaman', '<=', $peminjaman->tgl_pengembalian)
                ->where('tgl_pengembalian', '>=', $peminjaman->tgl_peminjaman)
                ->where(function ($q) use ($peminjaman) {
                    $q->whereRaw("TIME(jam_peminjaman) < TIME(?)", [$peminjaman->jam_pengembalian])
                      ->whereRaw("TIME(jam_pengembalian) > TIME(?)", [$peminjaman->jam_peminjaman]);
                })
                ->exists();

            if ($konflik) {
                // Rollback stok barang jika sudah dikurangi
                if ($peminjaman->barang_id) {
                    Barang::find($peminjaman->barang_id)?->increment('jumlah');
                }
                Alert::error('Konflik Jadwal', 'Ruangan sedang digunakan pada waktu yang sama.');
                return back();
            }

            $ruangan = Ruangan::find($peminjaman->ruangan_id);
            if ($ruangan && $ruangan->jumlah < 1) {
                if ($peminjaman->barang_id) Barang::find($peminjaman->barang_id)?->increment('jumlah');
                Alert::error('Tidak Tersedia', 'Ruangan ' . $ruangan->nama_ruangan . ' sedang tidak tersedia.');
                return back();
            }
            if ($ruangan) $ruangan->decrement('jumlah');
        }

        $peminjaman->update(['status' => 'validasi']);
        Alert::success('Berhasil', 'Peminjaman berhasil diverifikasi.');
        return back();
    }

    /**
     * ADMIN: Tolak peminjaman
     */
    public function tolak(Peminjaman $peminjaman)
    {
        if (!Auth::user()->isAdmin()) abort(403);
        if (!in_array($peminjaman->status, ['pending', 'validasi'])) {
            Alert::error('Gagal', 'Status tidak dapat diubah.');
            return back();
        }

        // Kembalikan stok jika sebelumnya validasi
        if ($peminjaman->status === 'validasi') {
            if ($peminjaman->barang_id)  Barang::find($peminjaman->barang_id)?->increment('jumlah');
            if ($peminjaman->ruangan_id) Ruangan::find($peminjaman->ruangan_id)?->increment('jumlah');
        }

        $peminjaman->update(['status' => 'ditolak']);
        Alert::success('Berhasil', 'Peminjaman telah ditolak.');
        return back();
    }

    /**
     * ADMIN: Selesaikan peminjaman — kembalikan stok
     */
    public function selesai(Peminjaman $peminjaman)
    {
        if (!Auth::user()->isAdmin()) abort(403);
        if ($peminjaman->status !== 'validasi') {
            Alert::error('Gagal', 'Hanya peminjaman berstatus divalidasi yang bisa diselesaikan.');
            return back();
        }

        if ($peminjaman->barang_id)  Barang::find($peminjaman->barang_id)?->increment('jumlah');
        if ($peminjaman->ruangan_id) Ruangan::find($peminjaman->ruangan_id)?->increment('jumlah');

        $peminjaman->update(['status' => 'selesai']);
        Alert::success('Berhasil', 'Peminjaman telah diselesaikan dan stok dikembalikan.');
        return back();
    }
}
