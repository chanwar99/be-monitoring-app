<?php

namespace Database\Seeders;

use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;
use App\Models\Program;

class ProgramsTableSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        $programs = ['PKH', 'BLT', 'Bansos Sembako', 'Bantuan Operasional'];

        foreach ($programs as $program) {
            Program::create(['name' => $program]);
        }
    }
}
