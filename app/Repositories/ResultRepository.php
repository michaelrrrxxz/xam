<?php

namespace App\Repositories;

use App\Models\Result;
use Illuminate\Support\Facades\DB;

class ResultRepository implements ResultRepositoryInterface
{
    public function create(array $data)
    {
        return Result::create($data);
    }

    public function getScaledScoreTotal(int $rawScore)
    {
        return DB::table('rstoss')->where('raw_score_t', $rawScore)->first();
    }

    public function getScaledScoreVerbal(int $rawScore)
    {
        return DB::table('rstoss')->where('raw_score_v', $rawScore)->first();
    }

    public function getScaledScoreNonVerbal(int $rawScore)
    {
        return DB::table('rstoss')->where('raw_score_nv', $rawScore)->first();
    }

    public function getSaiValue(string $table, string $column, int $scaledScore)
    {
        return DB::table($table)->where($column, $scaledScore)->first();
    }

    public function getPbg(string $table, int $scaledScore)
    {
        return DB::table($table)->where('scaled_score', $scaledScore)->first();
    }

    public function getPba(string $table, int $sai)
    {
        return DB::table($table)->where('sai', $sai)->first();
    }
}
