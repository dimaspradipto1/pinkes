<?php

namespace Tests\Feature\Admin;

use App\Models\Barang;
use App\Models\Peminjaman;
use App\Models\Pinjam;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

/**
 * Admin/Staff role checklist:
 * 1. Tombol/aksi verifikasi tersedia untuk peminjaman berstatus pending.
 * 2. Hanya admin/staf yang bisa menyelesaikan (selesai) peminjaman.
 * 6. Tambah peminjaman tidak butuh foto.
 * 10. Jam mulai == jam selesai peminjam pertama masih boleh (batas bersentuhan),
 *     tapi overlap waktu sungguhan ditolak.
 */
class PeminjamanVerifikasiTest extends TestCase
{
    use RefreshDatabase;

    private function admin(): User
    {
        return User::factory()->create(['role' => 'admin']);
    }

    private function staf(): User
    {
        return User::factory()->create(['role' => 'staf']);
    }

    private function userBiasa(): User
    {
        return User::factory()->create(['role' => 'kasi']);
    }

    public function test_store_peminjaman_tidak_memerlukan_foto(): void
    {
        $user    = $this->userBiasa();
        $barang  = Barang::factory()->create(['jumlah' => 1]);
        $pinjam  = Pinjam::factory()->create();

        $response = $this->actingAs($user)->post(route('peminjaman.store'), [
            'pinjam_id'        => $pinjam->id,
            'barang_id'        => $barang->id,
            'jumlah_pinjam'    => 1,
            'tgl_peminjaman'   => now()->toDateString(),
            'jam_peminjaman'   => '08:00',
            'tgl_pengembalian' => now()->toDateString(),
            'jam_pengembalian' => '10:00',
        ]);

        $response->assertRedirect(route('peminjaman.index'));
        $this->assertDatabaseHas('peminjamen', [
            'user_id'   => $user->id,
            'barang_id' => $barang->id,
            'status'    => 'pending',
        ]);
        // Stok belum berkurang selama masih pending
        $this->assertSame(1, $barang->fresh()->jumlah);
    }

    public function test_admin_bisa_verifikasi_peminjaman_pending_dan_stok_berkurang(): void
    {
        $admin  = $this->admin();
        $barang = Barang::factory()->create(['jumlah' => 1]);
        $p = Peminjaman::create([
            'user_id' => $this->userBiasa()->id,
            'barang_id' => $barang->id,
            'pinjam_id' => Pinjam::factory()->create()->id,
            'tgl_peminjaman' => now()->toDateString(),
            'jam_peminjaman' => '08:00',
            'tgl_pengembalian' => now()->toDateString(),
            'jam_pengembalian' => '10:00',
            'status' => 'pending',
        ]);

        $response = $this->actingAs($admin)->post(route('peminjaman.verifikasi', $p));

        $response->assertRedirect();
        $this->assertSame('validasi', $p->fresh()->status);
        $this->assertSame(0, $barang->fresh()->jumlah);
    }

    public function test_user_biasa_tidak_bisa_verifikasi(): void
    {
        $user = $this->userBiasa();
        $p = Peminjaman::create([
            'user_id' => $user->id,
            'barang_id' => Barang::factory()->create()->id,
            'pinjam_id' => Pinjam::factory()->create()->id,
            'tgl_peminjaman' => now()->toDateString(),
            'jam_peminjaman' => '08:00',
            'tgl_pengembalian' => now()->toDateString(),
            'jam_pengembalian' => '10:00',
            'status' => 'pending',
        ]);

        $this->actingAs($user)->post(route('peminjaman.verifikasi', $p))->assertForbidden();
    }

    public function test_hanya_admin_atau_staf_yang_bisa_menyelesaikan_peminjaman(): void
    {
        $barang = Barang::factory()->create(['jumlah' => 0]);
        $p = Peminjaman::create([
            'user_id' => $this->userBiasa()->id,
            'barang_id' => $barang->id,
            'pinjam_id' => Pinjam::factory()->create()->id,
            'tgl_peminjaman' => now()->toDateString(),
            'jam_peminjaman' => '08:00',
            'tgl_pengembalian' => now()->toDateString(),
            'jam_pengembalian' => '10:00',
            'status' => 'validasi',
        ]);

        // User biasa (bahkan pemilik peminjaman) tidak boleh menyelesaikan
        $this->actingAs($this->userBiasa())->post(route('peminjaman.selesai', $p))->assertForbidden();
        $this->assertSame('validasi', $p->fresh()->status);

        // Staf boleh menyelesaikan
        $this->actingAs($this->staf())->post(route('peminjaman.selesai', $p));
        $this->assertSame('selesai', $p->fresh()->status);
        $this->assertSame(1, $barang->fresh()->jumlah);
    }

