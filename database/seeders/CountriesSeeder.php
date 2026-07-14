<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;

class CountriesSeeder extends Seeder
{
    public function run(): void
    {
        $now = now();
        $countries = [
            ['code'=>'ID','code3'=>'IDN','name'=>'Indonesia','capital'=>'Jakarta','region'=>'Asia','subregion'=>'South-Eastern Asia','latitude'=>-0.7893,'longitude'=>113.9213,'currency_code'=>'IDR','currency_name'=>'Indonesian rupiah','flag_emoji'=>'🇮🇩'],
            ['code'=>'CN','code3'=>'CHN','name'=>'China','capital'=>'Beijing','region'=>'Asia','subregion'=>'Eastern Asia','latitude'=>35.0,'longitude'=>105.0,'currency_code'=>'CNY','currency_name'=>'Chinese yuan','flag_emoji'=>'🇨🇳'],
            ['code'=>'DE','code3'=>'DEU','name'=>'Germany','capital'=>'Berlin','region'=>'Europe','subregion'=>'Western Europe','latitude'=>51.5,'longitude'=>10.5,'currency_code'=>'EUR','currency_name'=>'Euro','flag_emoji'=>'🇩🇪'],
            ['code'=>'AU','code3'=>'AUS','name'=>'Australia','capital'=>'Canberra','region'=>'Oceania','subregion'=>'Australia and New Zealand','latitude'=>-25.0,'longitude'=>133.0,'currency_code'=>'AUD','currency_name'=>'Australian dollar','flag_emoji'=>'🇦🇺'],
            ['code'=>'US','code3'=>'USA','name'=>'United States','capital'=>'Washington D.C.','region'=>'Americas','subregion'=>'Northern America','latitude'=>38.0,'longitude'=>-97.0,'currency_code'=>'USD','currency_name'=>'US Dollar','flag_emoji'=>'🇺🇸'],
            ['code'=>'GB','code3'=>'GBR','name'=>'United Kingdom','capital'=>'London','region'=>'Europe','subregion'=>'Northern Europe','latitude'=>54.0,'longitude'=>-2.0,'currency_code'=>'GBP','currency_name'=>'British pound','flag_emoji'=>'🇬🇧'],
            ['code'=>'JP','code3'=>'JPN','name'=>'Japan','capital'=>'Tokyo','region'=>'Asia','subregion'=>'Eastern Asia','latitude'=>36.2048,'longitude'=>138.2529,'currency_code'=>'JPY','currency_name'=>'Japanese yen','flag_emoji'=>'🇯🇵'],
            ['code'=>'KR','code3'=>'KOR','name'=>'South Korea','capital'=>'Seoul','region'=>'Asia','subregion'=>'Eastern Asia','latitude'=>35.9078,'longitude'=>127.7669,'currency_code'=>'KRW','currency_name'=>'South Korean won','flag_emoji'=>'🇰🇷'],
            ['code'=>'IN','code3'=>'IND','name'=>'India','capital'=>'New Delhi','region'=>'Asia','subregion'=>'Southern Asia','latitude'=>20.5937,'longitude'=>78.9629,'currency_code'=>'INR','currency_name'=>'Indian rupee','flag_emoji'=>'🇮🇳'],
            ['code'=>'SG','code3'=>'SGP','name'=>'Singapore','capital'=>'Singapore','region'=>'Asia','subregion'=>'South-Eastern Asia','latitude'=>1.3521,'longitude'=>103.8198,'currency_code'=>'SGD','currency_name'=>'Singapore dollar','flag_emoji'=>'🇸🇬'],
            ['code'=>'MY','code3'=>'MYS','name'=>'Malaysia','capital'=>'Kuala Lumpur','region'=>'Asia','subregion'=>'South-Eastern Asia','latitude'=>4.2105,'longitude'=>108.9758,'currency_code'=>'MYR','currency_name'=>'Malaysian ringgit','flag_emoji'=>'🇲🇾'],
            ['code'=>'TH','code3'=>'THA','name'=>'Thailand','capital'=>'Bangkok','region'=>'Asia','subregion'=>'South-Eastern Asia','latitude'=>15.87,'longitude'=>100.9925,'currency_code'=>'THB','currency_name'=>'Thai baht','flag_emoji'=>'🇹🇭'],
            ['code'=>'VN','code3'=>'VNM','name'=>'Vietnam','capital'=>'Hanoi','region'=>'Asia','subregion'=>'South-Eastern Asia','latitude'=>14.0583,'longitude'=>108.2772,'currency_code'=>'VND','currency_name'=>'Vietnamese dong','flag_emoji'=>'🇻🇳'],
            ['code'=>'PH','code3'=>'PHL','name'=>'Philippines','capital'=>'Manila','region'=>'Asia','subregion'=>'South-Eastern Asia','latitude'=>12.8797,'longitude'=>121.774,'currency_code'=>'PHP','currency_name'=>'Philippine peso','flag_emoji'=>'🇵🇭'],
            ['code'=>'AE','code3'=>'ARE','name'=>'United Arab Emirates','capital'=>'Abu Dhabi','region'=>'Asia','subregion'=>'Western Asia','latitude'=>23.4241,'longitude'=>53.8478,'currency_code'=>'AED','currency_name'=>'UAE dirham','flag_emoji'=>'🇦🇪'],
            ['code'=>'SA','code3'=>'SAU','name'=>'Saudi Arabia','capital'=>'Riyadh','region'=>'Asia','subregion'=>'Western Asia','latitude'=>23.8859,'longitude'=>45.0792,'currency_code'=>'SAR','currency_name'=>'Saudi riyal','flag_emoji'=>'🇸🇦'],
            ['code'=>'BR','code3'=>'BRA','name'=>'Brazil','capital'=>'Brasilia','region'=>'Americas','subregion'=>'South America','latitude'=>-14.235,'longitude'=>-51.9253,'currency_code'=>'BRL','currency_name'=>'Brazilian real','flag_emoji'=>'🇧🇷'],
            ['code'=>'NL','code3'=>'NLD','name'=>'Netherlands','capital'=>'Amsterdam','region'=>'Europe','subregion'=>'Western Europe','latitude'=>52.1326,'longitude'=>5.2913,'currency_code'=>'EUR','currency_name'=>'Euro','flag_emoji'=>'🇳🇱'],
            ['code'=>'FR','code3'=>'FRA','name'=>'France','capital'=>'Paris','region'=>'Europe','subregion'=>'Western Europe','latitude'=>46.2276,'longitude'=>2.2137,'currency_code'=>'EUR','currency_name'=>'Euro','flag_emoji'=>'🇫🇷'],
            ['code'=>'CA','code3'=>'CAN','name'=>'Canada','capital'=>'Ottawa','region'=>'Americas','subregion'=>'Northern America','latitude'=>56.1304,'longitude'=>-106.3468,'currency_code'=>'CAD','currency_name'=>'Canadian dollar','flag_emoji'=>'🇨🇦'],
            ['code'=>'ZA','code3'=>'ZAF','name'=>'South Africa','capital'=>'Pretoria','region'=>'Africa','subregion'=>'Southern Africa','latitude'=>-30.5595,'longitude'=>22.9375,'currency_code'=>'ZAR','currency_name'=>'South African rand','flag_emoji'=>'🇿🇦'],
            ['code'=>'EG','code3'=>'EGY','name'=>'Egypt','capital'=>'Cairo','region'=>'Africa','subregion'=>'Northern Africa','latitude'=>26.8206,'longitude'=>30.8025,'currency_code'=>'EGP','currency_name'=>'Egyptian pound','flag_emoji'=>'🇪🇬'],
            ['code'=>'RU','code3'=>'RUS','name'=>'Russia','capital'=>'Moscow','region'=>'Europe','subregion'=>'Eastern Europe','latitude'=>61.524,'longitude'=>105.3188,'currency_code'=>'RUB','currency_name'=>'Russian ruble','flag_emoji'=>'🇷🇺'],
            ['code'=>'TR','code3'=>'TUR','name'=>'Turkey','capital'=>'Ankara','region'=>'Asia','subregion'=>'Western Asia','latitude'=>38.9637,'longitude'=>35.2433,'currency_code'=>'TRY','currency_name'=>'Turkish lira','flag_emoji'=>'🇹🇷'],
            ['code'=>'MX','code3'=>'MEX','name'=>'Mexico','capital'=>'Mexico City','region'=>'Americas','subregion'=>'Central America','latitude'=>23.6345,'longitude'=>-102.5528,'currency_code'=>'MXN','currency_name'=>'Mexican peso','flag_emoji'=>'🇲🇽'],
        ];
        foreach ($countries as $c) {
            DB::table('countries')->insertOrIgnore(array_merge($c,['is_active'=>true,'created_at'=>$now,'updated_at'=>$now]));
        }
    }
}
