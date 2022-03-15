<?php

namespace App\Http\Controllers;

use App\Traits\DataUtilities;
use Illuminate\Http\Request;
use League\Csv\Reader;

class DataController extends Controller {

	use DataUtilities;

	public $vaccination_policies = [
		'None', // 0
		'One group',
		'Two groups',
		'All vulnerable',
		'Vulnerable + others',
		'Universal' // 5
	];

	// switch so that low = bad
	public $facial_covering_policies = [
		'None',
		'Recommended',
		'Required in some public places',
		'Required in all public places',
		'Required outside-the-home at all times' // 4
	];

	public function topTenCountriesByInfectionRate() {

		$owid_covid_data_all_countries = json_decode(file_get_contents(storage_path() . '/app/private/owid_covid-19-data/owid-covid-data.json'), true);

		// What we are going to return
		$countries_data = [];

		// Remove entries that contain OWID in the key (they are continents)
		$only_countries = array_filter($owid_covid_data_all_countries, function ($val, $key) {
			return strpos($key, 'OWID') === false;
		}, ARRAY_FILTER_USE_BOTH);


		// Filter
		$large_countries = array_filter($only_countries, function ($val, $key) {
			return $val['population'] > 2000000;
		}, ARRAY_FILTER_USE_BOTH);


		foreach ($large_countries as $key => $country) {
			$countries_data[$key]['new_cases_smoothed_per_million'] = isset(end($country['data'])['new_cases_smoothed_per_million']) ? end($country['data'])['new_cases_smoothed_per_million'] : 0;
		}

		array_multisort(array_column($countries_data, 'new_cases_smoothed_per_million'), SORT_DESC, $countries_data);

		$first_10 = array_slice($countries_data, 0, 10);

		$keys = array_keys($first_10);

		return response($keys);

	}

	public function lastTenCountriesByInfectionRate() {

		$owid_covid_data_all_countries = json_decode(file_get_contents(storage_path() . '/app/private/owid_covid-19-data/owid-covid-data.json'), true);

		// What we are going to return
		$countries_data = [];

		// Remove entries that contain OWID in the key (they are continents)
		$only_countries = array_filter($owid_covid_data_all_countries, function ($val, $key) {
			return strpos($key, 'OWID') === false;
		}, ARRAY_FILTER_USE_BOTH);


		// Filter
		$large_countries = array_filter($only_countries, function ($val, $key) {
			return $val['population'] > 2000000;
		}, ARRAY_FILTER_USE_BOTH);


		foreach ($large_countries as $key => $country) {
			$countries_data[$key]['new_cases_smoothed_per_million'] = isset(end($country['data'])['new_cases_smoothed_per_million']) ? end($country['data'])['new_cases_smoothed_per_million'] : 0;
		}

		array_multisort(array_column($countries_data, 'new_cases_smoothed_per_million'), SORT_ASC, $countries_data);

		$first_10 = array_slice($countries_data, 0, 10);
		$first_10 = array_reverse($first_10);
		$keys = array_keys($first_10);

		return response($keys);

	}


	/**
	 * @return \Illuminate\Contracts\Foundation\Application|\Illuminate\Contracts\Routing\ResponseFactory|\Illuminate\Http\Response
	 */
	public function topTenCountriesByVaccinationRate() {

		$owid_covid_data_all_countries = json_decode(file_get_contents(storage_path() . '/app/private/owid_covid-19-data/owid-covid-data.json'), true);

		// Get Vaccinations
		$vaccination_data_countries = json_decode(file_get_contents(storage_path() . '/app/private/owid_covid-19-data/vaccinations.json'), true);

		// What we are going to return
		$countries_data = [];

		// Remove entries that contain OWID in the key (they are continents)
		$only_countries = array_filter($vaccination_data_countries, function ($val, $key) {
			return strpos($val['iso_code'], 'OWID') === false;
		}, ARRAY_FILTER_USE_BOTH);

		// Filter
		$large_countries = array_filter($only_countries, function ($val, $key) use ($owid_covid_data_all_countries) {
			return $owid_covid_data_all_countries[$val['iso_code']]['population'] > 2000000;
		}, ARRAY_FILTER_USE_BOTH);

		foreach ($large_countries as $key => $country) {
			$countries_data[$country['iso_code']]['people_fully_vaccinated_per_hundred'] = isset(end($country['data'])['people_fully_vaccinated_per_hundred']) ? end($country['data'])['people_fully_vaccinated_per_hundred'] : 0;
		}

		array_multisort(array_column($countries_data, 'people_fully_vaccinated_per_hundred'), SORT_DESC, $countries_data);

		$first_10 = array_slice($countries_data, 0, 10);

		$keys = array_keys($first_10);

		return response($keys);

	}


