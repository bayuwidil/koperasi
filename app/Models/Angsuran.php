<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Angsuran extends Model
{
    use HasFactory;
    protected $table = 'angsurans';
    protected $fillable = ['pinjaman_id', 'jumlah_bayar', 'denda', 'jatuh_tempo', 'status'];

    public function pinjaman()
    {
        return $this->belongsTo(Pinjaman::class);
    }
}
