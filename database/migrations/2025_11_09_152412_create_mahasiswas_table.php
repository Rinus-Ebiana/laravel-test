<?php

// database/migrations/xxxx_xx_xx_xxxxxx_create_mahasiswa_table.php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('mahasiswa', function (Blueprint $table) {
            $table->string('nim')->primary(); // NIM sebagai Primary Key
            $table->string('nama');
            
            // KOLOM BARU UNTUK LOGIKA SEMESTER:
            $table->integer('tahun_masuk_awal')->nullable(); // Misal: 2022
            $table->integer('semester_masuk_awal')->nullable(); // Misal: 1 (Ganjil) atau 2 (Genap)

            $table->string('no_telp')->nullable();
            $table->string('email')->unique(); 
            $table->timestamps();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('mahasiswa');
    }
};