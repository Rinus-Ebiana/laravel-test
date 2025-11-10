<?php
namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Schedule extends Model
{
    use HasFactory;
    
    protected $fillable = ['nama_jadwal', 'is_permanent', 'is_active'];

    /**
     * Satu Jadwal (Schedule) memiliki BANYAK Entri.
     */
    public function entries()
    {
        return $this->hasMany(ScheduleEntry::class);
    }
}