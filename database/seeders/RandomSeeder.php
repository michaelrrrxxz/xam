<?php

namespace Database\Seeders;

use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;
class RandomSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        DB::table('enrolled_students')->insert([
            [
        'id_number'=> '21100871',
        'last_name'=> 'MANGAOANG',
        'first_name'=> 'MICHAEL',
        'middle_name'=> 'Tabara',
        'birth_day'=> '2008-08-01',
        'course'=> 'BSIS',
        'gender'=> 'Male',
         'created_at' => now(),
                'updated_at' => now(),

            ],
        ]);

        DB::table('batches')->insert([
        [
        'name'=> 'batch new',
        'description'=> 'new',
        'access_key'=> 11111,
        'isLocked'=> 0,
       'created_at' => now(),
                'updated_at' => now(),
        ],

        ]);


    }
}
