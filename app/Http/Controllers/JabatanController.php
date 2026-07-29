<?php

namespace App\Http\Controllers;

use App\DataTables\JabatanDataTable;
use App\Models\Jabatan;
use Illuminate\Http\Request;
use Illuminate\Support\Str;
use RealRashid\SweetAlert\Facades\Alert;

class JabatanController extends Controller
{
    public function index(JabatanDataTable $dataTable)
    {
        return $dataTable->render('pages.jabatan.index');
    }

    public function create()
    {
        return view('pages.jabatan.create');
    }

    public function store(Request $request)
    {
        $request->validate([
            'nama_jabatan' => 'required|string|max:255|unique:jabatans,nama_jabatan',
        ], [
            'nama_jabatan.required' => 'Nama jabatan wajib diisi.',
            'nama_jabatan.unique'   => 'Nama jabatan sudah terdaftar.',
        ]);

        Jabatan::create([
            'nama_jabatan' => $request->nama_jabatan,
            'slug'         => Str::slug($request->nama_jabatan),
        ]);

        Alert::success('Berhasil', 'Jabatan berhasil ditambahkan.');
        return redirect()->route('jabatan.index');
    }

    public function edit(Jabatan $jabatan)
    {
        return view('pages.jabatan.edit', compact('jabatan'));
    }

    public function update(Request $request, Jabatan $jabatan)
    {
        $request->validate([
            'nama_jabatan' => 'required|string|max:255|unique:jabatans,nama_jabatan,' . $jabatan->id,
        ], [
            'nama_jabatan.required' => 'Nama jabatan wajib diisi.',
            'nama_jabatan.unique'   => 'Nama jabatan sudah terdaftar.',
        ]);

        $jabatan->update([
            'nama_jabatan' => $request->nama_jabatan,
            'slug'         => Str::slug($request->nama_jabatan),
        ]);

        Alert::success('Berhasil', 'Jabatan berhasil diperbarui.');
        return redirect()->route('jabatan.index');
    }

    public function destroy(Jabatan $jabatan)
    {
        $jabatan->delete();

        Alert::success('Berhasil', 'Jabatan berhasil dihapus.');
        return redirect()->route('jabatan.index');
    }
}
