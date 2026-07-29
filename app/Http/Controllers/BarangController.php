<?php

namespace App\Http\Controllers;

use App\DataTables\BarangDataTable;
use App\Models\Barang;
use App\Models\FotoBarang;
use Illuminate\Http\Request;
use Illuminate\Support\Str;
use Illuminate\Support\Facades\Storage;
use RealRashid\SweetAlert\Facades\Alert;

class BarangController extends Controller
{
    public function index(BarangDataTable $dataTable)
    {
        return $dataTable->render('pages.barang.index');
    }

    public function create()
    {
        return view('pages.barang.create');
    }

    public function store(Request $request)
    {
        $request->validate([
            'kode_barang'   => 'required|string|max:50|unique:barangs,kode_barang',
            'nama_barang'   => 'required|string|max:255',
            'jumlah'        => 'required|integer|min:1',
            'keterangan'    => 'nullable|string',
            'foto_barang'   => 'nullable|array',
            'foto_barang.*' => 'image|mimes:jpg,jpeg,png,webp',
        ], [
            'kode_barang.required'   => 'Kode barang wajib diisi.',
            'kode_barang.unique'     => 'Kode barang sudah terdaftar.',
            'nama_barang.required'   => 'Nama barang wajib diisi.',
            'jumlah.required'        => 'Jumlah wajib diisi.',
            'jumlah.min'             => 'Jumlah minimal 1.',
            'foto_barang.*.image'    => 'Setiap file harus berupa gambar.',
        ]);

        // Buat record barang dulu
        $barang = Barang::create([
            'kode_barang' => $request->kode_barang,
            'nama_barang' => $request->nama_barang,
            'slug'        => Str::slug($request->nama_barang . '-' . $request->kode_barang),
            'jumlah'      => $request->jumlah,
            'keterangan'  => $request->keterangan,
            'foto_barang' => '-',
        ]);

        // Upload setiap foto SEKALI, foto pertama jadi foto utama
        if ($request->hasFile('foto_barang')) {
            $fotoUtama = null;
            foreach ($request->file('foto_barang') as $index => $foto) {
                $path = $foto->store('barang', 'public');
                if ($index === 0) {
                    $fotoUtama = $path;
                }
                FotoBarang::create([
                    'barang_id' => $barang->id,
                    'nama_foto' => $path,
                ]);
            }
            if ($fotoUtama) {
                $barang->update(['foto_barang' => $fotoUtama]);
            }
        }

        Alert::success('Berhasil', 'Barang beserta foto berhasil ditambahkan.');
        return redirect()->route('barang.index');
    }

    public function show(Barang $barang)
    {
        $barang->load('fotos');
        return view('pages.barang.show', compact('barang'));
    }

    public function edit(Barang $barang)
    {
        $barang->load('fotos');
        return view('pages.barang.edit', compact('barang'));
    }

    public function update(Request $request, Barang $barang)
    {
        $request->validate([
            'kode_barang'   => 'required|string|max:50|unique:barangs,kode_barang,' . $barang->id,
            'nama_barang'   => 'required|string|max:255',
            'jumlah'        => 'required|integer|min:1',
            'keterangan'    => 'nullable|string',
            'foto_barang'   => 'nullable|array',
            'foto_barang.*' => 'image|mimes:jpg,jpeg,png,webp',
        ], [
            'kode_barang.required'   => 'Kode barang wajib diisi.',
            'kode_barang.unique'     => 'Kode barang sudah terdaftar.',
            'nama_barang.required'   => 'Nama barang wajib diisi.',
            'jumlah.required'        => 'Jumlah wajib diisi.',
            'foto_barang.*.image'    => 'Setiap file harus berupa gambar.',
        ]);

        $data = [
            'kode_barang' => $request->kode_barang,
            'nama_barang' => $request->nama_barang,
            'slug'        => Str::slug($request->nama_barang . '-' . $request->kode_barang),
            'jumlah'      => $request->jumlah,
            'keterangan'  => $request->keterangan,
        ];

        // Jika ada foto baru → update foto utama & tambah ke fotobarang
        if ($request->hasFile('foto_barang')) {
            // Hapus semua foto lama dari storage
            if ($barang->foto_barang && Storage::disk('public')->exists($barang->foto_barang)) {
                Storage::disk('public')->delete($barang->foto_barang);
            }
            foreach ($barang->fotos()->get() as $foto) {
                if (Storage::disk('public')->exists($foto->nama_foto)) {
                    Storage::disk('public')->delete($foto->nama_foto);
                }
            }
            $barang->fotos()->delete();

            // Upload foto baru
            $fotoUtama = $request->file('foto_barang')[0]->store('barang', 'public');
            $data['foto_barang'] = $fotoUtama;

            foreach ($request->file('foto_barang') as $foto) {
                $path = $foto->store('barang', 'public');
                FotoBarang::create([
                    'barang_id' => $barang->id,
                    'nama_foto' => $path,
                ]);
            }
        }

        $barang->update($data);

        Alert::success('Berhasil', 'Data barang berhasil diperbarui.');
        return redirect()->route('barang.index');
    }

    public function destroy(Barang $barang)
    {
        // Hapus semua foto dari storage
        if ($barang->foto_barang && Storage::disk('public')->exists($barang->foto_barang)) {
            Storage::disk('public')->delete($barang->foto_barang);
        }
        foreach ($barang->fotos()->get() as $foto) {
            if (Storage::disk('public')->exists($foto->nama_foto)) {
                Storage::disk('public')->delete($foto->nama_foto);
            }
        }

        $barang->delete(); // cascade delete foto_barangs

        Alert::success('Berhasil', 'Barang berhasil dihapus.');
        return redirect()->route('barang.index');
    }
}
