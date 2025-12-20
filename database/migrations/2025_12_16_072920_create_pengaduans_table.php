<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('pengaduan', function (Blueprint $table) {
            $table->id();

            // Relasi ke users
            $table->foreignId('user_id')
                  ->constrained('users')
                  ->onDelete('cascade');

            // Data pengaduan
            $table->string('judul');
            $table->text('deskripsi');
            $table->string('kategori');

            // Lokasi wilayah
            $table->string('wilayah');
            $table->string('kecamatan');
            $table->string('desa');

            // Koordinat lokasi
            $table->string('latitude');
            $table->string('longitude');

            // Foto pengaduan (opsional)
            $table->string('foto')->nullable();

            // Status pengaduan
            $table->enum('status', [
                'diajukan',
                'diproses',
                'selesai',
                'ditolak'
            ])->default('diajukan');

            $table->timestamps();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('pengaduan');
    }
};
