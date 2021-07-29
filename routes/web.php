<?php

use Illuminate\Support\Facades\Route;

/*
|--------------------------------------------------------------------------
| Web Routes
|--------------------------------------------------------------------------
|
| Here is where you can register web routes for your application. These
| routes are loaded by the RouteServiceProvider within a group which
| contains the "web" middleware group. Now create something great!
|
*/

Route::get('/', function () {
    return view('welcome');
});

Route::get('/dev', 'App\Http\Controllers\DevController@dev');
Route::get('/data/one', 'App\Http\Controllers\DataController@dataOne');
Route::get('/data/one', 'App\Http\Controllers\DataController@dataOne');
