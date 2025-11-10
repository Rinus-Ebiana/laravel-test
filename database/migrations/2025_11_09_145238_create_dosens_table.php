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
        Schema::create('dosen', function (Blueprint $table) {
            // Ubah baris ini:
            // $table->id();
            // $table->string('kd')->unique();
            
            // Menjadi baris ini:
            $table->string('kd')->primary(); // KD sebagai Primary Key

            $table->string('nama');
            $table->string('nip')->unique();
            $table->string('no_telp')->nullable();
            $table->string('email')->unique();
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('dosens');
    }
};
