<?php

// database/seeders/UserSeeder.php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use App\Models\User; // Pastikan Model User di-impor
use Illuminate\Support\Facades\Hash; // Impor Hash

class UserSeeder extends Seeder
{
    public function run(): void
    {

        User::create([
            'username' => 'adminPasca',
            'password' => Hash::make('!Pasca25') // Password di-hash
        ]);
    }
}