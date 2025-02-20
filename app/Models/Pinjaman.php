<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Pinjaman extends Model
{
    use HasFactory;
    protected $table = 'pinjamans';
    protected $fillable = [
        'anggota_id',
        'jumlah',
        'bunga',
        'tempo',
        'angsuran_bulanan',
        'total_pembayaran'
    ];

    public function angsurans()
    {
        return $this->hasMany(Angsuran::class);
    }

    public function anggota()
    {
        return $this->belongsTo(Anggota::class);
    }
}
