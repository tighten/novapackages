<?php

namespace App\Console\Commands;

use Illuminate\Console\Attributes\Description;
use Illuminate\Console\Attributes\Signature;
use Illuminate\Console\Command;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Str;

#[Signature('novapackages:generate-missing-uuids')]
#[Description('Generates UUIDs for existing failed jobs that do not have a UUID set.')]
class GenerateUuidsForExistingFailedJobs extends Command
{
    public function handle(): int
    {
        DB::table('failed_jobs')->whereNull('uuid')->cursor()->each(function ($job) {
            DB::table('failed_jobs')
                ->where('id', $job->id)
                ->update(['uuid' => (string) Str::uuid()]);
        });

        return 0;
    }
}
