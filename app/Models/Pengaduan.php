<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Pengaduan extends Model
{
    protected $table = 'pengaduan';

    protected $fillable = [
        'user_id',
        'judul',
        'deskripsi',
        'kategori',
        'provinsi',
        'kecamatan',
        'kelurahan',
        'foto',
        'status',
        'tanggapan'
    ];
}
