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
        Schema::create('schedule_entries', function (Blueprint $table) {
            $table->id();

            // Kunci asing ke tabel Induk (Jadwal mana yang memiliki entri ini)
            $table->foreignId('schedule_id')->constrained('schedules')->onDelete('cascade');
            
            // Kunci asing ke Matakuliah
            $table->string('matakuliah_kode_mk');
            $table->foreign('matakuliah_kode_mk')->references('kode_mk')->on('matakuliah');
            
            // Kunci asing ke Dosen (dosen yang DIPILIH)
            $table->string('dosen_kd');
            $table->foreign('dosen_kd')->references('kd')->on('dosen');
            
            // Data hari dan jam
            $table->string('hari'); // Misal: "Senin", "Sabtu"
            $table->string('jam_slot'); // Misal: "18:00–20:30", "13:00–15:30"
            
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('schedule_entries');
    }
};
