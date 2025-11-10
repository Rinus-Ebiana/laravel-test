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
}