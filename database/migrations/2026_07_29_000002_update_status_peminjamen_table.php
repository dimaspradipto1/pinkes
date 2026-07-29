<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;
use Illuminate\Support\Facades\DB;

return new class extends Migration
{
    public function up(): void
    {
        // Update data lama: berjalan -> validasi (sudah diverifikasi sebelumnya)
        DB::table('peminjamen')->where('status', 'berjalan')->update(['status' => 'validasi']);

        // Ubah kolom status dengan enum baru
        DB::statement("ALTER TABLE peminjamen MODIFY COLUMN status ENUM('pending','validasi','ditolak','selesai') NOT NULL DEFAULT 'pending'");
    }

    public function down(): void
    {
        DB::statement("ALTER TABLE peminjamen MODIFY COLUMN status ENUM('berjalan','selesai') NOT NULL DEFAULT 'berjalan'");
    }
};
