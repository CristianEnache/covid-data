<?php

namespace App\Console\Commands;

use Illuminate\Support\Facades\Mail;
use App\Mail\CronJobFailed;
use Illuminate\Console\Command;

class CheckDataFiles extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'datafiles:check';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Checks for the existance of data files';

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
     */
    public function handle()
    {

		$storage_path = storage_path();

		$files = [
			'/app/private/owid_covid-19-data/owid-covid-data.json',
			'/app/private/owid_covid-19-data/vaccinations.json',
			'/app/private/owid_covid-19-data/owid-covid-latest.json',
			'/app/private/other_data/OxCGRT_latest.json',
			'/app/private/other_data/mobility_data.json',
		];

		foreach ($files as $file){
			if(!file_exists($storage_path . $file)){
				$this->notifyAdmin($file);
				break;
			}
		}


    }

	public function notifyAdmin($file){
		Mail::to('cristian.enache@invgroup.co.uk')->cc('tp@raphlabs.com')->send(new CronJobFailed($file));
	}
}
