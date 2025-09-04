<?php

namespace App\Console\Commands;

use Illuminate\Console\Command;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Artisan;

class RunPublicSql extends Command
{
    protected $signature = 'setup:db {filename?}';
    protected $description = 'Run migrations, seeders, and execute SQL file from public folder';

    public function handle()
    {
        $filename = $this->argument('filename') ?: $this->ask('Please enter the SQL filename (e.g., data_dump.sql)');
        $filePath = public_path($filename);

        // 1. Run migrations
        $this->info('Running migrations...');
        Artisan::call('migrate');
        $this->info('✅ Migrations completed successfully!');

        // 2. Execute SQL file (if it exists)
        if (file_exists($filePath)) {
            $this->info("Executing SQL file: $filename");
            $sql = file_get_contents($filePath);

            try {
                DB::unprepared($sql);
                $this->info('✅ SQL file executed successfully!');
            } catch (\Exception $e) {
                $this->error('❌ Error executing SQL file: ' . $e->getMessage());
                return Command::FAILURE;
            }
        } else {
            $this->warn("⚠️  SQL file not found: $filePath");
        }

        // 3. Run seeders
        $this->info('Running seeders...');
        Artisan::call('db:seed');
        $this->info('✅ Seeders completed successfully!');

        $this->info('🎉 Database setup complete!');
        return Command::SUCCESS;
    }
}
