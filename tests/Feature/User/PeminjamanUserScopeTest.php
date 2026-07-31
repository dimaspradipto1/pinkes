<?php

namespace Tests\Feature\User;

use App\DataTables\PeminjamanDataTable;
use App\Models\Barang;
use App\Models\Peminjaman;
use App\Models\Pinjam;
use App\Models\User;
use App\Models\Ruangan;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

/**
 * Role User (kabid, kasi, kasubag, kepalabidang) checklist:
 * 1. Data Peminjaman hanya menampilkan milik sendiri.
 * 3. Bisa edit selama status pending, tidak bisa lagi setelah divalidasi, tidak ada fungsi hapus.
 */
class PeminjamanUserScopeTest extends TestCase
{
    use RefreshDatabase;

    private function buatPeminjaman(User $user, string $status = 'pending'): Peminjaman
    {
        return Peminjaman::create([
            'user_id' => $user->id,
            'barang_id' => Barang::factory()->create()->id,
            'pinjam_id' => Pinjam::factory()->create()->id,
            'tgl_peminjaman' => now()->toDateString(),
            'jam_peminjaman' => '08:00',
            'tgl_pengembalian' => now()->toDateString(),
            'jam_pengembalian' => '10:00',
            'status' => $status,
        ]);
    }

    public function test_query_datatable_peminjaman_untuk_user_biasa_hanya_miliknya(): void
    {
        $user  = User::factory()->create(['role' => 'kasi']);
        $orang_lain = User::factory()->create(['role' => 'kabid']);

        $milikSaya  = $this->buatPeminjaman($user);
        $milikOrang = $this->buatPeminjaman($orang_lain);

        $this->actingAs($user);

        $ids = (new PeminjamanDataTable())->query(new Peminjaman())->pluck('id');

        $this->assertTrue($ids->contains($milikSaya->id));
        $this->assertFalse($ids->contains($milikOrang->id));
    }

    public function test_query_datatable_peminjaman_untuk_admin_menampilkan_semua(): void
    {
        $admin = User::factory()->create(['role' => 'admin']);
        $userA = User::factory()->create(['role' => 'kasi']);
        $userB = User::factory()->create(['role' => 'kabid']);

        $pA = $this->buatPeminjaman($userA);
        $pB = $this->buatPeminjaman($userB);

        $this->actingAs($admin);

        $ids = (new PeminjamanDataTable())->query(new Peminjaman())->pluck('id');

        $this->assertTrue($ids->contains($pA->id));
        $this->assertTrue($ids->contains($pB->id));
    }

    public function test_user_biasa_tidak_bisa_lihat_peminjaman_milik_orang_lain(): void
    {
        $user       = User::factory()->create(['role' => 'kasi']);
        $orangLain  = User::factory()->create(['role' => 'kabid']);
        $p          = $this->buatPeminjaman($orangLain);

        $this->actingAs($user)->get(route('peminjaman.show', $p))->assertForbidden();
    }

    public function test_user_bisa_edit_selama_status_pending(): void
    {
        $user = User::factory()->create(['role' => 'kasi']);
        $p    = $this->buatPeminjaman($user, 'pending');

        $this->actingAs($user)->get(route('peminjaman.edit', $p))->assertOk();
    }

    public function test_user_tidak_bisa_edit_setelah_divalidasi(): void
    {
        $user = User::factory()->create(['role' => 'kasi']);
        $p    = $this->buatPeminjaman($user, 'validasi');

        $response = $this->actingAs($user)->get(route('peminjaman.edit', $p));

        $response->assertRedirect(route('peminjaman.index'));
    }

    public function test_user_bisa_hapus_peminjaman_sendiri_jika_pending(): void
    {
        $user = User::factory()->create(['role' => 'kasi']);
        $p    = $this->buatPeminjaman($user, 'pending');

        $response = $this->actingAs($user)->delete(route('peminjaman.destroy', $p));

        $response->assertRedirect(route('peminjaman.index'));
        $this->assertDatabaseMissing('peminjamen', ['id' => $p->id]);
    }

    public function test_user_tidak_bisa_hapus_peminjaman_sendiri_jika_bukan_pending(): void
    {
        $user = User::factory()->create(['role' => 'kasi']);
        $p    = $this->buatPeminjaman($user, 'validasi');

        $response = $this->actingAs($user)->delete(route('peminjaman.destroy', $p));

        $response->assertRedirect(route('peminjaman.index'));
        $this->assertDatabaseHas('peminjamen', ['id' => $p->id]);
    }

    public function test_user_tidak_bisa_hapus_peminjaman_orang_lain(): void
    {
        $user1 = User::factory()->create(['role' => 'kasi']);
        $user2 = User::factory()->create(['role' => 'kasi']);
        $p     = $this->buatPeminjaman($user2, 'pending');

        $response = $this->actingAs($user1)->delete(route('peminjaman.destroy', $p));

        $response->assertForbidden();
        $this->assertDatabaseHas('peminjamen', ['id' => $p->id]);
    }

    public function test_status_label_sesuai_ketentuan(): void
    {
        $this->assertSame('Menunggu Verifikasi', Peminjaman::$statusLabels['pending']['label']);
        $this->assertSame('Divalidasi', Peminjaman::$statusLabels['validasi']['label']);
        $this->assertSame('Ditolak', Peminjaman::$statusLabels['ditolak']['label']);
        $this->assertSame('Selesai', Peminjaman::$statusLabels['selesai']['label']);
    }

    public function test_ruangan_yang_sedang_dipinjam_tidak_tampil_di_form_create(): void
    {
        $user = User::factory()->create(['role' => 'kasi']);
        $ruangan1 = Ruangan::factory()->create();
        $ruangan2 = Ruangan::factory()->create();

        // Pinjam ruangan1 secara aktif (pending)
        Peminjaman::create([
            'user_id' => $user->id,
            'ruangan_id' => $ruangan1->id,
            'jumlah_pinjam' => 1,
            'pinjam_id' => Pinjam::factory()->create()->id,
            'tgl_peminjaman' => now()->toDateString(),
            'jam_peminjaman' => '08:00',
            'tgl_pengembalian' => now()->toDateString(),
            'jam_pengembalian' => '10:00',
            'status' => 'pending',
        ]);

        $response = $this->actingAs($user)->get(route('peminjaman.create'));

        $response->assertStatus(200);
        $response->assertDontSee($ruangan1->kode_ruangan);
        $response->assertSee($ruangan2->kode_ruangan);
    }
}
