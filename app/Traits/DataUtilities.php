<?php

namespace App\Traits;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Storage;
use League\Csv\Reader;

trait DataUtilities{

    public $country_codes_2_3_letters = [
        'AF' => 'AFG',
        'AL' => 'ALB',
        'DZ' => 'DZA',
        'AS' => 'ASM',
        'AD' => 'AND',
        'AO' => 'AGO',
        'AI' => 'AIA',
        'AQ' => 'ATA',
        'AG' => 'ATG',
        'AR' => 'ARG',
        'AM' => 'ARM',
        'AW' => 'ABW',
        'AU' => 'AUS',
        'AT' => 'AUT',
        'AZ' => 'AZE',
        'BS' => 'BHS',
        'BH' => 'BHR',
        'BD' => 'BGD',
        'BB' => 'BRB',
        'BY' => 'BLR',
        'BE' => 'BEL',
        'BZ' => 'BLZ',
        'BJ' => 'BEN',
        'BM' => 'BMU',
        'BT' => 'BTN',
        'BO' => 'BOL',
        'BQ' => 'BES',
        'BA' => 'BIH',
        'BW' => 'BWA',
        'BV' => 'BVT',
        'BR' => 'BRA',
        'IO' => 'IOT',
        'BN' => 'BRN',
        'BG' => 'BGR',
        'BF' => 'BFA',
        'BI' => 'BDI',
        'CV' => 'CPV',
        'KH' => 'KHM',
        'CM' => 'CMR',
        'CA' => 'CAN',
        'KY' => 'CYM',
        'CF' => 'CAF',
        'TD' => 'TCD',
        'CL' => 'CHL',
        'CN' => 'CHN',
        'CX' => 'CXR',
        'CC' => 'CCK',
        'CO' => 'COL',
        'KM' => 'COM',
        'CD' => 'COD',
        'CG' => 'COG',
        'CK' => 'COK',
        'CR' => 'CRI',
        'HR' => 'HRV',
        'CU' => 'CUB',
        'CW' => 'CUW',
        'CY' => 'CYP',
        'CZ' => 'CZE',
        'CI' => 'CIV',
        'DK' => 'DNK',
        'DJ' => 'DJI',
        'DM' => 'DMA',
        'DO' => 'DOM',
        'EC' => 'ECU',
        'EG' => 'EGY',
        'SV' => 'SLV',
        'GQ' => 'GNQ',
        'ER' => 'ERI',
        'EE' => 'EST',
        'SZ' => 'SWZ',
        'ET' => 'ETH',
        'FK' => 'FLK',
        'FO' => 'FRO',
        'FJ' => 'FJI',
        'FI' => 'FIN',
        'FR' => 'FRA',
        'GF' => 'GUF',
        'PF' => 'PYF',
        'TF' => 'ATF',
        'GA' => 'GAB',
        'GM' => 'GMB',
        'GE' => 'GEO',
        'DE' => 'DEU',
        'GH' => 'GHA',
        'GI' => 'GIB',
        'GR' => 'GRC',
        'GL' => 'GRL',
        'GD' => 'GRD',
        'GP' => 'GLP',
        'GU' => 'GUM',
        'GT' => 'GTM',
        'GG' => 'GGY',
        'GN' => 'GIN',
        'GW' => 'GNB',
        'GY' => 'GUY',
        'HT' => 'HTI',
        'HM' => 'HMD',
        'VA' => 'VAT',
        'HN' => 'HND',
        'HK' => 'HKG',
        'HU' => 'HUN',
        'IS' => 'ISL',
        'IN' => 'IND',
        'ID' => 'IDN',
        'IR' => 'IRN',
        'IQ' => 'IRQ',
        'IE' => 'IRL',
        'IM' => 'IMN',
        'IL' => 'ISR',
        'IT' => 'ITA',
        'JM' => 'JAM',
        'JP' => 'JPN',
        'JE' => 'JEY',
        'JO' => 'JOR',
        'KZ' => 'KAZ',
        'KE' => 'KEN',
        'KI' => 'KIR',
        'KP' => 'PRK',
        'KR' => 'KOR',
        'KW' => 'KWT',
        'KG' => 'KGZ',
        'LA' => 'LAO',
        'LV' => 'LVA',
        'LB' => 'LBN',
        'LS' => 'LSO',
        'LR' => 'LBR',
        'LY' => 'LBY',
        'LI' => 'LIE',
        'LT' => 'LTU',
        'LU' => 'LUX',
        'MO' => 'MAC',
        'MG' => 'MDG',
        'MW' => 'MWI',
        'MY' => 'MYS',
        'MV' => 'MDV',
        'ML' => 'MLI',
        'MT' => 'MLT',
        'MH' => 'MHL',
        'MQ' => 'MTQ',
        'MR' => 'MRT',
        'MU' => 'MUS',
        'YT' => 'MYT',
        'MX' => 'MEX',
        'FM' => 'FSM',
        'MD' => 'MDA',
        'MC' => 'MCO',
        'MN' => 'MNG',
        'ME' => 'MNE',
        'MS' => 'MSR',
        'MA' => 'MAR',
        'MZ' => 'MOZ',
        'MM' => 'MMR',
        'NA' => 'NAM',
        'NR' => 'NRU',
        'NP' => 'NPL',
        'NL' => 'NLD',
        'NC' => 'NCL',
        'NZ' => 'NZL',
        'NI' => 'NIC',
        'NE' => 'NER',
        'NG' => 'NGA',
        'NU' => 'NIU',
        'NF' => 'NFK',
        'MP' => 'MNP',
        'NO' => 'NOR',
        'OM' => 'OMN',
        'PK' => 'PAK',
        'PW' => 'PLW',
        'PS' => 'PSE',
        'PA' => 'PAN',
        'PG' => 'PNG',
        'PY' => 'PRY',
        'PE' => 'PER',
        'PH' => 'PHL',
        'PN' => 'PCN',
        'PL' => 'POL',
        'PT' => 'PRT',
        'PR' => 'PRI',
        'QA' => 'QAT',
        'MK' => 'MKD',
        'RO' => 'ROU',
        'RU' => 'RUS',
        'RW' => 'RWA',
        'RE' => 'REU',
        'BL' => 'BLM',
        'SH' => 'SHN',
        'KN' => 'KNA',
        'LC' => 'LCA',
        'MF' => 'MAF',
        'PM' => 'SPM',
        'VC' => 'VCT',
        'WS' => 'WSM',
        'SM' => 'SMR',
        'ST' => 'STP',
        'SA' => 'SAU',
        'SN' => 'SEN',
        'RS' => 'SRB',
        'SC' => 'SYC',
        'SL' => 'SLE',
        'SG' => 'SGP',
        'SX' => 'SXM',
        'SK' => 'SVK',
        'SI' => 'SVN',
        'SB' => 'SLB',
        'SO' => 'SOM',
        'ZA' => 'ZAF',
        'GS' => 'SGS',
        'SS' => 'SSD',
        'ES' => 'ESP',
        'LK' => 'LKA',
        'SD' => 'SDN',
        'SR' => 'SUR',
        'SJ' => 'SJM',
        'SE' => 'SWE',
        'CH' => 'CHE',
        'SY' => 'SYR',
        'TW' => 'TWN',
        'TJ' => 'TJK',
        'TZ' => 'TZA',
        'TH' => 'THA',
        'TL' => 'TLS',
        'TG' => 'TGO',
        'TK' => 'TKL',
        'TO' => 'TON',
        'TT' => 'TTO',
        'TN' => 'TUN',
        'TR' => 'TUR',
        'TM' => 'TKM',
        'TC' => 'TCA',
        'TV' => 'TUV',
        'UG' => 'UGA',
        'UA' => 'UKR',
        'AE' => 'ARE',
        'GB' => 'GBR',
        'UM' => 'UMI',
        'US' => 'USA',
        'UY' => 'URY',
        'UZ' => 'UZB',
        'VU' => 'VUT',
        'VE' => 'VEN',
        'VN' => 'VNM',
        'VG' => 'VGB',
        'VI' => 'VIR',
        'WF' => 'WLF',
        'EH' => 'ESH',
        'YE' => 'YEM',
        'ZM' => 'ZMB',
        'ZW' => 'ZWE',
        'AX' => 'ALA'
    ];

