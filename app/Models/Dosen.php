<?php

// app/Models/Dosen.php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Dosen extends Model
{
    use HasFactory;

    protected $table = 'dosen';

    // --- TAMBAHAN PENTING ---

    /**
     * Tentukan Primary Key.
     */
    protected $primaryKey = 'kd';

    /**
     * Tunjukkan bahwa Primary Key BUKAN auto-incrementing.
     */
    public $incrementing = false;

    /**
     * Tunjukkan bahwa Primary Key adalah string.
     */
    protected $keyType = 'string';

    // ------------------------


    protected $fillable = [
        'kd', // Pastikan 'kd' ada di sini
        'nama',
        'nip',
        'no_telp',
        'email',
    ];

    public function getRouteKeyName()
    {
        return 'kd';
    }

    public function matakuliah()
    {
        return $this->belongsToMany(
            Matakuliah::class,
            'dosen_matakuliah',      // Nama tabel pivot
            'dosen_kd',             // Kunci asing untuk Dosen
            'matakuliah_kode_mk' // Kunci asing untuk Matakuliah
        )->withTimestamps();
    }
}