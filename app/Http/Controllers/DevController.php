<?php

namespace App\Http\Controllers;

use App\Mail\CronJobFailed;
use Illuminate\Support\Facades\Artisan;
use Illuminate\Http\Request;

class DevController extends Controller
{

    public function dev(){

		//$x = 2;
		//
		Artisan::call('datafiles:check');

		//return (new CronJobFailed())->render();

    }

}
