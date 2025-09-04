<?php

namespace Database\Seeders;

use App\Models\User;
// use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;

class DatabaseSeeder extends Seeder
{

    /**
     * Seed the application's database.
     */
    public function run(): void
    {

        $this->call(TrialExamSeeder::class);
        $this->call(SchoolSeeder::class);
        $this->call(RandomSeeder::class);
        $this->call(ScaledscoreSeeder::class);
        $this->call(Sstosai18tSeeder::class);

        // User::factory(10)->create();

        User::factory()->create([
            'name' => 'Test User',
            'email' => 'test@example.com',
        ]);
    }
}
