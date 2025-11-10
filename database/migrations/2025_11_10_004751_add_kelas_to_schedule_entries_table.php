<?php
// database/migrations/xxxx_xx_xx_xxxxxx_add_kelas_to_schedule_entries_table.php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('schedule_entries', function (Blueprint $table) {
            // Tambahkan dua kolom baru, boleh kosong (nullable)
            $table->string('kode_kelas')->nullable()->after('jam_slot');
            $table->string('ruang_kelas')->nullable()->after('kode_kelas');
        });
    }

    public function down(): void
    {
        Schema::table('schedule_entries', function (Blueprint $table) {
            $table->dropColumn(['kode_kelas', 'ruang_kelas']);
        });
    }
};