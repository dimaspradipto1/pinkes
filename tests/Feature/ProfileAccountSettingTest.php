<?php

namespace Tests\Feature;

use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Support\Facades\Hash;
use Tests\TestCase;

/**
 * 7/8 (Admin & User). My Profile & Account Setting harus berfungsi.
 * User: My Profile bisa melengkapi nama lengkap, NIP, jabatan, pangkat dan golongan.
 */
class ProfileAccountSettingTest extends TestCase
{
    use RefreshDatabase;

    public function test_halaman_my_profile_bisa_diakses(): void
    {
        $user = User::factory()->create();

        $this->actingAs($user)->get(route('profile.show'))->assertOk();
    }

    public function test_user_bisa_melengkapi_profile_nama_nip_jabatan_pangkat_golongan(): void
    {
        $user = User::factory()->create(['role' => 'kasi']);

        $response = $this->actingAs($user)->post(route('profile.update'), [
            'name'             => 'Siti Nurhaliza',
            'nip'              => '198812252015022001',
            'jabatan_nama'     => 'Kepala Seksi Promosi Kesehatan',
            'pangkat_golongan' => 'Penata Muda / III-a',
        ]);

        $response->assertRedirect(route('profile.show'));

        $user->refresh();
        $this->assertSame('Siti Nurhaliza', $user->name);
        $this->assertSame('198812252015022001', $user->nip);
        $this->assertSame('Kepala Seksi Promosi Kesehatan', $user->jabatan_nama);
        $this->assertSame('Penata Muda / III-a', $user->pangkat_golongan);
    }

    public function test_halaman_account_setting_bisa_diakses(): void
    {
        $user = User::factory()->create();

        $this->actingAs($user)->get(route('account.setting'))->assertOk();
    }

    public function test_user_bisa_ganti_password_lewat_account_setting(): void
    {
        $user = User::factory()->create(['password' => Hash::make('password-lama')]);

        $response = $this->actingAs($user)->post(route('account.password'), [
            'current_password'            => 'password-lama',
            'password'                    => 'password-baru-123',
            'password_confirmation'       => 'password-baru-123',
        ]);

        $response->assertRedirect(route('account.setting'));
        $this->assertTrue(Hash::check('password-baru-123', $user->fresh()->password));
    }

    public function test_ganti_password_gagal_jika_password_lama_salah(): void
    {
        $user = User::factory()->create(['password' => Hash::make('password-lama')]);

        $this->actingAs($user)->post(route('account.password'), [
            'current_password'      => 'password-salah',
            'password'               => 'password-baru-123',
            'password_confirmation' => 'password-baru-123',
        ]);

        $this->assertTrue(Hash::check('password-lama', $user->fresh()->password));
    }
}
