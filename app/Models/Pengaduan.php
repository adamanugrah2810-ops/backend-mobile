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
        'kota',
        'kecamatan',
        'kelurahan',
        'foto',
        'status',
        'tanggapan'
    ];
}
