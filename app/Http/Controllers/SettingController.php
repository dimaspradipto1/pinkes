<?php

namespace App\Http\Controllers;

use App\Models\Setting;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use RealRashid\SweetAlert\Facades\Alert;

class SettingController extends Controller
{
    public function index()
    {
        // Hanya Admin/Staf yang bisa mengelola settings
        if (!Auth::user()->isAdmin()) abort(403);

        $nama    = Setting::getByKey('pimpinan_nama', '');
        $nip     = Setting::getByKey('pimpinan_nip', '');
        $jabatan = Setting::getByKey('pimpinan_jabatan', 'Kepala Sub Bagian Umum dan Kepegawaian');

        return view('pages.settings.index', compact('nama', 'nip', 'jabatan'));
    }

    public function update(Request $request)
    {
        if (!Auth::user()->isAdmin()) abort(403);

        $request->validate([
            'pimpinan_nama'    => 'nullable|string|max:255',
            'pimpinan_nip'     => 'nullable|string|max:50',
            'pimpinan_jabatan' => 'nullable|string|max:255',
        ]);

        Setting::setByKey('pimpinan_nama', $request->pimpinan_nama);
        Setting::setByKey('pimpinan_nip', $request->pimpinan_nip);
        Setting::setByKey('pimpinan_jabatan', $request->pimpinan_jabatan);

        Alert::success('Berhasil', 'Pengaturan pimpinan berhasil diperbarui.');
        return redirect()->route('settings.index');
    }
}
