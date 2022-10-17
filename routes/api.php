<?php

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Route;

/*
|--------------------------------------------------------------------------
| API Routes
|--------------------------------------------------------------------------
|infections-vs-vaccinations-custom
| Here is where you can register API routes for your application. These
| routes are loaded by the RouteServiceProvider within a group which
| is assigned the "api" middleware group. Enjoy building your API!
|
*/

Route::middleware('auth:api')->get('/user', function (Request $request) {
    return $request->user();
});



Route::get('/data/infections-vs-vaccinations-averaged', 'App\Http\Controllers\DataController@infectionsVsVaccinationsAveraged');
Route::get('/data/infections-vs-vaccinations-custom-csv', 'App\Http\Controllers\DataController@infectionsVsVaccinationsCustomToCSV');
Route::get('/data/infections-vs-vaccinations-score-only', 'App\Http\Controllers\DataController@infectionsVsVaccinationsScoreOnly');
Route::get('/data/country-scores', 'App\Http\Controllers\DataController@CountryScores');
Route::get('/data/infections-vs-vaccinations-custom', 'App\Http\Controllers\DataController@infectionsVsVaccinationsCustom');
Route::get('/data/infections-vs-vaccinations', 'App\Http\Controllers\DataController@infectionsVsVaccinations');
Route::get('/data/top-ten-countries-by-infection', 'App\Http\Controllers\DataController@topTenCountriesByInfectionRate');
Route::get('/data/last-ten-countries-by-infection', 'App\Http\Controllers\DataController@lastTenCountriesByInfectionRate');
Route::get('/data/top-ten-countries-by-vaccination', 'App\Http\Controllers\DataController@topTenCountriesByVaccinationRate');
