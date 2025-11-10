<?php

namespace App\Models;

use Illuminate\Foundation\Auth\User as Authenticatable;
use Illuminate\Notifications\Notifiable;

class User extends Authenticatable
{
    use Notifiable;

    /**
     * Kolom yang bisa diisi massal.
     */
    protected $fillable = [
        'username', // Pastikan 'username' ada di sini
        'password',
    ];

    /**
     * Kolom yang disembunyikan.
     */
    protected $hidden = [
        'password',
    ];

    /**
     * Beritahu Laravel untuk menggunakan 'username' saat login.
     * Hapus fungsi ini jika tidak ada, atau pastikan mengembalikan 'username'.
     */
    public function username()
    {
        return 'username';
    }
}