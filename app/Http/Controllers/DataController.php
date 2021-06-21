<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;

class DataController extends Controller
{

    public function infectionsVsVaccinations(Request $request){

        $selected_countries =  explode(',', $request->countries);

        //$selected_countries = ['PER', 'PAN', 'NIC', 'GTM', 'TTO', 'JAM', 'HND', 'DOM', 'CRI', 'BHS', 'COL', 'BOL', 'VEN', 'ECU', 'CHL', 'URY', 'ARG', 'PRY'];

        // Get active cases
        $countries_data = json_decode(file_get_contents(storage_path() . '/app/private/covid-19-data/public/data/owid-covid-data.json'), true);

        $filtered_countries = array_filter($countries_data, function($val, $key) use ($selected_countries) {
            return in_array($key, $selected_countries);
        }, ARRAY_FILTER_USE_BOTH);

        // What we are going to return
        $countries_data = [];

        foreach($filtered_countries as $key => $country){

            $last_day = end($country['data']);
            $seven_days_ago = $country['data'][sizeof($country['data']) - 7];

            $countries_data[$key]['total_cases_per_million'] = $last_day['total_cases_per_million'];
            $countries_data[$key]['total_cases_per_million_7_days_ago'] = $seven_days_ago['total_cases_per_million'];

            //if(isset($last_day['people_vaccinated'])){
            //    $countries_data[$key]['people_vaccinated'] = $last_day['people_vaccinated'];
            //}
            //
            //if(isset($last_day['people_fully_vaccinated'])){
            //    $countries_data[$key]['people_fully_vaccinated'] = $last_day['people_fully_vaccinated'];
            //}

        }


        // Vaccinations
        // Get active cases

        $vaccination_data_countries = json_decode(file_get_contents(storage_path() . '/app/private/covid-19-data/public/data/vaccinations/vaccinations.json'), true);

        $filtered_vaccination_data_countries = array_filter($vaccination_data_countries, function($val, $key) use ($selected_countries) {
            return in_array($val['iso_code'], $selected_countries);
        }, ARRAY_FILTER_USE_BOTH);

        foreach ($filtered_vaccination_data_countries as $key => $country){

            $vaccinated = isset(end($country['data'])['people_vaccinated']) ? end($country['data'])['people_vaccinated'] : 'N/A';
            $fully_vaccinated = isset(end($country['data'])['people_fully_vaccinated']) ? end($country['data'])['people_fully_vaccinated'] : 'N/A';

            $countries_data[$country['iso_code']]['people_vaccinated'] = $vaccinated;
            $countries_data[$country['iso_code']]['people_fully_vaccinated'] = $fully_vaccinated;

        }

        array_multisort(array_column($countries_data, 'people_vaccinated'), SORT_DESC, $countries_data);

        return response()->json($countries_data);

    }


}
