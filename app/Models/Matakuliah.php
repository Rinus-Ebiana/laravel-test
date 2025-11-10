<?php

// app/Models/Matakuliah.php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Matakuliah extends Model
{
    use HasFactory;

    protected $table = 'matakuliah';
    
    // --- KONFIGURASI PRIMARY KEY ---
    protected $primaryKey = 'kode_mk';
    public $incrementing = false;
    protected $keyType = 'string';
    // ---------------------------------

    protected $fillable = [
        'kode_mk',
        'nama_mk',
        'sks',
        'semester',
        'dosen_pengampu',
    ];

    public function getRouteKeyName()
    {
        return 'kode_mk';
    }
    public function dosen()
    {
        return $this->belongsToMany(
            Dosen::class,
            'dosen_matakuliah',      // Nama tabel pivot
            'matakuliah_kode_mk', // Kunci asing untuk Matakuliah
            'dosen_kd'             // Kunci asing untuk Dosen
        )->withTimestamps();
    }
}