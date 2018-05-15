<?php

use Illuminate\Database\Seeder;
use App\Match;
use Zttp\Zttp;

class MatchDateSeeder extends Seeder
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
        // group matches
        $groups = $response['groups'];
        foreach(['a','b','c','d','e','f','g','h'] as $key=>$value){
            foreach($groups[$value]['matches'] as $match){
                Match::find($match['name'])->update(['date'=>Carbon\Carbon::createFromFormat('Y-m-d\TH:i:sO',$match['date'])->setTimezone('Europe/Zurich')]);
            }
        }
        // knockout matches
        $knockout = $response['knockout'];        
        foreach(['round_16','round_8','round_4','round_2_loser','round_2'] as $key=>$value){
            foreach($knockout[$value]['matches'] as $match){
                Match::find($match['name'])->update(['date'=>Carbon\Carbon::createFromFormat('Y-m-d\TH:i:sO',$match['date'])->setTimezone('Europe/Zurich'),]);
            }
        }
    }
}
