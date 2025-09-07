<?php

namespace Database\Seeders;

use App\Models\User;
// use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\Hash;

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
        $this->call(QuestionSeeder::class);

        //results calculation
        $this->call([
            RstossSeeder::class,
            TblPbaPrnsSeeder::class,
            TblPbgPrnsnvSeeder::class,
            TblPbgPrnstSeeder::class,
            TblPbgPrnsvSeeder::class,
            TblSstosai16nvSeeder::class,
            TblSstosai16tSeeder::class,
            TblSstosai16vSeeder::class,
            TblSstosai17nvSeeder::class,
            TblSstosai17tSeeder::class,
            TblSstosai17vSeeder::class,
            TblSstosai18nvSeeder::class,
            TblSstosai18tSeeder::class,
            TblSstosai18vSeeder::class
        ]);
        // User::factory(10)->create();

        User::factory()->create([
            'name' => 'Test User',
            'email' => 'test@123.com',
            'password' =>  Hash::make('test123')
        ]);
    }
}