    public function processOxCGRT_latest(){

        $reader = Reader::createFromPath(storage_path() . '/app/private/other_data/OxCGRT_latest.csv', 'r');

        $countries = [];

        // Remember last item we iterated through (because we don't have access to the item via index)
        $last_good_item = [];
        $last_item = [];

        $reader->setHeaderOffset(0);
        $header = $reader->getHeader(); //returns the CSV header record
        $records = $reader->getRecords(); //returns all the CSV records as an Iterator object

        foreach ($records as $offset => $record){

            if(sizeof($last_item) && $record['CountryCode'] !== $last_item['CountryCode']){
                $countries[$last_good_item['CountryCode']] = $last_good_item;
            }

            $last_item = $record;

            if($last_item['C1_School closing'] !== '')
                $last_good_item = $last_item;
        }

        Storage::put('private/other_data/OxCGRT_latest.json', json_encode($countries));

    }

    public function processGlobal_Mobility_Reports(){

        // Get list of files
        $files = Storage::disk('private')->files('other_data/mobility_data');

        // Filter files to get only the ones for this year
        $current_year = date('Y');
        $files_for_current_year = array_filter($files, function($item) use ($current_year){
            return strpos($item, $current_year) !== false;
        });

        $countries_data = [];

        foreach($files_for_current_year as $fKey => $file_name){

            if(strpos($file_name, 'lock'))
                continue;

            $two_letter_country_code = str_replace('other_data/mobility_data/' . $current_year . '_', '', $file_name); // remove first part of string
            $two_letter_country_code = str_replace('_Region_Mobility_Report.csv', '', $two_letter_country_code); // remove second part of string

            $three_letter_country_code = $this->country_codes_2_3_letters[$two_letter_country_code];

            $file_name_path = storage_path() . '/app/private/' . $file_name;

            // Read file
            $reader = Reader::createFromPath($file_name_path, 'r');
            $reader->setHeaderOffset(0);
            $header = $reader->getHeader(); //returns the CSV header record
            $records = $reader->getRecords(); //returns all the CSV records as an Iterator object

            foreach ($records as $offset => $record){

                if($record['sub_region_1'] == ''){
                    $countries_data[$three_letter_country_code] = $record;
                }else{
                    break;
                }

            }

        }



        Storage::put('private/other_data/mobility_data.json', json_encode($countries_data));

    }

