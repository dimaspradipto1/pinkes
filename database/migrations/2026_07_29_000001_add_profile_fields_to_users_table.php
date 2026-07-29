<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('users', function (Blueprint $table) {
            $table->string('nip')->nullable()->after('role');
            $table->string('jabatan_nama')->nullable()->after('nip');
            $table->string('pangkat_golongan')->nullable()->after('jabatan_nama');
        });
    }

    public function down(): void
    {
        Schema::table('users', function (Blueprint $table) {
            $table->dropColumn(['nip', 'jabatan_nama', 'pangkat_golongan']);
        });
    }
};
