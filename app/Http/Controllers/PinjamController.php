<?php

namespace App\Http\Controllers;

use App\DataTables\PinjamDataTable;
use App\Models\Pinjam;
use Illuminate\Http\Request;
use Illuminate\Support\Str;
use RealRashid\SweetAlert\Facades\Alert;

class PinjamController extends Controller
{
    public function index(PinjamDataTable $dataTable)
    {
        return $dataTable->render('pages.pinjam.index');
    }

    public function create()
    {
        return view('pages.pinjam.create');
    }

    public function store(Request $request)
    {
        $request->validate([
            'tujuan_pinjam' => 'required|string|max:255|unique:pinjams,tujuan_pinjam',
        ], [
            'tujuan_pinjam.required' => 'Tujuan pinjam wajib diisi.',
            'tujuan_pinjam.unique'   => 'Tujuan pinjam sudah terdaftar.',
        ]);

        Pinjam::create([
            'tujuan_pinjam' => $request->tujuan_pinjam,
            'slug'          => Str::slug($request->tujuan_pinjam),
        ]);

        Alert::success('Berhasil', 'Tujuan pinjam berhasil ditambahkan.');
        return redirect()->route('pinjam.index');
    }

    public function edit(Pinjam $pinjam)
    {
        return view('pages.pinjam.edit', compact('pinjam'));
    }

    public function update(Request $request, Pinjam $pinjam)
    {
        $request->validate([
            'tujuan_pinjam' => 'required|string|max:255|unique:pinjams,tujuan_pinjam,' . $pinjam->id,
        ], [
            'tujuan_pinjam.required' => 'Tujuan pinjam wajib diisi.',
            'tujuan_pinjam.unique'   => 'Tujuan pinjam sudah terdaftar.',
        ]);

        $pinjam->update([
            'tujuan_pinjam' => $request->tujuan_pinjam,
            'slug'          => Str::slug($request->tujuan_pinjam),
        ]);

        Alert::success('Berhasil', 'Tujuan pinjam berhasil diperbarui.');
        return redirect()->route('pinjam.index');
    }

    public function destroy(Pinjam $pinjam)
    {
        $pinjam->delete();

        Alert::success('Berhasil', 'Tujuan pinjam berhasil dihapus.');
        return redirect()->route('pinjam.index');
    }
}
