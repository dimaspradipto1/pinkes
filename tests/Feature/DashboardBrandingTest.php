<?php

namespace Tests\Feature;

use App\Models\Barang;
use App\Models\Peminjaman;
use App\Models\Pinjam;
use App\Models\Ruangan;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

/**
 * 9/10/11 (Admin) & 8/9/10/12 (User): branding sistem peminjaman (bukan inventaris),
 * halaman depan, menu cepat, dan "peminjaman terbaru" di-scope per user.
 */
class DashboardBrandingTest extends TestCase
{
    use RefreshDatabase;

    public function test_halaman_depan_menampilkan_judul_sistem_peminjaman(): void
    {
        Ruangan::factory()->create();
        Barang::factory()->create();

        $response = $this->get(route('index'));

        $response->assertOk();
        $response->assertSee('Sistem Informasi Peminjaman Ruangan dan Barang Dinas Kesehatan Kota Batam');
        $response->assertDontSee('Sistem Informasi Inventaris');
    }

    public function test_dashboard_tidak_lagi_menyebut_sistem_inventaris(): void
    {
        $admin = User::factory()->create(['role' => 'admin']);

        $response = $this->actingAs($admin)->get(route('dashboard'));

        $response->assertOk();
        $response->assertSee('Sistem Informasi Peminjaman Ruangan dan Barang');
        $response->assertDontSee('Sistem Informasi Inventaris');
    }

    public function test_menu_cepat_user_biasa_tidak_ada_data_barang_ruangan_pengguna(): void
    {
        $user = User::factory()->create(['role' => 'kasi']);

        $response = $this->actingAs($user)->get(route('dashboard'));

        $response->assertOk();
        $response->assertDontSee('Data Barang');
        $response->assertDontSee('Data Ruangan');
        $response->assertDontSee(route('user.index'), false);
    }

    public function test_menu_cepat_admin_tetap_ada_data_barang_dan_ruangan(): void
    {
        $admin = User::factory()->create(['role' => 'admin']);

        $response = $this->actingAs($admin)->get(route('dashboard'));

        $response->assertSee('Data Barang');
        $response->assertSee('Data Ruangan');
    }

    public function test_peminjaman_terbaru_di_dashboard_user_hanya_miliknya(): void
    {
        $user      = User::factory()->create(['role' => 'kasi']);
        $orangLain = User::factory()->create(['role' => 'kabid']);
        $pinjam    = Pinjam::factory()->create();

        Peminjaman::create([
            'user_id' => $user->id,
            'barang_id' => Barang::factory()->create(['nama_barang' => 'Laptop Saya'])->id,
            'pinjam_id' => $pinjam->id,
            'tgl_peminjaman' => now()->toDateString(),
            'jam_peminjaman' => '08:00',
            'tgl_pengembalian' => now()->toDateString(),
            'jam_pengembalian' => '10:00',
            'status' => 'pending',
        ]);
        Peminjaman::create([
            'user_id' => $orangLain->id,
            'barang_id' => Barang::factory()->create(['nama_barang' => 'Laptop Orang Lain'])->id,
            'pinjam_id' => $pinjam->id,
            'tgl_peminjaman' => now()->toDateString(),
            'jam_peminjaman' => '08:00',
            'tgl_pengembalian' => now()->toDateString(),
            'jam_pengembalian' => '10:00',
            'status' => 'pending',
        ]);

        $response = $this->actingAs($user)->get(route('dashboard'));

        $response->assertSee('Laptop Saya');
        $response->assertDontSee('Laptop Orang Lain');
    }

    public function test_menu_foto_tidak_lagi_terdaftar(): void
    {
        $this->assertFalse(\Illuminate\Support\Facades\Route::has('fotobarang.index'));
        $this->assertFalse(\Illuminate\Support\Facades\Route::has('fotoruangan.index'));
        $this->assertFalse(\Illuminate\Support\Facades\Route::has('fotopeminjaman.index'));
        $this->assertFalse(\Illuminate\Support\Facades\Route::has('fotopeminjaman.destroy'));
    }
}
