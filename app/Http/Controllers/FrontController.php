<?php

namespace App\Http\Controllers;

use App\Models\Barang;
use App\Models\Ruangan;

class FrontController extends Controller
{
    public function index()
    {
        $barangs  = Barang::with('fotos')->orderBy('nama_barang')->get();
        $ruangans = Ruangan::with('fotos')->orderBy('nama_ruangan')->get();

        return view('front.index', compact('barangs', 'ruangans'));
    }
}
