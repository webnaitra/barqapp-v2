<?php

namespace App\Console\Commands;

use Illuminate\Console\Command;
use App\Services\DailyDigestService;

class SendDailyDigest extends Command
{
    protected $signature = 'digest:send';
    protected $description = 'Send daily news digest to eligible users';

    public function handle(DailyDigestService $digestService)
    {
        $digestService->sendDailyDigests();
        $this->info('Daily digests sent successfully.');
    }
}