	/**
	 * @param Request $request
	 * @return \Illuminate\Http\JsonResponse
	 */
	public function infectionsVsVaccinationsCustom(Request $request) {

		//// Country ISO Codes
		$selected_countries = explode(',', $request->countries);

		$owid_covid_latest = json_decode(file_get_contents(storage_path() . '/app/private/owid_covid-19-data/owid-covid-latest.json'), true);

		$oxford_last_day_array_from_json = json_decode(file_get_contents(storage_path() . '/app/private/other_data/OxCGRT_latest.json'), true);

		$mobility_data = json_decode(file_get_contents(storage_path() . '/app/private/other_data/mobility_data.json'), true);

		// Get active cases
		$owid_covid_data_all_countries = json_decode(file_get_contents(storage_path() . '/app/private/owid_covid-19-data/owid-covid-data.json'), true);

		// Filter out countries not in $selected_countries array
		$filtered_countries = array_filter($owid_covid_data_all_countries, function ($val, $key) use ($selected_countries) {
			return in_array($key, $selected_countries);
		}, ARRAY_FILTER_USE_BOTH);

		// Get Vaccinations from different source
		$vaccination_data_countries = json_decode(file_get_contents(storage_path() . '/app/private/owid_covid-19-data/vaccinations.json'), true);
		$filtered_vaccination_data_countries = array_filter($vaccination_data_countries, function ($val, $key) use ($selected_countries) {
			return in_array($val['iso_code'], $selected_countries);
		}, ARRAY_FILTER_USE_BOTH);

		// What we are going to return
		$countries_data = [];

		foreach ($filtered_countries as $key => $country) {

			if (array_key_exists('new_cases_smoothed_per_million', end($country['data'])) &&
				array_key_exists('new_cases_smoothed_per_million', $country['data'][sizeof($country['data']) - 7]) &&
				array_key_exists('new_cases_smoothed_per_million', $country['data'][sizeof($country['data']) - 14]) &&
				array_key_exists('new_cases_smoothed_per_million', $country['data'][sizeof($country['data']) - 21]) &&
				array_key_exists('new_cases_smoothed_per_million', $country['data'][sizeof($country['data']) - 28]) &&
				array_key_exists('new_cases_smoothed_per_million', $country['data'][sizeof($country['data']) - 35])) {
				$countries_data[$key]['new_cases_smoothed_per_million_today'] = end($country['data'])['new_cases_smoothed_per_million'];

				$countries_data[$key]['new_cases_smoothed_per_million'] = [];
				array_push($countries_data[$key]['new_cases_smoothed_per_million'], end($country['data'])['new_cases_smoothed_per_million']);
				array_push($countries_data[$key]['new_cases_smoothed_per_million'], $country['data'][sizeof($country['data']) - 7]['new_cases_smoothed_per_million']);
				array_push($countries_data[$key]['new_cases_smoothed_per_million'], $country['data'][sizeof($country['data']) - 14]['new_cases_smoothed_per_million']);
				array_push($countries_data[$key]['new_cases_smoothed_per_million'], $country['data'][sizeof($country['data']) - 21]['new_cases_smoothed_per_million']);
				array_push($countries_data[$key]['new_cases_smoothed_per_million'], $country['data'][sizeof($country['data']) - 28]['new_cases_smoothed_per_million']);
				array_push($countries_data[$key]['new_cases_smoothed_per_million'], $country['data'][sizeof($country['data']) - 35]['new_cases_smoothed_per_million']);

			} else {
				$countries_data[$key]['new_cases_smoothed_per_million_today'] = 0;
				$countries_data[$key]['new_cases_smoothed_per_million'] = [0, 0, 0, 0, 0];
			}

			// Search for total_boosters property in at least one of the items in $country['data']
			$booster_records = array_filter($country['data'], function ($country) {
				return array_key_exists('total_boosters', $country);
			});

			$countries_data[$key]['has_booster'] = !empty($booster_records);
			$countries_data[$key]['population'] = $country['population'];
			$countries_data[$key]['density_per_square_km'] = $this->getDensityPerSquareKM($key);
			$countries_data[$key]['healthcare'] = 'xxx';
			$countries_data[$key]['regs'] = 'P1 Permitted';

			$exists_vaccination_policy = array_key_exists($key, $oxford_last_day_array_from_json);

			if($exists_vaccination_policy){
				$countries_data[$key]['vaccination_policy'] = floatval($oxford_last_day_array_from_json[$key]['H7_Vaccination policy']);
				$countries_data[$key]['vaccination_policy_text'] = $this->vaccination_policies[$countries_data[$key]['vaccination_policy']];
			}else{
				$countries_data[$key]['vaccination_policy'] = '-';
				$countries_data[$key]['vaccination_policy_text'] = '-';
			}

			$countries_data[$key]['positive_rate'] = array_key_exists($key, $owid_covid_latest) ? $owid_covid_latest[$key]['positive_rate'] : 'N.A.';
			$countries_data[$key]['stringency'] = array_key_exists($key, $owid_covid_latest) ? $owid_covid_latest[$key]['stringency_index'] : 'N.A.';
			$exists_facial_covering = array_key_exists($key, $oxford_last_day_array_from_json) && array_key_exists('H6_Facial Coverings', $oxford_last_day_array_from_json[$key]);

			if($exists_facial_covering){
				$countries_data[$key]['facial_covering'] =  floatval($oxford_last_day_array_from_json[$key]['H6_Facial Coverings']);
				$countries_data[$key]['facial_covering_text'] = $this->facial_covering_policies[$countries_data[$key]['facial_covering']];
			}else{
				$countries_data[$key]['facial_covering'] = '-';
				$countries_data[$key]['facial_covering_text'] = '-';
			}

			$countries_data[$key]['grocery_and_pharmacy_percent_change_from_baseline'] = array_key_exists($key, $mobility_data) ? intval($mobility_data[$key]['grocery_and_pharmacy_percent_change_from_baseline']) : 'N.A.';
			$countries_data[$key]['grocery_and_pharmacy_percent_change_from_baseline_score'] = $this->getGroceryAndPharmacyScore($key, $mobility_data);
			$countries_data[$key]['workplaces_percent_change_from_baseline'] = array_key_exists($key, $mobility_data) ? intval($mobility_data[$key]['workplaces_percent_change_from_baseline']) : 0;
			$countries_data[$key]['workplaces_percent_change_from_baseline_score'] = $this->getWorkplaceScore($key, $mobility_data);
			$countries_data[$key]['location'] = $country['location'];

		}

		// Solve the differences between $filtered_countries AND $filtered_vaccination_data_countries
		foreach ($filtered_vaccination_data_countries as $key => $country) {
			$countries_data[$country['iso_code']]['people_vaccinated_per_hundred'] = isset(end($country['data'])['people_vaccinated_per_hundred']) ? end($country['data'])['people_vaccinated_per_hundred'] : 'N/A';
			$countries_data[$country['iso_code']]['people_fully_vaccinated_per_hundred'] = isset(end($country['data'])['people_fully_vaccinated_per_hundred']) ? end($country['data'])['people_fully_vaccinated_per_hundred'] : 'N/A';
		}

		// Calculate scores needs to be done after the $countries_data array is complete because the score is calculated in comparison with other countries in the array
		// Do the positive
		foreach ($countries_data as $key => $country) {

			$countries_data[$key]['positive_rate_score'] = is_numeric($countries_data[$key]['positive_rate']) ? $this->getScore('positive_rate', 'desc', 8, 'location', $country['location'], $countries_data) : 0;
			$countries_data[$key]['stringency_score'] = is_numeric($country['stringency']) ? $this->getScore('stringency', 'desc', 8, 'location', $country['location'], $countries_data) : 3;

			$new_cases_pm_score = 0;

			if (is_numeric($country['new_cases_smoothed_per_million_today'])) {
				if (intval($country['new_cases_smoothed_per_million_today']) == 0) {
					$new_cases_pm_score = 5;
				} else {
					$new_cases_pm_score = $this->getScore('new_cases_smoothed_per_million_today', 'desc', 8, 'location', $country['location'], $countries_data);
				}
			} else {
				$new_cases_pm_score = 3;
			}

			$countries_data[$key]['new_cases_smoothed_per_million_today_score'] = $new_cases_pm_score;

			// Final score: low = bad, high = good
			// use
			// vaccination_policy - use as is
			// positive_rate_score - use as is
			// stringency_score - use as is
			// facial_covering : make negative
			// grocery_and_pharmacy_percent_change_from_baseline
			// workplaces_percent_change_from_baseline
			//grocery_and_pharmacy_percent_change_from_baseline_score

			if (
				is_numeric($countries_data[$key]['vaccination_policy'])
				&& is_numeric($countries_data[$key]['positive_rate_score'])
				&& is_numeric($countries_data[$key]['stringency_score'])
				&& is_numeric($countries_data[$key]['facial_covering'])
				&& is_numeric($countries_data[$key]['workplaces_percent_change_from_baseline_score'])
				&& is_numeric($countries_data[$key]['grocery_and_pharmacy_percent_change_from_baseline_score'])
			) {
				$countries_data[$key]['final_score'] = $countries_data[$key]['vaccination_policy'] + $countries_data[$key]['positive_rate_score'] + $countries_data[$key]['stringency_score'] - $countries_data[$key]['facial_covering'] + $countries_data[$key]['workplaces_percent_change_from_baseline_score'] + $countries_data[$key]['grocery_and_pharmacy_percent_change_from_baseline_score'];
			} else {
				$countries_data[$key]['final_score'] = 'N.A.';
			}

		}

		// Clean elements that don't have all the data points
		foreach ($countries_data as $k => $country_data) {
			if (sizeof($country_data) !== 24)
				//$x = array_splice($countries_data,$k, 1);
				unset($countries_data[$k]);
		}

		$countries_data = $this->sortBy($request->sort_by, $request->sort_order, $countries_data);

		if ($request->sort_order == 'DESC') {
			array_multisort(array_column($countries_data, $request->sort_by), SORT_DESC, $countries_data);
		} else {
			array_multisort(array_column($countries_data, $request->sort_by), SORT_ASC, $countries_data);
		}

		return $countries_data;

	}

