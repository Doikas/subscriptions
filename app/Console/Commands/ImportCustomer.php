<?php

namespace App\Console\Commands;

use Illuminate\Console\Command;
use Maatwebsite\Excel\Facades\Excel;
use App\Imports\CustomerImport;

class ImportCustomer extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'import:customers';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Import customers from XLSX file';

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
        $import = new CustomerImport();
        Excel::import($import, 'app/customerimp.xlsx');
        
        $this->info('Customers imported successfully!');
    }
}
