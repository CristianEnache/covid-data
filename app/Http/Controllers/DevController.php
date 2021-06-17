<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;

class DevController extends Controller
{

    public function dev(){

        $contents = file_get_contents(storage_path() . '/app/private/covid-19-data/public/data/owid-covid-data.json');
        $contents_arr = json_decode($contents, true);
        $x = 2;


        //return response()->json($contents);

    }

}
