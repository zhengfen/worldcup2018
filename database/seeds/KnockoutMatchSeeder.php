<?php

use Illuminate\Database\Seeder;
use App\Match;
use Zttp\Zttp;

class KnockoutMatchSeeder extends Seeder
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
            //App\Knockout::create(['id'=>$key+1,'name'=>$knockout[$value]['name'],'slug'=>$value]);    
            foreach($knockout[$value]['matches'] as $match){
                Match::create(['id'=>$match['name'],
                    'team_h'=>null,
                    'team_a'=>null,
                    'date'=>Carbon\Carbon::createFromFormat('Y-m-d\TH:i:sO',$match['date'])->setTimezone('Europe/Zurich'),
                    'score_h'=>$match['home_result'],
                    'score_a'=>$match['away_result'],
                    'pen_h'=>$match['home_penalty'],
                    'pen_a'=>$match['away_penalty'],
                    'stadium_id'=>$match['stadium'],
                    'type'=>$match['type'],
                    'knockout_id'=>$key+1,
                    'team_h_description' =>$match['home_team'],
                    'team_a_description' =>$match['away_team'],
                    ]);     
            }
        }
    }
}
/*
 * protected $fillable = ['team_h','team_a','date','score_h','score_a','pen_h','pen_a','stadium','type','id'];
  "knockout": {
    "round_16": {
      "name": "Round of 16",
      "matches": [
        {
          "name": 49,
          "type": "qualified",
          "home_team": "winner_a",
          "away_team": "runner_b",
          "home_result": null,
          "away_result": null,
          "home_penalty": null,
          "away_penalty": null,
          "winner": null,
          "date": "2018-06-30T17:00:00+03:00",
          "stadium": 11,
          "channels": [4],
          "finished": false
        }, */