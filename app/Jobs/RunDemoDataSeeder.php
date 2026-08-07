<?php

namespace App\Jobs;

use Database\Seeders\DemoDataSeeder;
use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Foundation\Bus\Dispatchable;
use Illuminate\Queue\InteractsWithQueue;
use Illuminate\Queue\SerializesModels;
use Illuminate\Support\Facades\Artisan;
use Illuminate\Support\Facades\Log;

class RunDemoDataSeeder implements ShouldQueue
{
    use Dispatchable, InteractsWithQueue, Queueable, SerializesModels;

    // Historical assessment generation across every team is the slow part
    // of this seeder — give it plenty of room on the queue worker.
    public int $timeout = 600;

    public function handle(): void
    {
        Artisan::call('db:seed', [
            '--class' => DemoDataSeeder::class,
            '--force' => true,
        ]);

        Log::info('DemoDataSeeder finished running via admin-triggered job.');
    }
}
