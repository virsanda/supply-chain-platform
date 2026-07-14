<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;

class PortsSeeder extends Seeder
{
    public function run(): void
    {
        $now = now();
        $ports = [
            ['port_name'=>'Tanjung Priok','port_code'=>'IDJKT','country_code'=>'ID','country_name'=>'Indonesia','province_region'=>'Jakarta','latitude'=>-6.100,'longitude'=>106.883,'harbor_size'=>'large','harbor_type'=>'coastal','congestion_score'=>45],
            ['port_name'=>'Tanjung Perak','port_code'=>'IDSUB','country_code'=>'ID','country_name'=>'Indonesia','province_region'=>'East Java','latitude'=>-7.200,'longitude'=>112.733,'harbor_size'=>'large','harbor_type'=>'coastal','congestion_score'=>38],
            ['port_name'=>'Belawan','port_code'=>'IDBWN','country_code'=>'ID','country_name'=>'Indonesia','province_region'=>'North Sumatra','latitude'=>3.783,'longitude'=>98.683,'harbor_size'=>'medium','harbor_type'=>'coastal','congestion_score'=>22],
            ['port_name'=>'Makassar','port_code'=>'IDMKS','country_code'=>'ID','country_name'=>'Indonesia','province_region'=>'South Sulawesi','latitude'=>-5.133,'longitude'=>119.400,'harbor_size'=>'medium','harbor_type'=>'coastal','congestion_score'=>18],
            ['port_name'=>'Semarang','port_code'=>'IDSMG','country_code'=>'ID','country_name'=>'Indonesia','province_region'=>'Central Java','latitude'=>-6.967,'longitude'=>110.417,'harbor_size'=>'medium','harbor_type'=>'coastal','congestion_score'=>25],
            ['port_name'=>'Balikpapan','port_code'=>'IDBPN','country_code'=>'ID','country_name'=>'Indonesia','province_region'=>'East Kalimantan','latitude'=>-1.267,'longitude'=>116.833,'harbor_size'=>'medium','harbor_type'=>'coastal','congestion_score'=>15],
            ['port_name'=>'Shanghai','port_code'=>'CNSHA','country_code'=>'CN','country_name'=>'China','province_region'=>'Shanghai','latitude'=>31.233,'longitude'=>121.483,'harbor_size'=>'large','harbor_type'=>'coastal','congestion_score'=>62],
            ['port_name'=>'Shenzhen','port_code'=>'CNSZX','country_code'=>'CN','country_name'=>'China','province_region'=>'Guangdong','latitude'=>22.533,'longitude'=>114.050,'harbor_size'=>'large','harbor_type'=>'coastal','congestion_score'=>55],
            ['port_name'=>'Ningbo-Zhoushan','port_code'=>'CNNGB','country_code'=>'CN','country_name'=>'China','province_region'=>'Zhejiang','latitude'=>29.867,'longitude'=>121.550,'harbor_size'=>'large','harbor_type'=>'coastal','congestion_score'=>50],
            ['port_name'=>'Tianjin','port_code'=>'CNTSN','country_code'=>'CN','country_name'=>'China','province_region'=>'Tianjin','latitude'=>38.983,'longitude'=>117.717,'harbor_size'=>'large','harbor_type'=>'coastal','congestion_score'=>48],
            ['port_name'=>'Guangzhou Nansha','port_code'=>'CNGZH','country_code'=>'CN','country_name'=>'China','province_region'=>'Guangdong','latitude'=>22.700,'longitude'=>113.567,'harbor_size'=>'large','harbor_type'=>'coastal','congestion_score'=>52],
            ['port_name'=>'Qingdao','port_code'=>'CNTAO','country_code'=>'CN','country_name'=>'China','province_region'=>'Shandong','latitude'=>36.067,'longitude'=>120.317,'harbor_size'=>'large','harbor_type'=>'coastal','congestion_score'=>44],
            ['port_name'=>'Port of Singapore','port_code'=>'SGSIN','country_code'=>'SG','country_name'=>'Singapore','province_region'=>'Singapore','latitude'=>1.267,'longitude'=>103.850,'harbor_size'=>'large','harbor_type'=>'coastal','congestion_score'=>35],
            ['port_name'=>'Tokyo','port_code'=>'JPTYO','country_code'=>'JP','country_name'=>'Japan','province_region'=>'Tokyo','latitude'=>35.650,'longitude'=>139.767,'harbor_size'=>'large','harbor_type'=>'coastal','congestion_score'=>28],
            ['port_name'=>'Yokohama','port_code'=>'JPYOK','country_code'=>'JP','country_name'=>'Japan','province_region'=>'Kanagawa','latitude'=>35.450,'longitude'=>139.650,'harbor_size'=>'large','harbor_type'=>'coastal','congestion_score'=>25],
            ['port_name'=>'Kobe','port_code'=>'JPUKB','country_code'=>'JP','country_name'=>'Japan','province_region'=>'Hyogo','latitude'=>34.683,'longitude'=>135.183,'harbor_size'=>'large','harbor_type'=>'coastal','congestion_score'=>22],
            ['port_name'=>'Nagoya','port_code'=>'JPNGO','country_code'=>'JP','country_name'=>'Japan','province_region'=>'Aichi','latitude'=>35.067,'longitude'=>136.883,'harbor_size'=>'large','harbor_type'=>'coastal','congestion_score'=>20],
            ['port_name'=>'Busan','port_code'=>'KRPUS','country_code'=>'KR','country_name'=>'South Korea','province_region'=>'Busan','latitude'=>35.100,'longitude'=>129.033,'harbor_size'=>'large','harbor_type'=>'coastal','congestion_score'=>32],
            ['port_name'=>'Incheon','port_code'=>'KRICN','country_code'=>'KR','country_name'=>'South Korea','province_region'=>'Incheon','latitude'=>37.467,'longitude'=>126.617,'harbor_size'=>'large','harbor_type'=>'coastal','congestion_score'=>28],
            ['port_name'=>'Hamburg','port_code'=>'DEHAM','country_code'=>'DE','country_name'=>'Germany','province_region'=>'Hamburg','latitude'=>53.533,'longitude'=>10.000,'harbor_size'=>'large','harbor_type'=>'river','congestion_score'=>30],
            ['port_name'=>'Bremerhaven','port_code'=>'DEBRV','country_code'=>'DE','country_name'=>'Germany','province_region'=>'Bremen','latitude'=>53.550,'longitude'=>8.583,'harbor_size'=>'large','harbor_type'=>'coastal','congestion_score'=>22],
            ['port_name'=>'Rotterdam','port_code'=>'NLRTM','country_code'=>'NL','country_name'=>'Netherlands','province_region'=>'South Holland','latitude'=>51.917,'longitude'=>4.483,'harbor_size'=>'large','harbor_type'=>'coastal','congestion_score'=>28],
            ['port_name'=>'Antwerp','port_code'=>'BEANR','country_code'=>'BE','country_name'=>'Belgium','province_region'=>'Antwerp','latitude'=>51.250,'longitude'=>4.400,'harbor_size'=>'large','harbor_type'=>'river','congestion_score'=>35],
            ['port_name'=>'Felixstowe','port_code'=>'GBFXT','country_code'=>'GB','country_name'=>'United Kingdom','province_region'=>'Suffolk','latitude'=>51.967,'longitude'=>1.333,'harbor_size'=>'large','harbor_type'=>'coastal','congestion_score'=>42],
            ['port_name'=>'Southampton','port_code'=>'GBSOU','country_code'=>'GB','country_name'=>'United Kingdom','province_region'=>'Hampshire','latitude'=>50.900,'longitude'=>-1.400,'harbor_size'=>'large','harbor_type'=>'coastal','congestion_score'=>30],
            ['port_name'=>'Los Angeles','port_code'=>'USLAX','country_code'=>'US','country_name'=>'United States','province_region'=>'California','latitude'=>33.717,'longitude'=>-118.267,'harbor_size'=>'large','harbor_type'=>'coastal','congestion_score'=>58],
            ['port_name'=>'Long Beach','port_code'=>'USLGB','country_code'=>'US','country_name'=>'United States','province_region'=>'California','latitude'=>33.750,'longitude'=>-118.217,'harbor_size'=>'large','harbor_type'=>'coastal','congestion_score'=>55],
            ['port_name'=>'New York/New Jersey','port_code'=>'USNYC','country_code'=>'US','country_name'=>'United States','province_region'=>'New York','latitude'=>40.683,'longitude'=>-74.017,'harbor_size'=>'large','harbor_type'=>'coastal','congestion_score'=>45],
            ['port_name'=>'Houston','port_code'=>'USHOU','country_code'=>'US','country_name'=>'United States','province_region'=>'Texas','latitude'=>29.750,'longitude'=>-95.383,'harbor_size'=>'large','harbor_type'=>'coastal','congestion_score'=>35],
            ['port_name'=>'Savannah','port_code'=>'USSAV','country_code'=>'US','country_name'=>'United States','province_region'=>'Georgia','latitude'=>32.083,'longitude'=>-81.083,'harbor_size'=>'large','harbor_type'=>'river','congestion_score'=>40],
            ['port_name'=>'Sydney','port_code'=>'AUSYD','country_code'=>'AU','country_name'=>'Australia','province_region'=>'New South Wales','latitude'=>-33.867,'longitude'=>151.217,'harbor_size'=>'large','harbor_type'=>'coastal','congestion_score'=>25],
            ['port_name'=>'Melbourne','port_code'=>'AUMEL','country_code'=>'AU','country_name'=>'Australia','province_region'=>'Victoria','latitude'=>-37.817,'longitude'=>144.950,'harbor_size'=>'large','harbor_type'=>'coastal','congestion_score'=>28],
            ['port_name'=>'Brisbane','port_code'=>'AUBNE','country_code'=>'AU','country_name'=>'Australia','province_region'=>'Queensland','latitude'=>-27.467,'longitude'=>153.033,'harbor_size'=>'large','harbor_type'=>'river','congestion_score'=>20],
            ['port_name'=>'Fremantle','port_code'=>'AUFRE','country_code'=>'AU','country_name'=>'Australia','province_region'=>'Western Australia','latitude'=>-32.050,'longitude'=>115.750,'harbor_size'=>'large','harbor_type'=>'coastal','congestion_score'=>18],
            ['port_name'=>'Mumbai JNPT','port_code'=>'INNSA','country_code'=>'IN','country_name'=>'India','province_region'=>'Maharashtra','latitude'=>18.950,'longitude'=>72.933,'harbor_size'=>'large','harbor_type'=>'coastal','congestion_score'=>55],
            ['port_name'=>'Chennai','port_code'=>'INMAA','country_code'=>'IN','country_name'=>'India','province_region'=>'Tamil Nadu','latitude'=>13.100,'longitude'=>80.300,'harbor_size'=>'large','harbor_type'=>'coastal','congestion_score'=>42],
            ['port_name'=>'Kolkata','port_code'=>'INCCU','country_code'=>'IN','country_name'=>'India','province_region'=>'West Bengal','latitude'=>22.567,'longitude'=>88.350,'harbor_size'=>'large','harbor_type'=>'river','congestion_score'=>38],
            ['port_name'=>'Jebel Ali Dubai','port_code'=>'AEJEA','country_code'=>'AE','country_name'=>'UAE','province_region'=>'Dubai','latitude'=>24.983,'longitude'=>55.067,'harbor_size'=>'large','harbor_type'=>'coastal','congestion_score'=>38],
            ['port_name'=>'Port Klang','port_code'=>'MYPKG','country_code'=>'MY','country_name'=>'Malaysia','province_region'=>'Selangor','latitude'=>3.000,'longitude'=>101.383,'harbor_size'=>'large','harbor_type'=>'coastal','congestion_score'=>40],
            ['port_name'=>'Tanjung Pelepas','port_code'=>'MYPTP','country_code'=>'MY','country_name'=>'Malaysia','province_region'=>'Johor','latitude'=>1.367,'longitude'=>103.550,'harbor_size'=>'large','harbor_type'=>'coastal','congestion_score'=>35],
            ['port_name'=>'Jeddah','port_code'=>'SAJED','country_code'=>'SA','country_name'=>'Saudi Arabia','province_region'=>'Makkah','latitude'=>21.467,'longitude'=>39.233,'harbor_size'=>'large','harbor_type'=>'coastal','congestion_score'=>32],
            ['port_name'=>'Santos','port_code'=>'BRSSZ','country_code'=>'BR','country_name'=>'Brazil','province_region'=>'Sao Paulo','latitude'=>-23.933,'longitude'=>-46.317,'harbor_size'=>'large','harbor_type'=>'coastal','congestion_score'=>48],
            ['port_name'=>'Durban','port_code'=>'ZADDR','country_code'=>'ZA','country_name'=>'South Africa','province_region'=>'KwaZulu-Natal','latitude'=>-29.867,'longitude'=>31.033,'harbor_size'=>'large','harbor_type'=>'coastal','congestion_score'=>35],
            ['port_name'=>'Vancouver','port_code'=>'CAVAN','country_code'=>'CA','country_name'=>'Canada','province_region'=>'British Columbia','latitude'=>49.283,'longitude'=>-123.100,'harbor_size'=>'large','harbor_type'=>'coastal','congestion_score'=>30],
            ['port_name'=>'Port Said','port_code'=>'EGPSD','country_code'=>'EG','country_name'=>'Egypt','province_region'=>'Port Said','latitude'=>31.267,'longitude'=>32.300,'harbor_size'=>'large','harbor_type'=>'coastal','congestion_score'=>42],
            ['port_name'=>'Alexandria','port_code'=>'EGALY','country_code'=>'EG','country_name'=>'Egypt','province_region'=>'Alexandria','latitude'=>31.200,'longitude'=>29.917,'harbor_size'=>'large','harbor_type'=>'coastal','congestion_score'=>38],
            ['port_name'=>'Ho Chi Minh City','port_code'=>'VNSGN','country_code'=>'VN','country_name'=>'Vietnam','province_region'=>'Ho Chi Minh','latitude'=>10.783,'longitude'=>106.700,'harbor_size'=>'large','harbor_type'=>'river','congestion_score'=>45],
            ['port_name'=>'Hai Phong','port_code'=>'VNHPH','country_code'=>'VN','country_name'=>'Vietnam','province_region'=>'Hai Phong','latitude'=>20.867,'longitude'=>106.683,'harbor_size'=>'medium','harbor_type'=>'coastal','congestion_score'=>28],
            ['port_name'=>'Laem Chabang','port_code'=>'THLCH','country_code'=>'TH','country_name'=>'Thailand','province_region'=>'Chonburi','latitude'=>13.083,'longitude'=>100.883,'harbor_size'=>'large','harbor_type'=>'coastal','congestion_score'=>35],
            ['port_name'=>'Manila','port_code'=>'PHMNL','country_code'=>'PH','country_name'=>'Philippines','province_region'=>'Metro Manila','latitude'=>14.583,'longitude'=>120.967,'harbor_size'=>'large','harbor_type'=>'coastal','congestion_score'=>52],
            ['port_name'=>'Kaohsiung','port_code'=>'TWKHH','country_code'=>'TW','country_name'=>'Taiwan','province_region'=>'Kaohsiung','latitude'=>22.617,'longitude'=>120.283,'harbor_size'=>'large','harbor_type'=>'coastal','congestion_score'=>38],
            ['port_name'=>'Valencia','port_code'=>'ESVLC','country_code'=>'ES','country_name'=>'Spain','province_region'=>'Valencia','latitude'=>39.450,'longitude'=>-0.333,'harbor_size'=>'large','harbor_type'=>'coastal','congestion_score'=>30],
            ['port_name'=>'Genoa','port_code'=>'ITGOA','country_code'=>'IT','country_name'=>'Italy','province_region'=>'Liguria','latitude'=>44.400,'longitude'=>8.917,'harbor_size'=>'large','harbor_type'=>'coastal','congestion_score'=>28],
            ['port_name'=>'Marseille','port_code'=>'FRMRS','country_code'=>'FR','country_name'=>'France','province_region'=>'Provence','latitude'=>43.300,'longitude'=>5.367,'harbor_size'=>'large','harbor_type'=>'coastal','congestion_score'=>25],
            ['port_name'=>'Manzanillo','port_code'=>'MXZLO','country_code'=>'MX','country_name'=>'Mexico','province_region'=>'Colima','latitude'=>19.067,'longitude'=>-104.317,'harbor_size'=>'large','harbor_type'=>'coastal','congestion_score'=>38],
            ['port_name'=>'Novorossiysk','port_code'=>'RUNVS','country_code'=>'RU','country_name'=>'Russia','province_region'=>'Krasnodar','latitude'=>44.733,'longitude'=>37.767,'harbor_size'=>'large','harbor_type'=>'coastal','congestion_score'=>35],
        ];
        foreach ($ports as $p) {
            $level = $p['congestion_score']>50 ? 'high' : ($p['congestion_score']>30 ? 'moderate' : 'low');
            DB::table('ports')->insertOrIgnore(array_merge($p,['congestion_level'=>$level,'first_port_of_entry'=>true,'is_active'=>true,'created_at'=>$now,'updated_at'=>$now]));
        }
    }
}
