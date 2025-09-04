<?php

namespace App\Http\Controllers\Examinee;
use App\Http\Controllers\Controller;

use Illuminate\Http\Request;
use App\Models\Result;
use App\Models\EnrolledStudent;
use App\Models\Question;
use App\Models\Batch;
use App\Models\Scaledscore;

use Carbon\carbon;
use Illuminate\Support\Facades\DB;
use App\Models\Sstosai18t;

class ResultController extends Controller
{

    public function show($studentId)
    {
        $result = Result::where('student_id', $studentId)->latest()->first();

        if (!$result) {
            return response()->json(['message' => 'No result found.'], 404);
        }

        return response()->json([
            'message' => 'Result fetched successfully',
            'result' => $result,
        ]);
    }


    public function store(Request $request)
    {
        $validated = $request->validate([
            'student_id' => 'required|exists:enrolled_students,id',
            'school' => 'nullable|string',
            'answers' => 'required|array',
            'answers.*.question_id' => 'required|exists:questions,id',
            'answers.*.answer' => 'required|string',
        ]);

        $batch = Batch::where('isLocked', false)->first();

        $studentAnswers = collect($validated['answers'])->map(function ($answer) {
            $question = Question::find($answer['question_id']);
            return (object) [
                'question'   => $question,
                'given'      => $answer['answer'],
                'is_correct' => $question && $question->answer === $answer['answer'],
            ];
        });


        // $totalScore = $studentAnswers->where('is_correct', true)->count();
        $totalScore = 36;


        // $verbalScore = $studentAnswers->filter(fn($a) =>
        //     strtolower($a->question->test_type) === 'verbal' && $a->is_correct
        // )->count();

        $verbalScore = 30;

        $nonVerbalScore = $studentAnswers->filter(fn($a) =>
            strtolower($a->question->test_type) === 'non-verbal' && $a->is_correct
        )->count();


        $verbalReasoningScore = $studentAnswers->filter(fn($a) =>
            strtolower($a->question->test_type) === 'verbal' &&
            strtolower($a->question->qtype) === 'verbal reasoning' &&
            $a->is_correct
        )->count();

        $verbalComprehensionScore = $studentAnswers->filter(fn($a) =>
            strtolower($a->question->test_type) === 'verbal' &&
            strtolower($a->question->qtype) === 'verbal comprehension' &&
            $a->is_correct
        )->count();

        $quantitativeReasoningScore = $studentAnswers->filter(fn($a) =>
            strtolower($a->question->test_type) === 'non-verbal' &&
            strtolower($a->question->qtype) === 'quantitative reasoning' &&
            $a->is_correct
        )->count();

        $figuralReasoningScore = $studentAnswers->filter(fn($a) =>
            strtolower($a->question->test_type) === 'non-verbal' &&
            strtolower($a->question->qtype) === 'figural reasoning' &&
            $a->is_correct
        )->count();

        // Total Performance Category
        $totalPerformanceCategory = $totalScore <= 35
            ? "BA"
            : ($totalScore <= 57
                ? "A"
                : "AA");

        // Verbal Performance Category
        $verbalPerformanceCategory = $verbalScore <= 16
            ? "BA"
            : ($verbalScore <= 28
                ? "A"
                : "Above Average");

        // Verbal Comprehension Category
        $verbalComprehensionCategory = $verbalComprehensionScore <= 5
            ? "BA"
            : ($verbalComprehensionScore <= 9
                ? "A"
                : "AA");

        // Verbal Reasoning Category
        $verbalReasoningCategory = $verbalReasoningScore <= 10
            ? "BA"
            : ($verbalReasoningScore <= 18
                ? "A"
                : "AA");

        // Non-Verbal Performance Category
        $nonVerbalPerformanceCategory = $nonVerbalScore <= 18
            ? "BA"
            : ($nonVerbalScore <= 30
                ? "A"
                : "AA");

        // Figural Reasoning Category
        $figuralReasoningCategory = $figuralReasoningScore <= 8
            ? "BA"
            : ($figuralReasoningScore <= 14
                ? "A"
                : "AA");

        // Quantitative Reasoning Category
        $quantitativeReasoningCategory = $quantitativeReasoningScore <= 9
            ? "BA"
            : ($quantitativeReasoningScore <= 15
                ? "A"
                : "AA");

        //Age

        $birth_date = EnrolledStudent::where('id', $validated['student_id'])
            ->value('birth_day');


        $create_at = now()->format('Y-m-d');


        if ($birth_date && $create_at) {
            $birthDateCarbon = Carbon::parse($birth_date);
            $createAtCarbon = Carbon::parse($create_at);

            $diff = $birthDateCarbon->diff($createAtCarbon);

            $age_year  = $diff->y;
            $age_month = $diff->m;
            $age_day   = $diff->d;
        } else {
            $age_year  = 'N/A';
            $age_month = 'N/A';
            $age_day   = 'N/A';
        }

        if($totalScore=='0'){
            $totalScore=1;
            }


            //done for total scaled score

        $scaledScore = Db::table('rstoss')->where('raw_score_t', $totalScore )
        ->first();







        $sstosai = '';
        if ($age_month >= 18) {
            $sstosai = 'tbl_sstosai_18t';
        } elseif ($age_month == 17) {
            $sstosai = 'tbl_sstosai_17t';
        } elseif ($age_month <= 16) {
            $sstosai = 'tbl_sstosai_16t';
        }

        $sstosai_m = '';
        $sstosai_sai = '';
        if ($age_month <= 2) {
            $sstosai_m = 'month_a';
            $sstosai_sai = 'sai_a';
        } elseif ($age_month <= 5) {
            $sstosai_m = 'month_b';
            $sstosai_sai = 'sai_b';
        } elseif ($age_month <= 8) {
            $sstosai_m = 'month_c';
            $sstosai_sai = 'sai_c';
        } else {
            $sstosai_m = 'month_d';
            $sstosai_sai = 'sai_d';
        }



        $sai = db::table($sstosai)->where($sstosai_m,$scaledScore->scaled_score_t )->first();



       if ($sai && isset($sai->$sstosai_sai)) {
            $saiValue = $sai->$sstosai_sai;
        } else {

            $saiValue = $scaledScore->raw_score_t > 700 ? '150' : '50';
        }




        $pbg = DB::table('tbl_pbg_prnst')
        ->where('scaled_score', $scaledScore->scaled_score_t)
        ->first();


        $pba = DB::table('tbl_pba_prns')
        ->where('sai', $sai->$sstosai_sai)
        ->first();


        // done

    //Verbal Scaled Score
        if($verbalScore=='0'){
            $verbalScore=1;
        }
        $verbalscaledScore = Db::table('rstoss')->where('raw_score_v',$verbalScore )->first();


        if($age_year >='18'){
        $sstosai = "tbl_sstosai_18v";
        }else if($age_year =='17'){
            $sstosai = "tbl_sstosai_17v";
            }else if($age_year <='16'){
                $sstosai = "tbl_sstosai_16v";
                }

//saiv

          $sai_v = Db::table($sstosai)->where($sstosai_m, $verbalscaledScore->scaled_score_v )->first();

            if ($sai_v && isset($sai_v->$sstosai_sai)) {
                $saiVerbalValue = $sai_v->$sstosai_sai;
            } else {
                $saiVerbalValue = $verbalscaledScore->raw_score_v > 700 ? '150' : '50';
            }



              $pbg_v = DB::table('tbl_pbg_prnsv')
            ->where('scaled_score', $verbalscaledScore->scaled_score_v)
            ->first();

                    $pba_v = DB::table('tbl_pba_prns')
                ->where('sai', $sai_v->$sstosai_sai)
                ->first();


    //NonVerbal Scaled Score
        if($nonVerbalScore=='0'){
            $nonVerbalScore=1;
        }
        $nonverbalscaledScore = Db::table('rstoss')->where('raw_score_nv',$nonVerbalScore )->first();


        if($age_year >='18'){
        $sstosai = "tbl_sstosai_18nv";
        }else if($age_year =='17'){
            $sstosai = "tbl_sstosai_17nv";
            }else if($age_year <='16'){
                $sstosai = "tbl_sstosai_16nv";
                }

//saiv

          $sai_nv = Db::table($sstosai)->where($sstosai_m, $nonverbalscaledScore->scaled_score_nv )->first();

            if ($sai_nv && isset($sai_nv->$sstosai_sai)) {
                $sainonVerbalValue = $sai_nv->$sstosai_sai;
            } else {
                $sainonVerbalValue = $nonverbalscaledScore->raw_score_nv > 700 ? '150' : '50';
            }



              $pbg_v = DB::table('tbl_pbg_prnsnv')
            ->where('scaled_score', $nonverbalscaledScore->scaled_score_nv)
            ->first();

                    $pba_v = DB::table('tbl_pba_prns')
                ->where('sai', $sai_v->$sstosai_sai)
                ->first();



        $ip = request()->ip();
        $agent = request()->header('User-Agent');


        dd($ip,$agent);


        $result = Result::create([
            'student_id' => $validated['student_id'],
            'batch_id' =>  $batch->id,
            'total_score' => $totalScore,
            'verbal' => $verbalScore,
            'non_verbal' => $nonVerbalScore,
            'verbal_reasoning' => $verbalReasoningScore,
            'verbal_comprehension' => $verbalComprehensionScore,
            'quantitative_reasoning' => $quantitativeReasoningScore,
            'figural_reasoning' => $figuralReasoningScore,

            'total_performance_category' => $totalPerformanceCategory,
            'verbal_performance_category' => $verbalPerformanceCategory,
            'verbal_comprehension_category' => $verbalComprehensionCategory,
            'verbal_reasoning_category' => $verbalReasoningCategory,
            'non_verbal_performance_category' => $nonVerbalPerformanceCategory,
            'figural_reasoning_category' => $figuralReasoningCategory,
            'quantitative_reasoning_category' => $quantitativeReasoningCategory,
        ]);

        return response()->json([
            'message' => 'Exam completed successfully',
            'result'  => $result
        ], 201);
    }
}
