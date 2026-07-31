<?php

namespace Tests\Feature;

use App\Models\Barang;
use App\Models\Peminjaman;
use App\Models\Pinjam;
use App\Models\Ruangan;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Support\Facades\View;
use Tests\TestCase;

/**
 * 5. Rekapitulasi peminjaman ruangan & barang jadi satu menu, bisa difilter (barang/ruangan/semua).
 * 4. Print laporan pakai kop Dinkes + ttd Kasubag Umum dan Kepegawaian.
 * Role User: rekapitulasi hanya data milik sendiri, tidak ada tombol cetak PDF.
 */
class RekapitulasiTest extends TestCase
{
    use RefreshDatabase;

    private function buatPeminjaman(User $user, ?Barang $barang, ?Ruangan $ruangan): Peminjaman
    {
        return Peminjaman::create([
            'user_id'          => $user->id,
            'barang_id'        => $barang?->id,
            'ruangan_id'       => $ruangan?->id,
            'pinjam_id'        => Pinjam::factory()->create()->id,
            'tgl_peminjaman'   => now()->toDateString(),
            'jam_peminjaman'   => '08:00',
            'tgl_pengembalian' => now()->toDateString(),
            'jam_pengembalian' => '10:00',
            'status'           => 'validasi',
        ]);
    }

    public function test_rekapitulasi_menampilkan_barang_dan_ruangan_dalam_satu_halaman(): void
    {
        $admin = User::factory()->create(['role' => 'admin']);
        $this->buatPeminjaman(User::factory()->create(['role' => 'kasi']), Barang::factory()->create(), null);
        $this->buatPeminjaman(User::factory()->create(['role' => 'kasi']), null, Ruangan::factory()->create());

        $response = $this->actingAs($admin)->get(route('rekapitulasi.index'));

        $response->assertOk();
        $response->assertSee('Barang');
        $response->assertSee('Ruangan');
    }

    public function test_filter_jenis_barang_hanya_menampilkan_peminjaman_barang(): void
    {
        $admin  = User::factory()->create(['role' => 'admin']);
        $barang = Barang::factory()->create(['nama_barang' => 'Kursi Lipat']);
        $ruangan = Ruangan::factory()->create(['nama_ruangan' => 'Aula Serbaguna']);

        $this->buatPeminjaman(User::factory()->create(['role' => 'kasi']), $barang, null);
        $this->buatPeminjaman(User::factory()->create(['role' => 'kasi']), null, $ruangan);

        $response = $this->actingAs($admin)->get(route('rekapitulasi.index', ['jenis' => 'barang']));

        $response->assertSee('Kursi Lipat');
        $response->assertDontSee('Aula Serbaguna');
    }

    public function test_filter_jenis_ruangan_hanya_menampilkan_peminjaman_ruangan(): void
    {
        $admin   = User::factory()->create(['role' => 'admin']);
        $barang  = Barang::factory()->create(['nama_barang' => 'Kursi Lipat']);
        $ruangan = Ruangan::factory()->create(['nama_ruangan' => 'Aula Serbaguna']);

        $this->buatPeminjaman(User::factory()->create(['role' => 'kasi']), $barang, null);
        $this->buatPeminjaman(User::factory()->create(['role' => 'kasi']), null, $ruangan);

        $response = $this->actingAs($admin)->get(route('rekapitulasi.index', ['jenis' => 'ruangan']));

        $response->assertSee('Aula Serbaguna');
        $response->assertDontSee('Kursi Lipat');
    }

    public function test_user_biasa_hanya_melihat_rekapitulasi_miliknya_sendiri(): void
    {
        $user      = User::factory()->create(['role' => 'kasi', 'name' => 'Budi Kasi']);
        $orangLain = User::factory()->create(['role' => 'kabid', 'name' => 'Ani Kabid']);

        $this->buatPeminjaman($user, Barang::factory()->create(['nama_barang' => 'Proyektor Saya']), null);
        $this->buatPeminjaman($orangLain, Barang::factory()->create(['nama_barang' => 'Proyektor Orang Lain']), null);

        $response = $this->actingAs($user)->get(route('rekapitulasi.index'));

        $response->assertSee('Proyektor Saya');
        $response->assertDontSee('Proyektor Orang Lain');
    }

    public function test_user_biasa_tidak_melihat_tombol_cetak_pdf(): void
    {
        $user = User::factory()->create(['role' => 'kasi']);

        $response = $this->actingAs($user)->get(route('rekapitulasi.index'));

        $response->assertDontSee(route('rekapitulasi.pdf'), false);
    }

    public function test_admin_melihat_tombol_cetak_pdf(): void
    {
        $admin = User::factory()->create(['role' => 'admin']);

        $response = $this->actingAs($admin)->get(route('rekapitulasi.index'));

        $response->assertSee('PDF');
    }

