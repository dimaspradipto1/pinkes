<?php

namespace App\Http\Controllers;

use App\DataTables\UserDataTable;
use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Hash;
use RealRashid\SweetAlert\Facades\Alert;

class UserController extends Controller
{
    public function index(UserDataTable $dataTable)
    {
        return $dataTable->render('pages.user.index');
    }

    public function create()
    {
        return view('pages.user.create');
    }

    public function store(Request $request)
    {
        $request->validate([
            'name'      => 'required|string|max:255',
            'email'     => 'required|email|unique:users,email',
            'password'  => 'required|string|min:8|confirmed',
            'role'      => 'required|in:admin,kasi,staf,kabid,kasubag,kepalabidang',
            'is_active' => 'required|in:1,0',
        ], [
            'name.required'      => 'Nama wajib diisi.',
            'email.required'     => 'Email wajib diisi.',
            'email.unique'       => 'Email sudah digunakan.',
            'password.required'  => 'Password wajib diisi.',
            'password.min'       => 'Password minimal 8 karakter.',
            'password.confirmed' => 'Konfirmasi password tidak cocok.',
            'role.required'      => 'Role wajib dipilih.',
            'is_active.required' => 'Status wajib dipilih.',
        ]);

        User::create([
            'name'      => $request->name,
            'email'     => $request->email,
            'password'  => Hash::make($request->password),
            'role'      => $request->role,
            'is_active' => $request->is_active,
        ]);

        Alert::success('Berhasil', 'Pengguna berhasil ditambahkan.');
        return redirect()->route('user.index');
    }

    public function edit(string $id)
    {
        $user = User::findOrFail($id);
        return view('pages.user.edit', compact('user'));
    }

    public function update(Request $request, string $id)
    {
        $user = User::findOrFail($id);

        $request->validate([
            'name'      => 'required|string|max:255',
            'email'     => 'required|email|unique:users,email,' . $user->id,
            'role'      => 'required|in:admin,kasi,staf,kabid,kasubag,kepalabidang',
            'is_active' => 'required|in:1,0',
            'password'  => 'nullable|string|min:8|confirmed',
        ], [
            'name.required'      => 'Nama wajib diisi.',
            'email.required'     => 'Email wajib diisi.',
            'email.unique'       => 'Email sudah digunakan.',
            'role.required'      => 'Role wajib dipilih.',
            'is_active.required' => 'Status wajib dipilih.',
            'password.min'       => 'Password minimal 8 karakter.',
            'password.confirmed' => 'Konfirmasi password tidak cocok.',
        ]);

        $data = [
            'name'      => $request->name,
            'email'     => $request->email,
            'role'      => $request->role,
            'is_active' => $request->is_active,
        ];

        if ($request->filled('password')) {
            $data['password'] = Hash::make($request->password);
        }

        $user->update($data);

        Alert::success('Berhasil', 'Data pengguna berhasil diperbarui.');
        return redirect()->route('user.index');
    }

    public function destroy(string $id)
    {
        $user = User::findOrFail($id);

        if ($user->id === auth()->id()) {
            Alert::error('Gagal', 'Anda tidak dapat menghapus akun yang sedang digunakan.');
            return redirect()->route('user.index');
        }

        $user->delete();

        Alert::success('Berhasil', 'Pengguna berhasil dihapus.');
        return redirect()->route('user.index');
    }

    // ===== PROFIL =====

    public function profile()
    {
        $user = Auth::user();
        return view('pages.profile.show', compact('user'));
    }

    public function updateProfile(Request $request)
    {
        $user = Auth::user();

        $request->validate([
            'name'            => 'required|string|max:255',
            'nip'             => 'nullable|string|max:30',
            'jabatan_nama'    => 'nullable|string|max:255',
            'pangkat_golongan'=> 'nullable|string|max:100',
        ], [
            'name.required' => 'Nama lengkap wajib diisi.',
        ]);

        $user->update([
            'name'             => $request->name,
            'nip'              => $request->nip,
            'jabatan_nama'     => $request->jabatan_nama,
            'pangkat_golongan' => $request->pangkat_golongan,
        ]);

        Alert::success('Berhasil', 'Profil berhasil diperbarui.');
        return redirect()->route('profile.show');
    }

    // ===== ACCOUNT SETTING =====

    public function accountSetting()
    {
        return view('pages.profile.account');
    }

    public function updatePassword(Request $request)
    {
        $request->validate([
            'current_password' => 'required',
            'password'         => 'required|string|min:8|confirmed',
        ], [
            'current_password.required' => 'Password lama wajib diisi.',
            'password.required'         => 'Password baru wajib diisi.',
            'password.min'              => 'Password baru minimal 8 karakter.',
            'password.confirmed'        => 'Konfirmasi password tidak cocok.',
        ]);

        $user = Auth::user();

        if (!Hash::check($request->current_password, $user->password)) {
            Alert::error('Gagal', 'Password lama tidak sesuai.');
            return back();
        }

        $user->update(['password' => Hash::make($request->password)]);

        Alert::success('Berhasil', 'Password berhasil diperbarui.');
        return redirect()->route('account.setting');
    }
}
