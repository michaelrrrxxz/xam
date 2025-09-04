<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;

class FromSqlFileSeeder extends Seeder
{
    public function run(): void
    {
        $sqlFile = public_path('compu.sql');

        if (file_exists($sqlFile)) {
            $sql = file_get_contents($sqlFile);
            DB::unprepared($sql);
            $this->command->info('SQL file executed successfully!');
        } else {
            $this->command->error('SQL file not found!');
        }
    }
}
