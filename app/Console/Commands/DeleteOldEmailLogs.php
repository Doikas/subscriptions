<?php

namespace App\Console\Commands;

use Illuminate\Console\Command;
use Carbon\Carbon;
use App\Models\EmailLog;

class DeleteOldEmailLogs extends Command
{
    protected $signature = 'email-logs:delete-old';
    protected $description = 'Delete email logs older than 2 years';

    public function __construct()
    {
        parent::__construct();
    }

    public function handle()
    {
        $twoYearsAgo = Carbon::now()->subYears(2);

        // Use Eloquent to delete email logs older than 2 months
        EmailLog::where('sent_at', '<', $twoYearsAgo)->delete();

        $this->info('Old email logs deleted successfully.');
    }
}