    public function test_boundary_time_bersentuhan_tetap_bisa_diverifikasi_walau_stok_tinggal_1(): void
    {
        $admin  = $this->admin();
        $barang = Barang::factory()->create(['jumlah' => 1]);
        $pinjam = Pinjam::factory()->create();
        $tgl    = now()->toDateString();

        // Peminjam pertama: 08:00 - 10:00, sudah divalidasi (stok jadi 0)
        $pertama = Peminjaman::create([
            'user_id' => $this->userBiasa()->id,
            'barang_id' => $barang->id,
            'pinjam_id' => $pinjam->id,
            'tgl_peminjaman' => $tgl,
            'jam_peminjaman' => '08:00',
            'tgl_pengembalian' => $tgl,
            'jam_pengembalian' => '10:00',
            'status' => 'validasi',
        ]);
        $barang->decrement('jumlah'); // simulasikan stok sudah terpakai peminjam pertama

        // Peminjam kedua: mulai jam 10:00 (persis jam selesai peminjam pertama)
        $kedua = Peminjaman::create([
            'user_id' => $this->userBiasa()->id,
            'barang_id' => $barang->id,
            'pinjam_id' => $pinjam->id,
            'tgl_peminjaman' => $tgl,
            'jam_peminjaman' => '10:00',
            'tgl_pengembalian' => $tgl,
            'jam_pengembalian' => '12:00',
            'status' => 'pending',
        ]);

        $response = $this->actingAs($admin)->post(route('peminjaman.verifikasi', $kedua));

        $response->assertRedirect();
        $this->assertSame('validasi', $kedua->fresh()->status, 'Jam mulai == jam selesai peminjam pertama harus tetap boleh.');
    }

    public function test_overlap_waktu_sungguhan_ditolak_saat_verifikasi(): void
    {
        $admin  = $this->admin();
        $barang = Barang::factory()->create(['jumlah' => 1]);
        $pinjam = Pinjam::factory()->create();
        $tgl    = now()->toDateString();

        $pertama = Peminjaman::create([
            'user_id' => $this->userBiasa()->id,
            'barang_id' => $barang->id,
            'pinjam_id' => $pinjam->id,
            'tgl_peminjaman' => $tgl,
            'jam_peminjaman' => '08:00',
            'tgl_pengembalian' => $tgl,
            'jam_pengembalian' => '10:00',
            'status' => 'validasi',
        ]);
        $barang->decrement('jumlah');

        // Peminjam kedua mulai jam 09:00 -> masih di dalam rentang peminjam pertama (overlap sungguhan)
        $kedua = Peminjaman::create([
            'user_id' => $this->userBiasa()->id,
            'barang_id' => $barang->id,
            'pinjam_id' => $pinjam->id,
            'tgl_peminjaman' => $tgl,
            'jam_peminjaman' => '09:00',
            'tgl_pengembalian' => $tgl,
            'jam_pengembalian' => '11:00',
            'status' => 'pending',
        ]);

        $this->actingAs($admin)->post(route('peminjaman.verifikasi', $kedua));

        $this->assertSame('pending', $kedua->fresh()->status, 'Overlap waktu sungguhan harus ditolak (tetap pending).');
    }

    public function test_admin_bisa_hapus_peminjaman(): void
    {
        $admin = $this->admin();
        $p = Peminjaman::create([
            'user_id' => $this->userBiasa()->id,
            'barang_id' => Barang::factory()->create()->id,
            'pinjam_id' => Pinjam::factory()->create()->id,
            'tgl_peminjaman' => now()->toDateString(),
            'jam_peminjaman' => '08:00',
            'tgl_pengembalian' => now()->toDateString(),
            'jam_pengembalian' => '10:00',
            'status' => 'pending',
        ]);

        $response = $this->actingAs($admin)->delete(route('peminjaman.destroy', $p));

        $response->assertRedirect(route('peminjaman.index'));
        $this->assertDatabaseMissing('peminjamen', ['id' => $p->id]);
    }
}