	/**
	 * @param Request $request
	 * @return \Illuminate\Http\JsonResponse
	 */
	public function infectionsVsVaccinations(Request $request) {

		// Country ISO Codes
		$selected_countries = explode(',', $request->countries);

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

		}

		// Solve the differences between $filtered_countries AND $filtered_vaccination_data_countries
		foreach ($filtered_vaccination_data_countries as $key => $country) {
			$countries_data[$country['iso_code']]['people_vaccinated_per_hundred'] = isset(end($country['data'])['people_vaccinated_per_hundred']) ? end($country['data'])['people_vaccinated_per_hundred'] : 'N/A';
			$countries_data[$country['iso_code']]['people_fully_vaccinated_per_hundred'] = isset(end($country['data'])['people_fully_vaccinated_per_hundred']) ? end($country['data'])['people_fully_vaccinated_per_hundred'] : 'N/A';
		}

		// Clean elements that don't have all the data points
		foreach ($countries_data as $k => $country_data) {
			if (sizeof($country_data) !== 7)
				//$x = array_splice($countries_data,$k, 1);
				unset($countries_data[$k]);
		}

		if ($request->sort_order == 'DESC') {
			array_multisort(array_column($countries_data, $request->sort_by), SORT_DESC, $countries_data);
		} else {
			array_multisort(array_column($countries_data, $request->sort_by), SORT_ASC, $countries_data);
		}

