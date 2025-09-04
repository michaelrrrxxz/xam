<?php

namespace Database\Seeders;

use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;
class SchoolSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
          DB::table('schools')->insert([
            [
                'name' => 'Springfield High',
                'created_at' => now(),
                'updated_at' => now(),
            ],
            [
                'name' => 'Riverdale Academy',
                'created_at' => now(),
                'updated_at' => now(),
            ],
            [
                'name' => 'Sunnydale Prep',
                'created_at' => now(),
                'updated_at' => now(),
            ]
        ]);
    }
}
