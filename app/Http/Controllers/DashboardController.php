<?php

namespace App\Http\Controllers;

use App\Models\Barang;
use App\Models\Peminjaman;
use App\Models\Ruangan;
use App\Models\User;
use Carbon\Carbon;
use Illuminate\Support\Facades\Auth;

class DashboardController extends Controller
{
    public function index()
    {
        $user = Auth::user();

        if ($user->isAdmin()) {
            // ===== ADMIN =====
            $totalBarang          = Barang::count();
            $totalRuangan         = Ruangan::count();
            $totalPeminjaman      = Peminjaman::count();
            $totalUser            = User::where('role', '!=', 'admin')->count();
            $totalMenunggu        = Peminjaman::where('status', 'pending')->count();

            $peminjamanPerBulan = Peminjaman::selectRaw("DATE_FORMAT(tgl_peminjaman, '%Y-%m') as bulan, COUNT(*) as total")
                ->where('tgl_peminjaman', '>=', now()->subMonths(11)->startOfMonth())
                ->groupBy('bulan')
                ->orderBy('bulan')
                ->get()
                ->keyBy('bulan');

            $bulanLabels = [];
            $bulanData   = [];
            for ($i = 11; $i >= 0; $i--) {
                $key           = now()->subMonths($i)->format('Y-m');
                $label         = Carbon::parse($key . '-01')->translatedFormat('M Y');
                $bulanLabels[] = $label;
                $bulanData[]   = $peminjamanPerBulan[$key]->total ?? 0;
            }

            $recentPeminjaman = Peminjaman::with(['user', 'barang', 'ruangan'])
                ->latest('created_at')
                ->take(5)
                ->get();

            return view('dashboard.index', compact(
                'totalBarang', 'totalRuangan', 'totalPeminjaman',
                'totalUser', 'totalMenunggu',
                'bulanLabels', 'bulanData', 'recentPeminjaman'
            ));
        } else {
            // ===== USER BIASA =====
            $myTotal      = Peminjaman::forUser($user->id)->count();
            $myPending    = Peminjaman::forUser($user->id)->where('status', 'pending')->count();
            $myValidasi   = Peminjaman::forUser($user->id)->where('status', 'validasi')->count();
            $mySelesai    = Peminjaman::forUser($user->id)->where('status', 'selesai')->count();

            $recentPeminjaman = Peminjaman::forUser($user->id)
                ->with(['barang', 'ruangan', 'pinjam'])
                ->latest('created_at')
                ->take(5)
                ->get();

            // Chart: peminjaman saya per bulan
            $peminjamanPerBulan = Peminjaman::forUser($user->id)
                ->selectRaw("DATE_FORMAT(tgl_peminjaman, '%Y-%m') as bulan, COUNT(*) as total")
                ->where('tgl_peminjaman', '>=', now()->subMonths(11)->startOfMonth())
                ->groupBy('bulan')
                ->orderBy('bulan')
                ->get()
                ->keyBy('bulan');

            $bulanLabels = [];
            $bulanData   = [];
            for ($i = 11; $i >= 0; $i--) {
                $key           = now()->subMonths($i)->format('Y-m');
                $label         = Carbon::parse($key . '-01')->translatedFormat('M Y');
                $bulanLabels[] = $label;
                $bulanData[]   = $peminjamanPerBulan[$key]->total ?? 0;
            }

            return view('dashboard.index', compact(
                'myTotal', 'myPending', 'myValidasi', 'mySelesai',
                'recentPeminjaman', 'bulanLabels', 'bulanData'
            ));
        }
    }
}