		return response()->json($countries_data);

	}

	/**
	 * @param Request $request
	 * @return \Illuminate\Http\JsonResponse
	 */
	public function infectionsVsVaccinationsAveraged(Request $request) {

		// Country ISO Codes
		$selected_countries = explode(',', $request->countries);

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


			$countries_data[$key]['new_cases_smoothed_per_million_today'] = array_key_exists('new_cases_smoothed_per_million', end($country['data'])) ? end($country['data'])['new_cases_smoothed_per_million'] : 0;
			$countries_data[$key]['new_cases_smoothed_per_million'] = $this->get_week_keys($country['data'], $request->get('steps_back', 5), $request->get('step_size', 7));

			// Search for total_boosters property in at least one of the items in $country['data']
			$booster_records = array_filter($country['data'], function ($country) {
				return array_key_exists('total_boosters', $country);
			});

			$countries_data[$key]['population'] = $country['population'];
			$countries_data[$key]['density_per_square_km'] = $this->getDensityPerSquareKM($key);

		}

		// Solve the differences between $filtered_countries AND $filtered_vaccination_data_countries
		foreach ($filtered_vaccination_data_countries as $key => $country) {
			$countries_data[$country['iso_code']]['people_vaccinated_per_hundred'] = isset(end($country['data'])['people_vaccinated_per_hundred']) ? end($country['data'])['people_vaccinated_per_hundred'] : 'N/A';
			$countries_data[$country['iso_code']]['people_fully_vaccinated_per_hundred'] = isset(end($country['data'])['people_fully_vaccinated_per_hundred']) ? end($country['data'])['people_fully_vaccinated_per_hundred'] : 'N/A';
		}

		$averaged = $this->averageArray($countries_data);

		return response()->json($averaged);

	}

	private function averageArray($items) {

		$averaged = [];

		// Convert associative to numeric
		$items = array_values($items);

		$keys_to_average = ['new_cases_smoothed_per_million_today', 'population', 'density_per_square_km', 'people_vaccinated_per_hundred', 'people_fully_vaccinated_per_hundred'];

		foreach ($keys_to_average as $k => $key) {
			$averaged[$key] = floor(array_sum(array_column($items, $key)) / sizeof(array_column($items, $key)));
		}

		$averaged['new_cases_smoothed_per_million'] = [];

		//new_cases_smoothed_per_million

		$steps_count = sizeof(last($items)['new_cases_smoothed_per_million']);

		$new_cases_smoothed_per_million = array_column($items, 'new_cases_smoothed_per_million');

		$items_count = sizeof($items);


		for ($stepKey = 0; $stepKey < $steps_count; $stepKey++) {

			$arr = [];

			for ($i = 0; $i < $items_count; $i++) {

				array_push($arr, $new_cases_smoothed_per_million[$i][$stepKey]);

			}

			$averaged_key = floor(array_sum($arr) / sizeof($arr));

			array_push($averaged['new_cases_smoothed_per_million'], $averaged_key);

		}

		return $averaged;

	}

	/**
	 * @param $array
	 * @param $steps_back
	 * @param $step_size
	 * @return array
	 */
	private function get_week_keys($array, $steps_back, $step_size) {

		$week_keys = [];

		for ($step = 0; $step <= $steps_back; $step++) {

			$offset = ($step == 0) ? 0 : $step * $step_size;

			$array_length = sizeof($array);

			$array_element = $array[$array_length - $offset - 1];

			if (array_key_exists('new_cases_smoothed_per_million', $array_element)) {
				array_push($week_keys, $array_element['new_cases_smoothed_per_million']);
			} else {
				array_push($week_keys, 0);
			}


		}

		return $week_keys;

	}


	/**
	 * @param Request $request
	 * @return \Illuminate\Http\JsonResponse
	 */
	public function infectionsVsVaccinationsCustom(Request $request) {

		//// Country ISO Codes
		$selected_countries = explode(',', $request->countries);

		$owid_covid_vaccinations = json_decode(file_get_contents(storage_path() . '/app/private/owid_covid-19-data/vaccinations.json'), true);

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
			$countries_data[$key]['vaccination_policy'] = floatval($oxford_last_day_array_from_json[$key]['H7_Vaccination policy']);
			$countries_data[$key]['vaccination_policy_text'] = $this->vaccination_policies[$countries_data[$key]['vaccination_policy']];
			$countries_data[$key]['positive_rate'] = array_key_exists($key, $owid_covid_latest) ? $owid_covid_latest[$key]['positive_rate'] : 'N.A.';
			$countries_data[$key]['stringency'] = array_key_exists($key, $owid_covid_latest) ? $owid_covid_latest[$key]['stringency_index'] : 'N.A.';
			$countries_data[$key]['facial_covering'] = floatval($oxford_last_day_array_from_json[$key]['H6_Facial Coverings']);
			$countries_data[$key]['facial_covering_text'] = $this->facial_covering_policies[$countries_data[$key]['facial_covering']];
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

	private function sortBy($the_field, $the_order, $the_array) {

		if ($the_order == 'DESC') {
			array_multisort(array_column($the_array, $the_field), SORT_DESC);
		} else {
			array_multisort(array_column($the_array, $the_field), SORT_ASC);
		}

		return $the_array;

	}

	/**
	 * We divide the number of results into blocks $the_block_size size. The 'score' is the count of the block for the current result
	 *
	 * @param $the_field
	 * @param $the_order
	 * @param $the_block_size
	 * @param $identifier_field
	 * @param $identifier_value
	 * @param $the_array
	 * @return false|float
	 */
	private function getScore($the_field, $the_order, $the_block_size, $identifier_field, $identifier_value, $the_array) {
		$the_array = $this->sortBy($the_field, $the_order, $the_array);
		$position = array_search($identifier_value, array_column($the_array, $identifier_field)); // 0
		$score = ($position == 0) ? 0 : $position / $the_block_size;
		$floored_score = floor($score);
		return $floored_score;
	}

	private function getGroceryAndPharmacyScore($key, $mobility_data) {

		$score = 0;

		if (!array_key_exists($key, $mobility_data))
			return $score;

		$score_int = intval($mobility_data[$key]['grocery_and_pharmacy_percent_change_from_baseline']);

		if ($score_int >= -100 && $score <= -60) {
			$score = 1;
		} elseif ($score_int > -60 && $score_int <= -20) {
			$score = 2;
		} elseif ($score_int > -20 && $score_int <= 20) {
			$score = 3;
		} elseif ($score_int > 20 && $score_int <= 60) {
			$score = 4;
		} elseif ($score_int > 60 && $score_int <= 100) {
			$score = 5;
		}

		return $score;

	}

	private function getWorkplaceScore($key, $mobility_data) {

		$score = 0;

		if (!array_key_exists($key, $mobility_data))
			return $score;

		$score_int = intval($mobility_data[$key]['workplaces_percent_change_from_baseline']);

		if ($score_int >= -100 && $score <= -50) {
			$score = 1;
		} elseif ($score_int > -50 && $score_int <= -0) {
			$score = 2;
		} elseif ($score_int > -0 && $score_int <= 50) {
			$score = 3;
		} elseif ($score_int > 50 && $score_int <= 100) {
			$score = 4;
		}

		return $score;

	}


	public function infectionsVsVaccinationsCustomToCSV(Request $request) {

		$data_items = $this->infectionsVsVaccinationsCustom($request);

		// Iterate countries
		foreach ($data_items as $diKey => $dataItem) {

			// Last 5 weeks of data
			$weeks = $data_items[$diKey]['new_cases_smoothed_per_million'];
			foreach ($weeks as $k => $week) {

				if ($k == sizeof($weeks) - 1) {
					unset($weeks[$k]);
					break;
				}

				$new_val = $weeks[$k];
				$old_val = $weeks[$k + 1];

				if ($old_val == 0) {
					$weeks[$k] = 'n.a.';
				} else if ($new_val > $old_val) {
					$weeks[$k] = "+" . round(($new_val - $old_val) / $old_val * 100, 2) . '%';
				} else {
					$weeks[$k] = "" . round(($new_val - $old_val) / $old_val * 100, 2) . '%';
				}

			}

			$reversed = array_reverse($weeks);
			$data_items[$diKey]['new_cases_smoothed_per_million_text'] = implode(' | ', $reversed);
			unset($data_items[$diKey]['new_cases_smoothed_per_million']);

		}

		$date_time = date('Y-m-d_H_i_s');
		$fileName = 'statistics_custom_' . $date_time . '.csv';

		$headers = array(
			"Content-type" => "text/csv",
			"Content-Disposition" => "attachment; filename=$fileName",
			"Pragma" => "no-cache",
			"Cache-Control" => "must-revalidate, post-check=0, pre-check=0",
			"Expires" => "0"
		);

		$columns = array(
			'Country',
			'Pop size',
			'Healthcare',
			'Fully vaccinated',
			'Partially vaccinated',
			'Booster',
			'Regs',
			'Infection trend last 5 weeks',
			'Infections PM',
			'Infections PM Score',
			'Vaccination policy',
			'Vaccination policy score',
			'Positive rate',
			'Positive rate score',
			'Stringency index',
			'Stringency index score',
			'Facial covering',
			'Facial covering score',
			'Grocery and pharmacy percent change from baseline',
			'Grocery and pharmacy percent change from baseline score',
			'Workplace percent change from baseline',
			'Workplace percent change from baseline score',
			'Score (High is good)');

		$callback = function () use ($data_items, $columns) {

			$file = fopen('php://output', 'w');
			fputcsv($file, $columns);

			foreach ($data_items as $data_item) {

				$row['Country'] = $data_item['location'];
				$row['Pop size'] = round($data_item['population'] / 1000000, 1) . ' Mil';
				$row['Healthcare'] = $data_item['healthcare'];
				$row['Fully vaccinated'] = $data_item['people_fully_vaccinated_per_hundred'] . '%';
				$row['Partially vaccinated'] = $data_item['people_vaccinated_per_hundred'] . '%';
				$row['Booster'] = $data_item['has_booster'] ? "Yes" : "No";
				$row['Regs'] = $data_item['regs'];
				$row['Infection trend last 5 weeks'] = $data_item['new_cases_smoothed_per_million_text'];
				$row['Infections PM'] = $data_item['new_cases_smoothed_per_million_today'];
				$row['Infections PM Score'] = $data_item['new_cases_smoothed_per_million_today_score'];
				$row['Vaccination policy'] = $data_item['vaccination_policy_text'];
				$row['Vaccination policy score'] = $data_item['vaccination_policy'];
				$row['Positive rate'] = $data_item['positive_rate'];
				$row['Positive rate score'] = $data_item['positive_rate_score'];
				$row['Stringency index'] = $data_item['stringency'];
				$row['Stringency index score'] = $data_item['stringency_score'];
				$row['Facial covering'] = $data_item['facial_covering_text'];
				$row['Facial covering score'] = $data_item['facial_covering'];
				$row['Grocery and pharmacy percent change from baseline'] = $data_item['grocery_and_pharmacy_percent_change_from_baseline'];
				$row['Grocery and pharmacy percent change from baseline score'] = $data_item['grocery_and_pharmacy_percent_change_from_baseline_score'];
				$row['Workplace percent change from baseline'] = $data_item['workplaces_percent_change_from_baseline'];
				$row['Workplace percent change from baseline score'] = $data_item['workplaces_percent_change_from_baseline_score'];
				$row['Score (High is good)'] = $data_item['final_score'];

				fputcsv($file, array(
					$row['Country'],
					$row['Pop size'],
					$row['Healthcare'],
					$row['Fully vaccinated'],
					$row['Partially vaccinated'],
					$row['Booster'],
					$row['Regs'],
					$row['Infection trend last 5 weeks'],
					$row['Infections PM'],
					$row['Infections PM Score'],
					$row['Vaccination policy'],
					$row['Vaccination policy score'],
					$row['Positive rate'],
					$row['Positive rate score'],
					$row['Stringency index'],
					$row['Stringency index score'],
					$row['Facial covering'],
					$row['Facial covering score'],
					$row['Grocery and pharmacy percent change from baseline'],
					$row['Grocery and pharmacy percent change from baseline score'],
					$row['Workplace percent change from baseline'],
					$row['Workplace percent change from baseline score'],
					$row['Score (High is good)']
				));

			}

			fclose($file);

		};

		return response()->stream($callback, 200, $headers);

	}


	/**
	 * @param $country_code
	 * @return mixed|string
	 */
	private function getDensityPerSquareKM($country_code) {

		$densities_by_country_code = [
			'ABW' => 588.03,
			'AFG' => 56.94,
			'AGO' => 24.71,
			'ALB' => 104.61,
			'AND' => 163.84,
			'ARB' => 32.09,
			'ARE' => 135.61,
			'ARG' => 16.26,
			'ARM' => 103.68,
			'ASM' => 277.33,
			'ATG' => 218.83,
			'AUS' => 3.25,
			'AUT' => 107.13,
			'AZE' => 120.26,
			'BDI' => 435.18,
			'BEL' => 377.38,
			'BEN' => 101.85,
			'BFA' => 72.19,
			'BGD' => 1, 239.58,
			'BGR' => 64.71,
			'BHR' => 2, 012.10,
			'BHS' => 38.53,
			'BIH' => 64.92,
			'BLR' => 46.72,
			'BLZ' => 16.79,
			'BMU' => 1, 183.70,
			'BOL' => 10.48,
			'BRA' => 25.06,
			'BRB' => 666.61,
			'BRN' => 81.40,
			'BTN' => 19.78,
			'BWA' => 3.98,
			'CAF' => 7.49,
			'CAN' => 4.13,
			'CEB' => 92.69,
			'CHE' => 215.47,
			'CHI' => 861.11,
			'CHL' => 25.19,
			'CHN' => 147.77,
			'CIV' => 78.83,
			'CMR' => 53.34,
			'COD' => 37.08,
			'COG' => 15.36,
			'COL' => 44.76,
			'COM' => 447.24,
			'CPV' => 134.93,
			'CRI' => 97.91,
			'CSS' => 18.18,
			'CUB' => 109.23,
			'CUW' => 358.86,
			'CYM' => 267.39,
			'CYP' => 128.71,
			'CZE' => 137.69,
			'DEU' => 237.29,
			'DJI' => 41.37,
			'DMA' => 95.50,
			'DNK' => 144.84,
			'DOM' => 219.98,
			'DZA' => 17.73,
			'EAP' => 129.95,
			'EAR' => 97.49,
			'EAS' => 95.02,
			'ECA' => 17.75,
			'ECS' => 33.44,
			'ECU' => 68.79,
			'EGY' => 98.87,
			'EMU' => 127.44,
			'ERI' => 34.19,
			'ESP' => 93.67,
			'EST' => 30.41,
			'ETH' => 96.72,
			'EUU' => 111.70,
			'FCS' => 40.98,
			'FIN' => 18.15,
			'FJI' => 48.36,
			'FRA' => 122.30,
			'FRO' => 34.74,
			'FSM' => 160.91,
			'GAB' => 8.22,
			'GBR' => 274.71,
			'GEO' => 65.20,
			'GHA' => 130.82,
			'GIB' => 3, 371.80,
			'GIN' => 50.52,
			'GMB' => 225.31,
			'GNB' => 66.65,
			'GNQ' => 46.67,
			'GRC' => 83.27,
			'GRD' => 327.81,
			'GRL' => 0.14,
			'GTM' => 152.55,
			'GUM' => 306.98,
			'GUY' => 3.96,
			'HIC' => 34.91,
			'HKG' => 7, 096.19,
			'HND' => 85.69,
			'HPC' => 40.31,
			'HRV' => 72.24,
			'HTI' => 403.60,
			'HUN' => 107.12,
			'IBD' => 67.22,
			'IBT' => 66.74,
			'IDA' => 65.37,
			'IDB' => 125.24,
			'IDN' => 142.56,
			'IDX' => 52.63,
			'IMN' => 147.50,
			'IND' => 454.94,
			'INX' => 'n.a.',
			'IRL' => 70.65,
			'IRN' => 50.22,
			'IRQ' => 88.53,
			'ISL' => 3.50,
			'ISR' => 410.48,
			'ITA' => 202.94,
			'JAM' => 270.99,
			'JOR' => 112.14,
			'JPN' => 347.13,
			'KAZ' => 6.77,
			'KEN' => 90.30,
			'KGZ' => 32.97,
			'KHM' => 92.06,
			'KIR' => 143.02,
			'KNA' => 201.68,
			'KOR' => 529.19,
			'KWT' => 232.17,
			'LAC' => 31.98,
			'LAO' => 30.60,
			'LBN' => 669.49,
			'LBR' => 50.03,
			'LBY' => 3.80,
			'LCA' => 298.18,
			'LCN' => 31.96,
			'LDC' => 49.56,
			'LIC' => 43.08,
			'LIE' => 236.94,
			'LKA' => 350.28,
			'LMC' => 130.95,
			'LMY' => 67.18,
			'LSO' => 69.44,
			'LTE' => 51.34,
			'LTU' => 44.73,
			'LUX' => 250.19,
			'LVA' => 31.04,
			'MAC' => 19, 198.66,
			'MAF' => 745.28,
			'MAR' => 80.73,
			'MCO' => 19, 083.37,
			'MDA' => 94.26,
			'MDG' => 45.14,
			'MDV' => 1, 718.99,
			'MEA' => 40.00,
			'MEX' => 64.91,
			'MHL' => 324.52,
			'MIC' => 71.76,
			'MKD' => 82.59,
			'MLI' => 15.64,
			'MLT' => 1, 514.47,
			'MMR' => 82.28,
			'MNA' => 44.31,
			'MNE' => 46.26,
			'MNG' => 2.04,
			'MNP' => 123.66,
			'MOZ' => 37.51,
			'MRT' => 4.27,
			'MUS' => 623.30,
			'MWI' => 192.44,
			'MYS' => 95.96,
			'NAC' => 20.09,
			'NAM' => 2.97,
			'NCL' => 15.54,
			'NER' => 17.72,
			'NGA' => 215.06,
			'NIC' => 53.73,
			'NLD' => 511.78,
			'NOR' => 14.55,
			'NPL' => 195.94,
			'NRU' => 635.20,
			'NZL' => 18.61,
			'OED' => 38.15,
			'OMN' => 15.60,
			'OSS' => 15.14,
			'PAK' => 275.29,
			'PAN' => 56.31,
			'PER' => 24.99,
			'PHL' => 357.69,
			'PLW' => 38.93,
			'PNG' => 19.00,
			'POL' => 124.03,
			'PRE' => 45.36,
			'PRI' => 360.02,
			'PRK' => 212.19,
			'PRT' => 112.26,
			'PRY' => 17.51,
			'PSE' => 758.98,
			'PSS' => 38.28,
			'PST' => 35.39,
			'PYF' => 78.89,
			'QAT' => 242.10,
			'ROU' => 84.63,
			'RUS' => 8.82,
			'RWA' => 498.66,
			'SAS' => 380.31,
			'SAU' => 15.68,
			'SDN' => 22.60,
			'SEN' => 82.35,
			'SGP' => 7, 953.00,
			'SLB' => 23.32,
			'SLE' => 105.99,
			'SLV' => 309.88,
			'SMR' => 563.08,
			'SOM' => 23.92,
			'SRB' => 79.84,
			'SSA' => 45.15,
			'SSD' => 17.37,
			'SSF' => 45.21,
			'SST' => 16.22,
			'STP' => 219.82,
			'SUR' => 3.69,
			'SVK' => 113.29,
			'SVN' => 102.99,
			'SWE' => 24.98,
			'SWZ' => 66.06,
			'SXM' => 1, 195.71,
			'SYC' => 210.35,
			'SYR' => 92.07,
			'TCA' => 39.65,
			'TCD' => 12.29,
			'TEA' => 129.32,
			'TEC' => 19.96,
			'TGO' => 145.05,
			'THA' => 135.90,
			'TJK' => 65.57,
			'TKM' => 12.45,
			'TLA' => 31.37,
			'TLS' => 85.27,
			'TMN' => 43.81,
			'TON' => 143.33,
			'TSA' => 380.31,
			'TSS' => 45.21,
			'TTO' => 270.93,
			'TUN' => 74.44,
			'TUR' => 106.96,
			'TUV' => 383.60,
			'TZA' => 63.58,
			'UGA' => 213.06,
			'UKR' => 77.02,
			'UMC' => 49.23,
			'URY' => 19.71,
			'USA' => 35.71,
			'UZB' => 74.81,
			'VCT' => 282.59,
			'VEN' => 32.73,
			'VGB' => 198.68,
			'VIR' => 305.72,
			'VNM' => 308.13,
			'VUT' => 24.01,
			'WLD' => 58.42,
			'WSM' => 69.30,
			'XKX' => 'n.a.',
			'YEM' => 53.98,
			'ZAF' => 47.64,
			'ZMB' => 23.34,
			'ZWE' => 37.32
		];

		return isset($densities_by_country_code[$country_code]) ? $densities_by_country_code[$country_code] : 'n.a.';


	}


}
