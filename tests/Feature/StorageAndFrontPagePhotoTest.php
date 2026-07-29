<?php

namespace Tests\Feature;

use App\Models\Barang;
use App\Models\FotoBarang;
use App\Models\FotoRuangan;
use App\Models\Ruangan;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

/**
 * Regresi untuk dua bug nyata yang ditemukan saat audit:
 * - public/storage bukan symlink ke storage/app/public -> semua foto ter-upload 404.
 * - Halaman depan salah nama kolom (foto_barang vs nama_foto) saat fallback ke foto galeri barang.
 */
class StorageAndFrontPagePhotoTest extends TestCase
{
    use RefreshDatabase;

    public function test_public_storage_tertaut_ke_storage_app_public(): void
    {
        // is_link() tidak mendeteksi Windows junction, jadi verifikasi secara fungsional:
        // file yang ditulis ke storage/app/public harus muncul lewat public/storage.
        \Illuminate\Support\Facades\Storage::disk('public')->put('regresi-test/cek.txt', 'ok');

        $this->assertFileExists(public_path('storage/regresi-test/cek.txt'), 'public/storage harus tertaut ke storage/app/public (symlink/junction), bukan folder kosong terpisah, agar foto ter-upload bisa diakses.');

        \Illuminate\Support\Facades\Storage::disk('public')->deleteDirectory('regresi-test');
    }

    public function test_halaman_depan_menampilkan_foto_galeri_barang_saat_foto_utama_kosong(): void
    {
        $barang = Barang::factory()->create(['foto_barang' => '-']);
        $foto = FotoBarang::create(['barang_id' => $barang->id, 'nama_foto' => 'barang/contoh-foto.jpg']);

        $response = $this->get(route('index'));

        $response->assertOk();
        $response->assertSee(asset('storage/' . $foto->nama_foto), false);
    }

    public function test_halaman_depan_menampilkan_foto_galeri_ruangan_saat_foto_utama_kosong(): void
    {
        $ruangan = Ruangan::factory()->create(['foto_ruangan' => '-']);
        $foto = FotoRuangan::create(['ruangan_id' => $ruangan->id, 'nama_foto' => 'ruangan/contoh-foto.jpg']);

        $response = $this->get(route('index'));

        $response->assertOk();
        $response->assertSee(asset('storage/' . $foto->nama_foto), false);
    }
}
