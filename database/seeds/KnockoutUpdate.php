<?php

use Illuminate\Database\Seeder;
use App\Match;
use Zttp\Zttp;

class KnockoutUpdate extends Seeder
{
    /**
     * Run the database seeds.
     *
     * @return void
     */
    public function run()
    {
        $url = 'https://raw.githubusercontent.com/lsv/fifa-worldcup-2018/master/data.json';
        $response = Zttp::get($url)->json();
        $knockout = $response['knockout'];        
        foreach(['round_16','round_8','round_4','round_2_loser','round_2'] as $key=>$value){
            foreach($knockout[$value]['matches'] as $match){
                $match = Match::find($match['name'])->update([
                    'team_h_description' =>$match['home_team'],
                    'team_a_description' =>$match['away_team'],
                ]);
            }
        }
    }
}
