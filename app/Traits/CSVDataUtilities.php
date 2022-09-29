<?php

namespace App\Traits;

use Illuminate\Http\Request;

trait CSVDataUtilities{

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

		$title = $request->get('title', '-');
		$date_time = date('Y-m-d_H_i_s');
		$fileName = "statistics_custom_{$title}_{$date_time}.csv";

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

}
