<?php
// database/migrations/xxxx_xx_xx_xxxxxx_create_dosen_matakuliah_pivot_table.php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('dosen_matakuliah', function (Blueprint $table) {
            $table->id();
            
            // Kunci asing ke tabel 'dosen' (menggunakan 'kd')
            $table->string('dosen_kd');
            $table->foreign('dosen_kd')->references('kd')->on('dosen')->onDelete('cascade');

            // Kunci asing ke tabel 'matakuliah' (menggunakan 'kode_mk')
            $table->string('matakuliah_kode_mk');
            $table->foreign('matakuliah_kode_mk')->references('kode_mk')->on('matakuliah')->onDelete('cascade');
            
            $table->timestamps();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('dosen_matakuliah');
    }
};