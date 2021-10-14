<?php

namespace App\Console\Commands;

use App\Traits\DataUtilities;
use Illuminate\Console\Command;

class ProcessCsvs extends Command
{

    use DataUtilities;
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'csvs:process';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Command description';

    /**
     * Create a new command instance.
     *
     * @return void
     */
    public function __construct()
    {
        parent::__construct();
    }

    /**
     * Execute the console command.
     *
     * @return int
     */
    public function handle()
    {
        $y = $this->processOxCGRT_latest();
        $x = $this->processGlobal_Mobility_Reports();
    }
}
