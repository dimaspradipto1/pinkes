<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Symfony\Component\HttpFoundation\Response;

class Checkrole
{
    public function handle(Request $request, Closure $next): Response
    {
        $role = Auth::user()->role;
        $path = $request->path();

        // Role user biasa: kabid, kasubag, kepalabidang, kasi
        $limitedRoles = ['kabid', 'kasubag', 'kepalabidang', 'kasi'];

        if (in_array($role, $limitedRoles)) {
            $allowedPaths = [
                'dashboard',
                'peminjaman',
                'rekapitulasi',
                'profile',
                'account-setting',
                'logout',
            ];

            $isAllowed = false;
            foreach ($allowedPaths as $allowed) {
                if (str_starts_with($path, $allowed) || $path === $allowed) {
                    $isAllowed = true;
                    break;
                }
            }

            if (!$isAllowed) {
                abort(403, 'Anda tidak memiliki hak akses ke halaman ini.');
            }
        }

        // Staf tidak bisa akses manajemen user
        if ($role === 'staf') {
            if (str_starts_with($path, 'user')) {
                abort(403, 'Hanya Admin yang dapat mengelola data pengguna.');
            }
        }

        return $next($request);
    }
}
