<?php

namespace App\Console\Commands;

use Illuminate\Console\Command;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Str;

class GenerateUuidsForExistingFailedJobs extends Command
{
    protected $signature = 'novapackages:generate-missing-uuids';

    protected $description = 'Generates UUIDs for existing failed jobs that do not have a UUID set.';

    public function handle()
    {
        DB::table('failed_jobs')->whereNull('uuid')->cursor()->each(function ($job) {
            DB::table('failed_jobs')
                ->where('id', $job->id)
                ->update(['uuid' => (string) Str::uuid()]);
        });

        return 0;
    }
}