    public function test_user_biasa_tidak_bisa_akses_export_pdf(): void
    {
        $user = User::factory()->create(['role' => 'kasi']);

        $this->actingAs($user)->get(route('rekapitulasi.pdf'))->assertForbidden();
    }

    public function test_admin_bisa_download_pdf(): void
    {
        $admin = User::factory()->create(['role' => 'admin']);
        $this->buatPeminjaman(User::factory()->create(['role' => 'kasi']), Barang::factory()->create(), null);

        $response = $this->actingAs($admin)->get(route('rekapitulasi.pdf'));

        $response->assertOk();
        $response->assertHeader('content-type', 'application/pdf');
    }

    public function test_pdf_menampilkan_kop_dinkes_dan_ttd_kasubag_dinamis(): void
    {
        User::factory()->create([
            'role'             => 'kasubag',
            'name'             => 'Rina Wijaya, S.Sos',
            'jabatan_nama'     => 'Kasubag Umum dan Kepegawaian',
            'pangkat_golongan' => 'Penata Tk. I / III-d',
            'nip'              => '198501012010012001',
        ]);

        $penandatangan = User::where('jabatan_nama', 'like', '%Kasubag Umum dan Kepegawaian%')->first();

        $html = View::make('pages.rekapitulasi.pdf', [
            'peminjamen'    => collect(),
            'tgl_mulai'     => null,
            'tgl_selesai'   => null,
            'jenis'         => 'semua',
            'penandatangan' => $penandatangan,
        ])->render();

        $this->assertStringContainsString('DINAS KESEHATAN', $html);
        $this->assertStringContainsString('PEMERINTAH KOTA BATAM', $html);
        $this->assertStringContainsString('Kasubag Umum', $html);
        $this->assertStringContainsString('Rina Wijaya, S.Sos', $html);
        $this->assertStringContainsString('198501012010012001', $html);
        $this->assertStringNotContainsString('Andi Saputra', $html);
    }

    public function test_pdf_ttd_tidak_memakai_nama_karangan_saat_data_kasubag_belum_ada(): void
    {
        $html = View::make('pages.rekapitulasi.pdf', [
            'peminjamen'    => collect(),
            'tgl_mulai'     => null,
            'tgl_selesai'   => null,
            'jenis'         => 'semua',
            'penandatangan' => null,
        ])->render();

        $this->assertStringNotContainsString('Andi Saputra', $html);
    }

    public function test_admin_bisa_akses_dan_simpan_pengaturan_pimpinan(): void
    {
        $admin = User::factory()->create(['role' => 'admin']);

        $response = $this->actingAs($admin)->get(route('settings.index'));
        $response->assertOk();
        $response->assertSee('Kustomisasi Penandatangan Laporan');

        $response = $this->actingAs($admin)->post(route('settings.update'), [
            'pimpinan_nama' => 'Dr. H. Sabar, M.Si',
            'pimpinan_nip' => '197001012000031001',
            'pimpinan_jabatan' => 'Kepala Dinas Kesehatan',
        ]);

        $response->assertRedirect(route('settings.index'));
        $this->assertSame('Dr. H. Sabar, M.Si', \App\Models\Setting::getByKey('pimpinan_nama'));
        $this->assertSame('197001012000031001', \App\Models\Setting::getByKey('pimpinan_nip'));
        $this->assertSame('Kepala Dinas Kesehatan', \App\Models\Setting::getByKey('pimpinan_jabatan'));
    }

    public function test_user_biasa_tidak_bisa_akses_settings(): void
    {
        $user = User::factory()->create(['role' => 'kasi']);

        $this->actingAs($user)->get(route('settings.index'))->assertForbidden();
        $this->actingAs($user)->post(route('settings.update'), [])->assertForbidden();
    }

    public function test_pdf_menggunakan_nama_pimpinan_dari_settings(): void
    {
        \App\Models\Setting::setByKey('pimpinan_nama', 'Dr. H. Sabar, M.Si');
        \App\Models\Setting::setByKey('pimpinan_nip', '197001012000031001');
        \App\Models\Setting::setByKey('pimpinan_jabatan', 'Kepala Dinas Kesehatan');

        $html = View::make('pages.rekapitulasi.pdf', [
            'peminjamen'    => collect(),
            'tgl_mulai'     => null,
            'tgl_selesai'   => null,
            'jenis'         => 'semua',
            'penandatangan' => (object) [
                'name'             => \App\Models\Setting::getByKey('pimpinan_nama'),
                'nip'              => \App\Models\Setting::getByKey('pimpinan_nip'),
                'pangkat_golongan' => null,
                'jabatan_nama'     => \App\Models\Setting::getByKey('pimpinan_jabatan'),
            ],
        ])->render();

        $this->assertStringContainsString('Kepala Dinas Kesehatan', $html);
        $this->assertStringContainsString('Dr. H. Sabar, M.Si', $html);
        $this->assertStringContainsString('197001012000031001', $html);
    }
}
