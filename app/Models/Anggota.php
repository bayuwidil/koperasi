<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Anggota extends Model
{
    use HasFactory;

    protected $table = 'anggotas';
    protected $fillable = ['user_id', 'nama', 'NIK', 'email', 'alamat', 'no_telepon'];
    protected $guarded = [];

    public function pinjaman()
    {
        return $this->hasMany(Pinjaman::class);
    }

    public function user()
    {
        return $this->belongsTo(User::class, 'user_id');
    }
}
