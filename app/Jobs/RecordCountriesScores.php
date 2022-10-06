<?php

namespace App\Jobs;

use App\Models\CountryScore;
use GuzzleHttp\Client;
use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldBeUnique;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Foundation\Bus\Dispatchable;
use Illuminate\Queue\InteractsWithQueue;
use Illuminate\Queue\SerializesModels;

class RecordCountriesScores implements ShouldQueue
{
    use Dispatchable, InteractsWithQueue, Queueable, SerializesModels;

    /**
     * Create a new job instance.
     *
     * @return void
     */
    public function __construct()
    {
        //
    }

    /**
     * Execute the job.
     *
     * @return void
     */
    public function handle()
    {

		$client = new Client();

		$response = $client->request('GET', 'http://raphstats.xyz/api/data/infections-vs-vaccinations-custom', [
			'query' => [
				'sort_by' => "new_cases_smoothed_per_million_today",
				'sort_order' => "DESC",
				'countries'=> "DZA,AGO,BWA,BDI,CMR,CPV,CAF,TCD,COM,MYT,COG,COD,BEN,GNQ,ETH,ERI,DJI,GAB,GMB,GHA,GIN,CIV,KEN,LSO,LBR,LBY,MDG,MWI,MLI,MRT,MUS,MAR,MOZ,NAM,NER,NGA,GNB,REU,RWA,SHN,STP,SEN,SYC,SLE,SOM,ZAF,ZWE,SSD,ESH,SDN,SWZ,TGO,TUN,UGA,EGY,TZA,BFA,ZMB,AFG,AZE,BHR,BGD,ARM,BTN,IOT,BRN,MMR,KHM,LKA,CHN,TWN,CXR,CCK,CYP,GEO,PSE,HKG,IND,IDN,BRN,IRN,IRQ,ISR,JPN,KAZ,JOR,PRK,KOR,KWT,KGZ,LAO,LBN,MAC,MYS,MDV,MNG,OMN,NPL,PAK,PHL,TLS,QAT,RUS,SAU,SGP,VNM,SYR,TJK,THA,ARE,TUR,TKM,UZB,YEM,ALB,AND,AZE,AUT,ARM,BEL,BIH,BGR,BLR,HRV,CYP,CZE,DNK,EST,FRO,FIN,ALA,FRA,GEO,DEU,GIB,GRC,VAT,HUN,ISL,IRL,ITA,KAZ,LVA,LIE,LTU,LUX,MLT,MCO,MDA,MNE,NLD,NOR,POL,PRT,ROU,RUS,SMR,SRB,SVK,SVN,ESP,SJM,SWE,CHE,TUR,UKR,MKD,GBR,GGY,JEY,IMN,ATG,BHS,BRB,BMU,BLZ,VGB,CAN,CYM,CRI,CUB,DMA,DOM,SLV,GRL,GRD,GLP,GTM,HTI,HND,JAM,MTQ,MEX,MSR,ANT,CUW,ABW,SXM,BES,NIC,UMI,PAN,PRI,BLM,KNA,AIA,LCA,MAF,SPM,VCT,TTO,TCA,USA,VIR,ASM,AUS,SLB,COK,FJI,PYF,KIR,GUM,NRU,NCL,VUT,NZL,NIU,NFK,MNP,UMI,FSM,MHL,PLW,PNG,PCN,TKL,TON,TUV,WLF,WSM,ARG,BOL,BRA,CHL,COL,ECU,FLK,GUF,GUY,PRY,PER,SUR,URY,VEN"
			],
		]);

		$countries = json_decode($response->getBody()->getContents());

		foreach($countries as $countryKey => $country){

			$final_score = $country->final_score == 'N.A.' ? null : $country->final_score;

			$country_score = new CountryScore([
				'country_code' => $countryKey,
				'score' => $final_score
			]);
			$country_score->save();
		}

    }
}
