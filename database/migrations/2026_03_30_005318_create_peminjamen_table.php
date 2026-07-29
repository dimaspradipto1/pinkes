<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Run the migrations.
     */
    public function up(): void
    {
        Schema::create('peminjamen', function (Blueprint $table) {
            $table->id();
            $table->foreignId('user_id')->constrained()->cascadeOnDelete();
            $table->foreignId('barang_id')->nullable()->constrained()->cascadeOnDelete();
            $table->foreignId('ruangan_id')->nullable()->constrained()->cascadeOnDelete();
            $table->foreignId('pinjam_id')->constrained()->cascadeOnDelete();
            $table->date('tgl_peminjaman');
            $table->time('jam_peminjaman');
            $table->date('tgl_pengembalian');
            $table->time('jam_pengembalian');
            $table->string('foto_peminjaman');
            $table->text('keterangan')->nullable();
            $table->enum('status', ['berjalan', 'selesai'])->default('berjalan');
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('peminjamen');
    }
};
