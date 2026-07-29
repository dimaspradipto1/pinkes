<?php

namespace App\Http\Controllers;

use App\Models\FotoBarang;
use Illuminate\Support\Facades\Storage;
use RealRashid\SweetAlert\Facades\Alert;

class FotoBarangController extends Controller
{
    public function destroy(FotoBarang $fotobarang)
    {
        if ($fotobarang->nama_foto && Storage::disk('public')->exists($fotobarang->nama_foto)) {
            Storage::disk('public')->delete($fotobarang->nama_foto);
        }

        $barangId = $fotobarang->barang_id;
        $fotobarang->delete();

        Alert::success('Berhasil', 'Foto berhasil dihapus.');

        return redirect()->route('barang.edit', $barangId);
    }
}
