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
    'wilayah',
    'kecamatan',
    'desa',
    'latitude',
    'longitude',
    'foto',
    'status',
    'tanggapan'
];

}
