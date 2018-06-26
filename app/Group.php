<?php

namespace App;

use Illuminate\Database\Eloquent\Model;

class Group extends Model
{
    public $timestamps = false;
    protected $fillable = ['name'];    
    //Relationships
    public function teams(){
        return $this->hasMany('App\Team');
    }
    public function matches(){
        return $this->hasMany('App\Match')->orderBy('id');
    }    
    
   // team standings within a group  // team: TeamModel|string, played: number = 0, wins: number = 0, draws: number = 0, losts: number = 0, goalsFor: number = 0, goalsAgainst: number = 0)
    public function standings(){
        $standings = array();
        // define initial velues
        foreach($this->teams as $team){
            $standings[$team->id] = array(
                'team_id'=>$team->id,
                'team_name'=>$team->name,
                'team_iso'=>$team->iso,
                'played' => 0,
                'wins' => 0,
                'draws' => 0,
                'losts'=> 0,
                'goalsFor' => 0,
                'goalsAgainst' => 0,
            );
        }
        // update values according to each match
        foreach($this->matches as $match){  // only group matches
            if (is_null($match->score_h) || is_null($match->score_a)) break;  // match not finished
            $standings[$match->team_h]['played'] += 1;
            $standings[$match->team_a]['played'] += 1;
            $standings[$match->team_h]['goalsFor'] += $match->score_h;
            $standings[$match->team_h]['goalsAgainst'] += $match->score_a;            
            $standings[$match->team_a]['goalsFor'] += $match->score_a;
            $standings[$match->team_a]['goalsAgainst'] += $match->score_h;
            switch ($match->score_h <=> $match->score_a){
                case 0 : $standings[$match->team_h]['draws'] += 1; $standings[$match->team_a]['draws'] += 1; break;
                case 1 : $standings[$match->team_h]['wins'] += 1;  $standings[$match->team_a]['losts'] += 1; break;  // home team wins
                case -1: $standings[$match->team_h]['losts'] += 1;  $standings[$match->team_a]['wins'] += 1; break;  // home team loses
            } 
        }
        usort($standings,array($this, "cmp_standings"));   
        return $standings;
    }
    
    // compare team standings in group phase
    static function cmp_standings($a,$b){
         //if both teams have no pronostics yet
        if($a['played'] == 0 && $b['played'] == 0) return 0;
        // compare points
        $cmp_points = ($b['wins']*3+$b['draws'])<=>($a['wins']*3+$a['draws']);
        if ($cmp_points !== 0) return $cmp_points;
        // Overall goal difference
        $result = ($b['goalsFor']-$b['goalsAgainst'])<=>($a['goalsFor']-$a['goalsAgainst']);
        if ($result !== 0) return $result;
        // Overall goals scored
        $result = $b['goalsFor']<=> $a['goalsFor'];
        if ($result !== 0) return $result;       
        //check the match between the two teams
        $match = Match::where('id','<',49)->where(['team_h'=>$a['team_id'], 'team_a'=>$b['team_id']])->first();
        if($match){
            if ($match->score_h > $match->score_a) return -1;
            if ($match->score_h < $match->score_a) return 1;
        }
        $match = Match::where('id','<',49)->where(['team_h'=>$b['team_id'], 'team_a'=>$a['team_id']])->first();
        if($match){
            if ($match->score_h > $match->score_a) return 1;
            if ($match->score_h < $match->score_a) return -1;
        }
        return 0;
    }
    
    public function finished(){
        foreach($this->matches as $match){
            if(is_null($match->score_h) || is_null($match->score_a)){
                return false;
            }            
        }
        return true;
    }  
    
}