	/**
	 * @param $array
	 * @param $steps_back
	 * @param $step_size
	 * @return array
	 */
	private function get_week_keys(array $array, int $steps_back, int $step_size, $country_name = 'Not set', $end_date) {

		$seconds_per_step = 86400 * $step_size; // 86400 seconds in a day

		$week_keys = [];

		$end_date_offset = 0;

		$array_size = sizeof($array);

		if(!is_null($end_date)){

			// End date transformed to unix timestamp
			$end_date_to_time = strtotime($end_date);

			$last_array_date_to_time = strtotime(end($array)['date']);

			// If end date bigger than last day of the array
			if($end_date_to_time > $last_array_date_to_time){

				$pointer = $end_date_to_time;

				// While pointer is ahead of last available date in the array
				while($pointer > $last_array_date_to_time && $steps_back > 0){
					array_push($week_keys, 0);

					$pointer = $pointer - $seconds_per_step;
					$steps_back--;
				}

			}else if($end_date_to_time < $last_array_date_to_time){

				for ($i = $array_size; $i > 0 ; $i--) {

					$element = $array[$i - 1];

					// Element date transformed to unix timestamp
					$element_date_to_time = strtotime($element['date']);

					if($element_date_to_time < $end_date_to_time){
						$end_date_offset = $array_size - $i - 1;
						break;
					}

				}

			}

		}

		// Start stepping back through array
		for ($step = 0; $step <= $steps_back; $step++) {

			// Offset is zero - TO - steps_back  (eg 0 - 5)
			$offset = $step * $step_size;

			try{
				$array_element = $array[$array_size - $end_date_offset - $offset - 1];
			}catch(\ErrorException $exception){
				// Nothing
			}

			if (array_key_exists('new_cases_smoothed_per_million', $array_element)) {
				array_push($week_keys, $array_element['new_cases_smoothed_per_million']);
			} else {
				array_push($week_keys, 0);
			}


		}

		return $week_keys;

	}


	private function sortBy($the_field, $the_order, $the_array) {

		if ($the_order == 'DESC') {
			array_multisort(array_column($the_array, $the_field), SORT_DESC);
		} else {
			array_multisort(array_column($the_array, $the_field), SORT_ASC);
		}

		return $the_array;

	}


}
