<?php

namespace App\Console\Commands;

use Illuminate\Console\Command;
use Maatwebsite\Excel\Facades\Excel;
use App\Imports\SubscriptionImport;

class ImportSubscription extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'import:subscriptions';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Import subscriptions from XLSX file';

    /**
     * Execute the console command.
     *
     * @return int
     */
    public function __construct()
    {
        parent::__construct();
    }
    public function handle()
    {
        $import = new SubscriptionImport();
        Excel::import($import, 'app/subscriptionsimp.xlsx');
        
        $this->info('Subscriptions imported successfully!');
    }
}
