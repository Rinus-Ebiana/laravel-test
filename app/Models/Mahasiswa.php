<?php

// app/Models/Mahasiswa.php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Casts\Attribute; // Impor ini

class Mahasiswa extends Model
{
    use HasFactory;

    protected $table = 'mahasiswa';
    protected $primaryKey = 'nim';
    public $incrementing = false;
    protected $keyType = 'string';

    protected $fillable = [
        'nim',
        'nama',
        'tahun_masuk_awal', // Diubah
        'semester_masuk_awal', // Diubah
        'no_telp',
        'email',
    ];

    /**
     * Tambahkan 'semester' dan 'tahun_masuk_string' (yang dihitung) ke array/JSON output.
     */
    protected $appends = ['semester', 'tahun_masuk_string'];

    /**
     * Accessor Ajaib untuk menghitung semester saat ini secara otomatis.
     */
    protected function semester(): Attribute
    {
        return new Attribute(
            get: function (): int {

                // 1. Ambil data awal mahasiswa
                $startYear = $this->tahun_masuk_awal;
                $startSemester = $this->semester_masuk_awal; // 1=Ganjil, 2=Genap

                // Jika data awal tidak ada, kembalikan 1
                if (!$startYear || !$startSemester) {
                    return 1;
                }

                // 2. Ambil data akademik saat ini
                $currentYear = (int) date('Y');
                $currentMonth = (int) date('m');

                // Semester Ganjil (1) = Agustus (8) s/d Januari (1)
                // Semester Genap (2) = Februari (2) s/d Juli (7)
                $currentAcademicSemester = ($currentMonth >= 8 || $currentMonth <= 1) ? 1 : 2;

                // Tahun akademik dimulai di Agustus.
                // Jika bulan ini Januari, kita masih di tahun akademik sebelumnya.
                $currentAcademicYear = ($currentMonth >= 8) ? $currentYear : $currentYear - 1;

                // 3. Hitung selisihnya
                $yearDiff = $currentAcademicYear - $startYear;
                $semesterDiff = $currentAcademicSemester - $startSemester;

                $calculatedSemester = ($yearDiff * 2) + $semesterDiff + 1;

                // 4. Terapkan batas maksimal 8
                return min($calculatedSemester, 8);
            }
        );
    }

    /**
     * Accessor Ajaib untuk MEREKONSTRUKSI string tahun masuk
     * (Kebalikan dari logika import)
     */
    protected function tahunMasukString(): Attribute
    {
        return new Attribute(
            get: function (): string {
                $tahun_awal = $this->tahun_masuk_awal;
                $semester_awal = $this->semester_masuk_awal;

                if (!$tahun_awal || !$semester_awal) {
                    return ""; // Kembalikan string kosong jika data tidak ada
                }

                $tahun_akhir = $tahun_awal + 1;
                $semester_string = ($semester_awal == 1) ? "GANJIL" : "GENAP";

                return "T.A. $semester_string $tahun_awal/$tahun_akhir";
            }
        );
    }

    public function nilaiMatakuliah()
    {
        return $this->belongsToMany(Matakuliah::class, 'mahasiswa_matakuliah', 'mahasiswa_nim', 'matakuliah_kode_mk')
                    ->withPivot('nilai') // Ambil juga kolom 'nilai'
                    ->withTimestamps();
    }

    public function krsRecords()
    {
        return $this->hasMany(KrsRecord::class, 'mahasiswa_nim', 'nim');
    }

    public function getRouteKeyName()
    {
        return 'nim';
    }
}