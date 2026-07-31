<?php

namespace App\Http\Controllers;

use App\Models\Peminjaman;
use App\Models\Setting;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Barryvdh\DomPDF\Facade\Pdf;
use Carbon\Carbon;

class RekapitulasiController extends Controller
{
    public function index(Request $request)
    {
        $user  = Auth::user();
        $jenis = $request->get('jenis', 'semua'); // barang | ruangan | semua

        $query = Peminjaman::with(['user', 'barang', 'ruangan', 'pinjam']);

        // User biasa hanya lihat data sendiri
        if ($user->isUser()) {
            $query->where('user_id', $user->id);
        }

        // Filter jenis
        if ($jenis === 'barang') {
            $query->whereNotNull('barang_id')->whereNull('ruangan_id');
        } elseif ($jenis === 'ruangan') {
            $query->whereNull('barang_id')->whereNotNull('ruangan_id');
        }

        // Filter periode
        if ($request->filled('tgl_mulai') && $request->filled('tgl_selesai')) {
            $query->where(function ($q) use ($request) {
                $q->whereBetween('tgl_peminjaman', [$request->tgl_mulai, $request->tgl_selesai])
                  ->orWhereBetween('tgl_pengembalian', [$request->tgl_mulai, $request->tgl_selesai]);
            });
        }

        $peminjamen = $query->latest('created_at')->get();

        return view('pages.rekapitulasi.index', compact('peminjamen', 'jenis'));
    }

    public function pdf(Request $request)
    {
        // Hanya admin yang bisa export PDF
        if (!Auth::user()->isAdmin()) abort(403);

        $jenis = $request->get('jenis', 'semua');
        $query = Peminjaman::with(['user', 'barang', 'ruangan', 'pinjam']);

        if ($jenis === 'barang') {
            $query->whereNotNull('barang_id')->whereNull('ruangan_id');
        } elseif ($jenis === 'ruangan') {
            $query->whereNull('barang_id')->whereNotNull('ruangan_id');
        }

        if ($request->filled('tgl_mulai') && $request->filled('tgl_selesai')) {
            $query->where(function ($q) use ($request) {
                $q->whereBetween('tgl_peminjaman', [$request->tgl_mulai, $request->tgl_selesai])
                  ->orWhereBetween('tgl_pengembalian', [$request->tgl_mulai, $request->tgl_selesai]);
            });
        }

        $peminjamen  = $query->latest('created_at')->get();
        $tgl_mulai   = $request->tgl_mulai;
        $tgl_selesai = $request->tgl_selesai;

        // Penandatangan laporan (Bisa dikustomisasi via request, database setting, atau fallback ke user Kasubag)
        $penandatangan = null;
        if ($request->filled('nama_pimpinan')) {
            $penandatangan = (object) [
                'name'             => $request->nama_pimpinan,
                'nip'              => $request->nip_pimpinan,
                'pangkat_golongan' => null,
                'jabatan_nama'     => $request->jabatan_pimpinan ?? 'Kepala Sub Bagian Umum dan Kepegawaian',
            ];
        } elseif (Setting::getByKey('pimpinan_nama')) {
            $penandatangan = (object) [
                'name'             => Setting::getByKey('pimpinan_nama'),
                'nip'              => Setting::getByKey('pimpinan_nip'),
                'pangkat_golongan' => null,
                'jabatan_nama'     => Setting::getByKey('pimpinan_jabatan', 'Kepala Sub Bagian Umum dan Kepegawaian'),
            ];
        } else {
            $userPenandatangan = \App\Models\User::where('jabatan_nama', 'like', '%Kasubag Umum dan Kepegawaian%')->first();
            if ($userPenandatangan) {
                $penandatangan = (object) [
                    'name'             => $userPenandatangan->name,
                    'nip'              => $userPenandatangan->nip,
                    'pangkat_golongan' => $userPenandatangan->pangkat_golongan,
                    'jabatan_nama'     => $userPenandatangan->jabatan_nama,
                ];
            }
        }

        $pdf = Pdf::loadView('pages.rekapitulasi.pdf', compact('peminjamen', 'tgl_mulai', 'tgl_selesai', 'jenis', 'penandatangan'));
        $filename = 'REKAP_PEMINJAMAN_' . strtoupper($jenis) . '_' . Carbon::now()->format('Ymd_His') . '.pdf';

        return $pdf->setPaper('a4', 'portrait')->stream($filename);
    }
}
