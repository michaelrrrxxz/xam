<?php

namespace Database\Seeders;

use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;


class BatchSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
         DB::table('batches')->insert([
        [
        'name'=> 'batch new',
        'description'=> 'new',
        'access_key'=> 11111,
        'isLocked'=> 0,
       'created_at' => "2023-09-19T10:31:55.000000Z",
                'updated_at' => now(),
        ],

        ]);
    }
}
