<?php

namespace App\Services;

use App\Repositories\ResultRepositoryInterface;
use App\Models\Batch;
use App\Models\EnrolledStudent;
use App\Models\Question;
use Carbon\Carbon;

class ResultService
{
    protected $repo;

    public function __construct(ResultRepositoryInterface $repo)
    {
        $this->repo = $repo;
    }

    public function storeResult(array $validated, Batch $batch)
    {
        // STEP 1: Build student answers
        $studentAnswers = collect($validated['answers'])->map(function ($answer) {
            $question = Question::find($answer['question_id']);
            return (object) [
                'question'   => $question,
                'given'      => $answer['answer'],
                'is_correct' => $question && $question->answer === $answer['answer'],
            ];
        });

        // STEP 2: Raw scores
        $totalScore = max(1, $studentAnswers->where('is_correct', true)->count());
        $verbalScore = max(1, $studentAnswers->filter(fn($a) =>
            strtolower($a->question->test_type) === 'verbal' && $a->is_correct
        )->count());
        $nonVerbalScore = max(1, $studentAnswers->filter(fn($a) =>
            strtolower($a->question->test_type) === 'non-verbal' && $a->is_correct
        )->count());

        $verbalReasoningScore = $studentAnswers->filter(fn($a) =>
            strtolower($a->question->test_type) === 'verbal' &&
            strtolower($a->question->qtype) === 'verbal reasoning' && $a->is_correct
        )->count();

        $verbalComprehensionScore = $studentAnswers->filter(fn($a) =>
            strtolower($a->question->test_type) === 'verbal' &&
            strtolower($a->question->qtype) === 'verbal comprehension' && $a->is_correct
        )->count();

        $quantitativeReasoningScore = $studentAnswers->filter(fn($a) =>
            strtolower($a->question->test_type) === 'non-verbal' &&
            strtolower($a->question->qtype) === 'quantitative reasoning' && $a->is_correct
        )->count();

        $figuralReasoningScore = $studentAnswers->filter(fn($a) =>
            strtolower($a->question->test_type) === 'non-verbal' &&
            strtolower($a->question->qtype) === 'figural reasoning' && $a->is_correct
        )->count();

        // STEP 3: Performance categories
        $totalPerformanceCategory         = $this->getCategory($totalScore, [35, 57]);
        $verbalPerformanceCategory        = $this->getCategory($verbalScore, [16, 28]);
        $verbalComprehensionCategory      = $this->getCategory($verbalComprehensionScore, [5, 9]);
        $verbalReasoningCategory          = $this->getCategory($verbalReasoningScore, [10, 18]);
        $nonVerbalPerformanceCategory     = $this->getCategory($nonVerbalScore, [18, 30]);
        $figuralReasoningCategory         = $this->getCategory($figuralReasoningScore, [8, 14]);
        $quantitativeReasoningCategory    = $this->getCategory($quantitativeReasoningScore, [9, 15]);

        // STEP 4: Age
        $birth_date = EnrolledStudent::where('id', $validated['student_id'])->value('birth_day');
        $age = $this->calculateAge($birth_date);

        // STEP 5: Scaled score lookups
        $scaledScore        = $this->repo->getScaledScoreTotal($totalScore);
        $verbalscaledScore  = $this->repo->getScaledScoreVerbal($verbalScore);
        $nonverbalscaledScore = $this->repo->getScaledScoreNonVerbal($nonVerbalScore);

        // Sai tables by age
        $sstosai_t = $age['year'] >= 18 ? "tbl_sstosai_18t" : ($age['year'] == 17 ? "tbl_sstosai_17t" : "tbl_sstosai_16t");
        $sstosai_v = $age['year'] >= 18 ? "tbl_sstosai_18v" : ($age['year'] == 17 ? "tbl_sstosai_17v" : "tbl_sstosai_16v");
        $sstosai_nv = $age['year'] >= 18 ? "tbl_sstosai_18nv" : ($age['year'] == 17 ? "tbl_sstosai_17nv" : "tbl_sstosai_16nv");

        // Age-month mapping
        [$sstosai_m, $sstosai_sai] = $this->getSaiMonthColumn($age['month']);

        // Sai values
        $sai_t  = $this->repo->getSaiValue($sstosai_t, $sstosai_m, $scaledScore->scaled_score_t);
        $sai_v  = $this->repo->getSaiValue($sstosai_v, $sstosai_m, $verbalscaledScore->scaled_score_v);
        $sai_nv = $this->repo->getSaiValue($sstosai_nv, $sstosai_m, $nonverbalscaledScore->scaled_score_nv);

        $saiTotalValue  = $sai->$sstosai_sai ?? 50;
        $saiVerbalValue = $sai_v->$sstosai_sai ?? 50;
        $saiNonVerbalValue = $sai_nv->$sstosai_sai ?? 50;

        // PBG lookups
        $pbg_t = $this->repo->getPbg("tbl_pbg_prnst", $scaledScore->scaled_score_t) ?? (object)['percentile_ranks'=>1,'stanines'=>1];
        $pbg_v = $this->repo->getPbg("tbl_pbg_prnsv", $verbalscaledScore->scaled_score_v) ?? (object)['percentile_ranks'=>1,'stanines'=>1];
        $pbg_nv= $this->repo->getPbg("tbl_pbg_prnsnv", $nonverbalscaledScore->scaled_score_nv) ?? (object)['percentile_ranks'=>1,'stanines'=>1];

        // PBA lookups
        $pba_t = $this->repo->getPba("tbl_pba_prns", $saiTotalValue) ?? (object)['percentile_ranks'=>1,'stanines'=>1];
        $pba_v = $this->repo->getPba("tbl_pba_prns", $saiVerbalValue) ?? (object)['percentile_ranks'=>1,'stanines'=>1];
        $pba_nv= $this->repo->getPba("tbl_pba_prns", $saiNonVerbalValue) ?? (object)['percentile_ranks'=>1,'stanines'=>1];

        // STEP 6: Final insert
        $data = [
            'student_id' => $validated['student_id'],
            'batch_id'   => $batch->id,
            'test_ip'    => request()->ip(),

            'total_score'        => $totalScore,
            'scaled_score_t'     => $scaledScore->scaled_score_t,
            'sai_t'              => $saiTotalValue,
            'pba_pr_t'           => $pba_t->percentile_ranks,
            'pba_s_t'            => $pba_t->stanines,
            'pbg_pr_t'           => $pbg_t->percentile_ranks,
            'pbg_s_t'            => $pbg_t->stanines,

            'verbal'             => $verbalScore,
            'scaled_score_v'     => $verbalscaledScore->scaled_score_v,
            'sai_v'              => $saiVerbalValue,
            'pba_pr_v'           => $pba_v->percentile_ranks,
            'pba_s_v'            => $pba_v->stanines,
            'pbg_pr_v'           => $pbg_v->percentile_ranks,
            'pbg_s_v'            => $pbg_v->stanines,

            'non_verbal'         => $nonVerbalScore,
            'scaled_score_nv'    => $nonverbalscaledScore->scaled_score_nv,
            'sai_nv'             => $saiNonVerbalValue,
            'pba_pr_nv'          => $pba_nv->percentile_ranks,
            'pba_s_nv'           => $pba_nv->stanines,
            'pbg_pr_nv'          => $pbg_nv->percentile_ranks,
            'pbg_s_nv'           => $pbg_nv->stanines,

            'verbal_comprehension' => $verbalComprehensionScore,
            'verbal_comprehension_category' => $verbalComprehensionCategory,

            'verbal_reasoning'   => $verbalReasoningScore,
            'verbal_reasoning_category' => $verbalReasoningCategory,

            'figural_reasoning'  => $figuralReasoningScore,
            'figural_reasoning_category' => $figuralReasoningCategory,

            'quantitative_reasoning' => $quantitativeReasoningScore,
            'quantitative_reasoning_category' => $quantitativeReasoningCategory,

            'total_performance_category' => $totalPerformanceCategory,
            'verbal_performance_category' => $verbalPerformanceCategory,
            'non_verbal_performance_category' => $nonVerbalPerformanceCategory,
        ];

        return $this->repo->create($data);
    }

    private function getCategory(int $score, array $thresholds): string
    {
        return $score <= $thresholds[0]
            ? "BA"
            : ($score <= $thresholds[1] ? "A" : "AA");
    }

    private function calculateAge(?string $birth_date): array
    {
        if (!$birth_date) {
            return ['year' => null, 'month' => null, 'day' => null];
        }
        $birth = Carbon::parse($birth_date);
        $now   = now();
        $diff  = $birth->diff($now);
        return ['year' => $diff->y, 'month' => $diff->m, 'day' => $diff->d];
    }

    private function getSaiMonthColumn(int $ageMonth): array
    {
        if ($ageMonth <= 2) {
            return ['month_a', 'sai_a'];
        } elseif ($ageMonth <= 5) {
            return ['month_b', 'sai_b'];
        } elseif ($ageMonth <= 8) {
            return ['month_c', 'sai_c'];
        } else {
            return ['month_d', 'sai_d'];
        }
    }
}
