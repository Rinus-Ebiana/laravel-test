<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class KrsRecord extends Model
{
    use HasFactory;

    protected $table = 'krs_records';

    protected $fillable = [
        'mahasiswa_nim',
        'schedule_entry_id',
        'matakuliah_kode_mk'
    ];

    /**
     * Relasi ke ScheduleEntry (Untuk Matakuliah yang ada jadwal/kelasnya).
     * Ini yang dicari oleh Controller Anda.
     */
    public function scheduleEntry()
    {
        return $this->belongsTo(ScheduleEntry::class, 'schedule_entry_id');
    }

    /**
     * Relasi ke Matakuliah (Khusus untuk MK tanpa jadwal seperti Tesis/Publikasi).
     */
    public function matakuliah()
    {
        return $this->belongsTo(Matakuliah::class, 'matakuliah_kode_mk', 'kode_mk');
    }

    /**
     * Relasi ke Mahasiswa (Opsional, tapi bagus untuk dimiliki).
     */
    public function mahasiswa()
    {
        return $this->belongsTo(Mahasiswa::class, 'mahasiswa_nim', 'nim');
    }
}