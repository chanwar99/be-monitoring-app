<?php

namespace Database\Seeders;

use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\Hash;
use App\Models\User;

class UsersTableSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        // Admin
        User::create([
            'name' => 'Admin Pusat',
            'email' => 'admin@pusat.com',
            'password' => Hash::make('password'),
            'role' => 'admin',
        ]);

        // Pengguna Daerah
        User::create([
            'name' => 'Pengguna Daerah',
            'email' => 'user@daerah.com',
            'password' => Hash::make('password'),
            'role' => 'user',
        ]);
    }
}
