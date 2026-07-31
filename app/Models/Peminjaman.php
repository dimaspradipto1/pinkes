<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Peminjaman extends Model
{
    protected $table = 'peminjamen';

    protected $fillable = [
        'user_id', 'barang_id', 'ruangan_id', 'jumlah_pinjam', 'pinjam_id',
        'tgl_peminjaman', 'jam_peminjaman',
        'tgl_pengembalian', 'jam_pengembalian',
        'keterangan', 'status',
    ];

    /**
     * Status yang tersedia:
     * pending   = baru disubmit user, menunggu verifikasi admin
     * validasi  = sudah diverifikasi admin (sedang berjalan)
     * ditolak   = ditolak admin
     * selesai   = selesai, barang/ruangan sudah dikembalikan
     */
    public static $statusLabels = [
        'pending'  => ['label' => 'Menunggu Verifikasi', 'color' => 'warning'],
        'validasi' => ['label' => 'Divalidasi',          'color' => 'primary'],
        'ditolak'  => ['label' => 'Ditolak',             'color' => 'danger'],
        'selesai'  => ['label' => 'Selesai',             'color' => 'success'],
    ];

    /** Scope: hanya milik user tertentu */
    public function scopeForUser($query, int $userId)
    {
        return $query->where('user_id', $userId);
    }

    /** Scope: hanya yang aktif (pending atau validasi) */
    public function scopeAktif($query)
    {
        return $query->whereIn('status', ['pending', 'validasi']);
    }

    public function user()    { return $this->belongsTo(User::class); }
    public function barang()  { return $this->belongsTo(Barang::class); }
    public function ruangan() { return $this->belongsTo(Ruangan::class); }
    public function pinjam()  { return $this->belongsTo(Pinjam::class); }
}
