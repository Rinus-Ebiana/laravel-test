<?php
// database/migrations/xxxx_xx_xx_xxxxxx_create_mahasiswa_matakuliah_table.php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        // Tabel ini untuk menyimpan NILAI mahasiswa
        Schema::create('mahasiswa_matakuliah', function (Blueprint $table) {
            $table->id();
            
            $table->string('mahasiswa_nim');
            $table->foreign('mahasiswa_nim')->references('nim')->on('mahasiswa')->onDelete('cascade');

            $table->string('matakuliah_kode_mk');
            $table->foreign('matakuliah_kode_mk')->references('kode_mk')->on('matakuliah')->onDelete('cascade');

            // Kolom untuk nilai, misal: "A", "AB", "B", "C", "D", "E"
            $table->string('nilai', 2)->nullable(); 
            
            $table->timestamps();

            // Pastikan mahasiswa hanya punya 1 nilai per matakuliah
            $table->unique(['mahasiswa_nim', 'matakuliah_kode_mk']);
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('mahasiswa_matakuliah');
    }
};