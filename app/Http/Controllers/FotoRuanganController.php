<?php

namespace App\Http\Controllers;

use App\Models\FotoRuangan;
use Illuminate\Support\Facades\Storage;
use RealRashid\SweetAlert\Facades\Alert;

class FotoRuanganController extends Controller
{
    public function destroy(FotoRuangan $fotoruangan)
    {
        if ($fotoruangan->nama_foto && Storage::disk('public')->exists($fotoruangan->nama_foto)) {
            Storage::disk('public')->delete($fotoruangan->nama_foto);
        }

        $ruanganId = $fotoruangan->ruangan_id;
        $fotoruangan->delete();

        Alert::success('Berhasil', 'Foto ruangan berhasil dihapus.');

        return redirect()->route('ruangan.edit', $ruanganId);
    }
}
