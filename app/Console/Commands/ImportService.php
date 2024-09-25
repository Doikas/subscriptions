<?php

namespace App\Console\Commands;

use Illuminate\Console\Command;
use Maatwebsite\Excel\Facades\Excel;
use App\Imports\ServiceImport;

class ImportService extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'import:services';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Import services from XLSX file';

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
        $import = new ServiceImport();
        Excel::import($import, 'app/servicesimp.xlsx');
        
        $this->info('Services imported successfully!');
    }
}
