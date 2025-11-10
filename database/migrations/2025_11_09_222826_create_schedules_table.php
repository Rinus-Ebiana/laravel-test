<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Run the migrations.
     */
    public function up(): void
    {
        Schema::create('schedules', function (Blueprint $table) {
            $table->id();
            $table->string('nama_jadwal'); // Misal: "Jadwal Ganjil 2025/2026"
            
            // Flag untuk membedakan 'Simpan Sementara' vs 'Simpan Permanen'
            $table->boolean('is_permanent')->default(false); 
            
            // Flag untuk menandai jadwal mana yang tampil di halaman 'jadwal.index'
            $table->boolean('is_active')->default(true); 
            
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('schedules');
    }
};
