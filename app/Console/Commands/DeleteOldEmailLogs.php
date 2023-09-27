<?php

namespace App\Console\Commands;

use Illuminate\Console\Command;
use Carbon\Carbon;
use App\Models\EmailLog;

class DeleteOldEmailLogs extends Command
{
    protected $signature = 'email-logs:delete-old';
    protected $description = 'Delete email logs older than 2 months';

    public function __construct()
    {
        parent::__construct();
    }

    public function handle()
    {
        $twoMonthsAgo = Carbon::now()->subMonths(2);

        // Use Eloquent to delete email logs older than 2 months
        EmailLog::where('sent_at', '<', $twoMonthsAgo)->delete();

        $this->info('Old email logs deleted successfully.');
    }
}

