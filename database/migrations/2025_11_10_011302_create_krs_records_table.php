<?php
// database/migrations/xxxx_xx_xx_xxxxxx_create_krs_records_table.php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        // Tabel ini untuk menyimpan matakuliah/kelas yang DIPILIH mahasiswa
        Schema::create('krs_records', function (Blueprint $table) {
            $table->id();

            $table->string('mahasiswa_nim');
            $table->foreign('mahasiswa_nim')->references('nim')->on('mahasiswa')->onDelete('cascade');

            // Kunci asing ke kelas/jadwal yang spesifik (dari schedule_entries)
            // Nullable karena Tesis/Publikasi mungkin tidak punya entri jadwal
            $table->foreignId('schedule_entry_id')->nullable()->constrained('schedule_entries')->onDelete('set null');

            // Kunci asing untuk matakuliah (untuk Tesis, dll)
            $table->string('matakuliah_kode_mk')->nullable();
            $table->foreign('matakuliah_kode_mk')->references('kode_mk')->on('matakuliah')->onDelete('cascade');
            
            $table->timestamps();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('krs_records');
    }
};