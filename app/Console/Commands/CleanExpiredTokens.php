<?php

namespace App\Console\Commands;

use Illuminate\Console\Command;
use Laravel\Sanctum\PersonalAccessToken;
use Carbon\Carbon;

class CleanExpiredTokens extends Command
{
    protected $signature = 'tokens:cleanup';
    protected $description = 'Delete expired Sanctum tokens';

    public function handle()
    {
        $deleted = PersonalAccessToken::where('expires_at', '<', Carbon::now())->delete();
        $this->info("Deleted {$deleted} expired tokens.");
    }
}
