<?php
namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class ScheduleEntry extends Model
{
    use HasFactory;
    
    protected $fillable = [
        'schedule_id', 
        'matakuliah_kode_mk', 
        'dosen_kd', 
        'hari', 
        'jam_slot',
        'kode_kelas',
        'ruang_kelas'
    ];

    /**
     * Satu Entri milik SATU Jadwal (Schedule).
     */
    public function schedule()
    {
        return $this->belongsTo(Schedule::class);
    }
    
    /**
     * Satu Entri milik SATU Matakuliah.
     */
    public function matakuliah()
    {
        return $this->belongsTo(Matakuliah::class, 'matakuliah_kode_mk', 'kode_mk');
    }
    
    /**
     * Satu Entri milik SATU Dosen.
     */
    public function dosen()
    {
        return $this->belongsTo(Dosen::class, 'dosen_kd', 'kd');
    }
}