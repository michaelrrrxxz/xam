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
        'birth_day'=> 'c',
        'course'=> 'BSIS',
        'gender'=> 'Female',
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
         DB::table('questions')->insert([
            [
                'format' => 'text',
                'test_type' => 'verbal',
                'question' => 'What is the synonym of "rapid"?',
                'answer' => 'ch2',
                'ch1' => 'Slow',
                'ch2' => 'Fast',
                'ch3' => 'Weak',
                'ch4' => 'Calm',
                'ch5' => 'Dull',
                'qtype' => 'verbal reasoning',
                'created_at' => now(),
                'updated_at' => now(),
            ],
            [
                'format' => 'text',
                'test_type' => 'non-verbal',
                'question' => 'Identify the next shape in the sequence.',
                'answer' => 'ch3',
                'ch1' => 'Circle',
                'ch2' => 'Square',
                'ch3' => 'Triangle',
                'ch4' => 'Hexagon',
                'ch5' => 'Pentagon',
                'qtype' => 'figural reasoning',
                'created_at' => now(),
                'updated_at' => now(),
            ],
            [
                'format' => 'text',
                'test_type' => 'verbal',
                'question' => 'Which number is odd in the sequence=> 2, 4, 6, 9, 10?',
                'answer' => 'ch4',
                'ch1' => '2',
                'ch2' => '4',
                'ch3' => '6',
                'ch4' => '9',
                'ch5' => '10',
                'qtype' => 'quantitative reasoning',
                'created_at' => now(),
                'updated_at' => now(),
            ],
        ]);


    }
}
