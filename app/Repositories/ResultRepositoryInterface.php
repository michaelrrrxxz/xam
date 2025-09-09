<?php

namespace App\Repositories;

interface ResultRepositoryInterface
{
    public function create(array $data);

    public function getScaledScoreTotal(int $rawScore);
    public function getScaledScoreVerbal(int $rawScore);
    public function getScaledScoreNonVerbal(int $rawScore);

    public function getSaiValue(string $table, string $column, int $scaledScore);
    public function getPbg(string $table, int $scaledScore);
    public function getPba(string $table, int $sai);
}
