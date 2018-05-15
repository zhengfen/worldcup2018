<?php

use Illuminate\Database\Seeder;
use App\Match;
use Zttp\Zttp;

class GroupMatchSeeder extends Seeder
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
        $groups = $response['groups'];
        foreach(['a','b','c','d','e','f','g','h'] as $key=>$value){
            foreach($groups[$value]['matches'] as $match){
                Match::create(['id'=>$match['name'],
                    'team_h'=>$match['home_team'],
                    'team_a'=>$match['away_team'],
                    'date'=>Carbon\Carbon::createFromFormat('Y-m-d\TH:i:sO',$match['date'])->setTimezone('Europe/Zurich'),
                    'score_h'=>$match['home_result'],
                    'score_a'=>$match['away_result'],
                    'stadium_id'=>$match['stadium'],
                    'type'=>$match['type'],
                    'group_id'=>$key + 1,
                    ]);     
            }
        }
    }
}


/*
        {
          "name": 1,
          "type": "group",
          "home_team": 1,
          "away_team": 2,
          "home_result": null,
          "away_result": null,
          "date": "2018-06-14T18:00:00+03:00",  // 2018-06-14T18:00:00 local time   ; +03:00 timezone  
          "stadium": 1,
          "channels": [4,6],
          "finished": false
        },
 * 
 * >>> $date= Carbon\Carbon::createFromFormat('Y-m-d\TH:i:sO',"2018-06-14T18:00:00+03:00")
    => Carbon\Carbon @1528988400 {#807
     date: 2018-06-14 18:00:00.0 +03:00,
   }
    >>> $date->setTimezone('Europe/Zurich')
    => Carbon\Carbon @1528988400 {#807
     date: 2018-06-14 17:00:00.0 Europe/Zurich (+02:00),
   }
 *  */