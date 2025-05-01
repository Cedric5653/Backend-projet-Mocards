<?php

namespace Database\Seeders;

use Illuminate\Database\Console\Seeds\WithoutModelEvents;

use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;

class LocalisationSeeder extends Seeder
{
    public function run()
    {
        // Remplacez ou complétez la liste ci-dessous selon les districts réels
        DB::table('localisation')->insert([
            [
                'region' => 'Centre',
                'province' => 'Kadiogo',
                'ville' => 'Ouagadougou',
                'district_sanitaire' => 'Baskuy'
            ],
            [
                'region' => 'Centre',
                'province' => 'Kadiogo',
                'ville' => 'Ouagadougou',
                'district_sanitaire' => 'Bogodogo'
            ],
            [
                'region' => 'Centre',
                'province' => 'Kadiogo',
                'ville' => 'Ouagadougou',
                'district_sanitaire' => 'Boulmiougou'
            ],
            [
                'region' => 'Centre',
                'province' => 'Kadiogo',
                'ville' => 'Ouagadougou',
                'district_sanitaire' => 'Noong-Massom'
            ],
            [
                'region' => 'Centre',
                'province' => 'Kadiogo',
                'ville' => 'Ouagadougou',
                'district_sanitaire' => 'Sig-Noghin'
            ],
            [
                'region' => 'Centre',
                'province' => 'Kadiogo',
                'ville' => 'Ouagadougou',
                'district_sanitaire' => 'Tanghin-Dassouri'
            ],
        
        ]);
    }
}
