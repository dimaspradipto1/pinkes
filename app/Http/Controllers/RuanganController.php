<?php

namespace App\Http\Controllers;

use App\DataTables\RuanganDataTable;
use App\Models\Ruangan;
use App\Models\FotoRuangan;
use Illuminate\Http\Request;
use Illuminate\Support\Str;
use Illuminate\Support\Facades\Storage;
use RealRashid\SweetAlert\Facades\Alert;

class RuanganController extends Controller
{
    public function index(RuanganDataTable $dataTable)
    {
        return $dataTable->render('pages.ruangan.index');
    }

    public function create()
    {
        return view('pages.ruangan.create');
    }

    public function store(Request $request)
    {
        $request->validate([
            'kode_ruangan'   => 'required|string|max:50|unique:ruangans,kode_ruangan',
            'nama_ruangan'   => 'required|string|max:255',
            'jumlah'         => 'required|integer|min:0',
            'keterangan'     => 'nullable|string',
            'foto_ruangan'   => 'nullable|array',
            'foto_ruangan.*' => 'image|mimes:jpg,jpeg,png,webp',
        ], [
            'kode_ruangan.required' => 'Kode ruangan wajib diisi.',
            'kode_ruangan.unique'   => 'Kode ruangan sudah terdaftar.',
            'nama_ruangan.required' => 'Nama ruangan wajib diisi.',
            'foto_ruangan.*.image'  => 'Setiap file harus berupa gambar.',
        ]);

        $ruangan = Ruangan::create([
            'kode_ruangan' => $request->kode_ruangan,
            'nama_ruangan' => $request->nama_ruangan,
            'slug'         => Str::slug($request->nama_ruangan . '-' . $request->kode_ruangan),
            'jumlah'       => $request->jumlah,
            'keterangan'   => $request->keterangan,
            'foto_ruangan' => '-',
        ]);

        // Upload setiap foto SEKALI, foto pertama jadi foto utama
        if ($request->hasFile('foto_ruangan')) {
            $fotoUtama = null;
            foreach ($request->file('foto_ruangan') as $index => $foto) {
                $path = $foto->store('ruangan', 'public');
                if ($index === 0) $fotoUtama = $path;
                FotoRuangan::create(['ruangan_id' => $ruangan->id, 'nama_foto' => $path]);
            }
            if ($fotoUtama) $ruangan->update(['foto_ruangan' => $fotoUtama]);
        }

        Alert::success('Berhasil', 'Ruangan beserta foto berhasil ditambahkan.');
        return redirect()->route('ruangan.index');
    }

    public function show(Ruangan $ruangan)
    {
        $ruangan->load('fotos');
        return view('pages.ruangan.show', compact('ruangan'));
    }

    public function edit(Ruangan $ruangan)
    {
        $ruangan->load('fotos');
        return view('pages.ruangan.edit', compact('ruangan'));
    }

    public function update(Request $request, Ruangan $ruangan)
    {
        $request->validate([
            'kode_ruangan'   => 'required|string|max:50|unique:ruangans,kode_ruangan,' . $ruangan->id,
            'nama_ruangan'   => 'required|string|max:255',
            'jumlah'         => 'required|integer|min:0',
            'keterangan'     => 'nullable|string',
            'foto_ruangan'   => 'nullable|array',
            'foto_ruangan.*' => 'image|mimes:jpg,jpeg,png,webp',
        ], [
            'kode_ruangan.required' => 'Kode ruangan wajib diisi.',
            'kode_ruangan.unique'   => 'Kode ruangan sudah terdaftar.',
            'nama_ruangan.required' => 'Nama ruangan wajib diisi.',
            'foto_ruangan.*.image'  => 'Setiap file harus berupa gambar.',
        ]);

        $data = [
            'kode_ruangan' => $request->kode_ruangan,
            'nama_ruangan' => $request->nama_ruangan,
            'slug'         => Str::slug($request->nama_ruangan . '-' . $request->kode_ruangan),
            'jumlah'       => $request->jumlah,
            'keterangan'   => $request->keterangan,
        ];

        if ($request->hasFile('foto_ruangan')) {
            // Hapus semua foto lama dari storage
            if ($ruangan->foto_ruangan && $ruangan->foto_ruangan !== '-' && Storage::disk('public')->exists($ruangan->foto_ruangan)) {
                Storage::disk('public')->delete($ruangan->foto_ruangan);
            }
            foreach ($ruangan->fotos()->get() as $foto) {
                if (Storage::disk('public')->exists($foto->nama_foto)) {
                    Storage::disk('public')->delete($foto->nama_foto);
                }
            }
            $ruangan->fotos()->delete();

            // Upload foto baru SEKALI per file
            $fotoUtama = null;
            foreach ($request->file('foto_ruangan') as $index => $foto) {
                $path = $foto->store('ruangan', 'public');
                if ($index === 0) $fotoUtama = $path;
                FotoRuangan::create(['ruangan_id' => $ruangan->id, 'nama_foto' => $path]);
            }
            if ($fotoUtama) $data['foto_ruangan'] = $fotoUtama;
        }

        $ruangan->update($data);

        Alert::success('Berhasil', 'Data ruangan berhasil diperbarui.');
        return redirect()->route('ruangan.index');
    }

    public function destroy(Ruangan $ruangan)
    {
        if ($ruangan->foto_ruangan && $ruangan->foto_ruangan !== '-' && Storage::disk('public')->exists($ruangan->foto_ruangan)) {
            Storage::disk('public')->delete($ruangan->foto_ruangan);
        }
        foreach ($ruangan->fotos()->get() as $foto) {
            if (Storage::disk('public')->exists($foto->nama_foto)) {
                Storage::disk('public')->delete($foto->nama_foto);
            }
        }

        $ruangan->delete();

        Alert::success('Berhasil', 'Ruangan berhasil dihapus.');
        return redirect()->route('ruangan.index');
    }
}
