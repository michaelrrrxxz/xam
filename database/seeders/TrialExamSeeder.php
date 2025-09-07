<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;

class TrialExamSeeder extends Seeder
{
    public function run(): void
    {
        DB::table('trials')->insert([
        [
                'id' => 1,
                'format' => '',
                'test_type' => '',
                'question' => '<img src=/storage/trial_qst/Sample_A/Question_A.png>',
                'answer' => '<img src=/storage/trial_qst/Sample_A/Answer_B.png>',
                'ch1' => '<img src=/storage/trial_qst/Sample_A/Answer_A.png>',
                'ch2' => '<img src=/storage/trial_qst/Sample_A/Answer_B.png>',
                'ch3' => '<img src=/storage/trial_qst/Sample_A/Answer_C.png>',
                'ch4' => '<img src=/storage/trial_qst/Sample_A/Answer_D.png>',
                'ch5' => '<img src=/storage/trial_qst/Sample_A/Answer_E.png>',
                'qtype' => '',
            ],
            [
                'id' => 2,
                'format' => '',
                'test_type' => '',
                'question' => '<img src=/storage/trial_qst/Sample_B/Question_B.png>',
                'answer' => '<img src=/storage/trial_qst/Sample_B/Answer_K.png>',
                'ch1' => '<img src=/storage/trial_qst/Sample_B/Answer_F.png>',
                'ch2' => '<img src=/storage/trial_qst/Sample_B/Answer_G.png>',
                'ch3' => '<img src=/storage/trial_qst/Sample_B/Answer_H.png>',
                'ch4' => '<img src=/storage/trial_qst/Sample_B/Answer_J.png>',
                'ch5' => '<img src=/storage/trial_qst/Sample_B/Answer_K.png>',
                'qtype' => '',
            ],
            [
                'id' => 3,
                'format' => '',
                'test_type' => '',
                'question' => '<img src=/storage/trial_qst/Sample_C/Question_C.png>',
                'answer' => '<img src=/storage/trial_qst/Sample_C/Answer_A.png>',
                'ch1' => '<img src=/storage/trial_qst/Sample_C/Answer_A.png>',
                'ch2' => '<img src=/storage/trial_qst/Sample_C/Answer_B.png>',
                'ch3' => '<img src=/storage/trial_qst/Sample_C/Answer_C.png>',
                'ch4' => '<img src=/storage/trial_qst/Sample_C/Answer_D.png>',
                'ch5' => '<img src=/storage/trial_qst/Sample_C/Answer_E.png>',
                'qtype' => '',
            ],
            [
                'id' => 4,
                'format' => '',
                'test_type' => '',
                'question' => '<img src=/storage/trial_qst/Sample_D/Question_D.png>',
                'answer' => '<img src=/storage/trial_qst/Sample_D/Answer_H.png>',
                'ch1' => '<img src=/storage/trial_qst/Sample_D/Answer_F.png>',
                'ch2' => '<img src=/storage/trial_qst/Sample_D/Answer_G.png>',
                'ch3' => '<img src=/storage/trial_qst/Sample_D/Answer_H.png>',
                'ch4' => '<img src=/storage/trial_qst/Sample_D/Answer_J.png>',
                'ch5' => '<img src=/storage/trial_qst/Sample_D/Answer_K.png>',
                'qtype' => '',
            ],
            [
                'id' => 5,
                'format' => '',
                'test_type' => '',
                'question' => '<img src=/storage/trial_qst/Sample_E/Question_E.png>',
                'answer' => '<img src=/storage/trial_qst/Sample_E/Answer_D.png>',
                'ch1' => '<img src=/storage/trial_qst/Sample_E/Answer_A.png>',
                'ch2' => '<img src=/storage/trial_qst/Sample_E/Answer_B.png>',
                'ch3' => '<img src=/storage/trial_qst/Sample_E/Answer_C.png>',
                'ch4' => '<img src=/storage/trial_qst/Sample_E/Answer_D.png>',
                'ch5' => '<img src=/storage/trial_qst/Sample_E/Answer_E.png>',
                'qtype' => '',
            ],
        ]);
    }
}
