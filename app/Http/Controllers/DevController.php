<?php

namespace App\Http\Controllers;

use App\Jobs\RecordCountriesScores;
use App\Mail\CronJobFailed;
use App\Models\CountryScore;
use Carbon\Carbon;
use Illuminate\Support\Facades\Artisan;
use Illuminate\Http\Request;

class DevController extends Controller
{

    public function dev(){


		$initial_records = CountryScore::where('created_at', '<', '2022-10-08 14:00:00')->get();

		$date = Carbon::make('2022-10-08 14:00:00');

		for ($i=0; $i < 10; $i++) {

			$day = $date->addDay()->toDayDateTimeString();

			foreach ($initial_records as $initial_record){

				$new_score = new CountryScore([
					'country_code' => $initial_record->country_code,
					'score' => rand(0, 30)
				]);

				$new_score->save();

				$new_score->created_at = $day;
				$new_score->updated_at = $day;
				$new_score->save();

			}



		}

		return response()->json($initial_records);


		//RecordCountriesScores::dispatchSync();

		//$x = 2;
		//
		//Artisan::call('datafiles:check');

		//return (new CronJobFailed())->render();

    }

}
