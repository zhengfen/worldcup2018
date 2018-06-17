<?php

namespace App;

use Illuminate\Notifications\Notifiable;
use Illuminate\Foundation\Auth\User as Authenticatable;

class User extends Authenticatable
{
    use Notifiable;
    // The attributes that are mass assignable.
    protected $fillable = [
        'name', 'email', 'password','username','status'
    ];
    // The attributes that should be hidden for arrays.
    protected $hidden = [
        'password', 'remember_token',
    ];    
    // The relationships to always eager-load.
   // protected $with = ['pronostics']; 
    
    // relationship
    public function pronostics(){
        return $this->hasMany('App\Pronostic');
    } 
    
    public function score_h(int $match_id){
        // verify if user has entered scores for the match
        return $this->pronostics->where('match_id',$match_id)->isEmpty() ? null : $this->pronostics->where('match_id',$match_id)->first()->score_h;        
    }
    
    public function score_a(int $match_id){
        // verify if user has entered scores for the match
        return $this->pronostics->where('match_id',$match_id)->isEmpty() ? null : $this->pronostics->where('match_id',$match_id)->first()->score_a;
    }   
                
    public function pronostic(int $match_id){
        return $this->pronostics->where('match_id',$match_id)->first();
    }   
   
    // team standings according to user pronostics, grouped by group    
    public function standings($groups=null){
        $standings = array();
        if(!$groups){
            $groups = Group::with(['matches','teams'])->get();             
        }
        foreach($groups as $group){
            $standings[$group->id] = array();
            // assign initial values for the teams in the group
            foreach($group->teams as $team){
                $standings[$group->id][$team->id] = array(
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
            // parse the pronostics for the group matches            
            foreach($group->matches as $match){
                $pronostic = $this->pronostics->where('match_id',$match->id)->first();  // ->first() return an instance of the first found model, or null otherwise.
                if(!$pronostic||is_null($pronostic->score_h) || is_null($pronostic->score_a)) continue;
                $team_h = $match->team_h;  // team id
                $team_a = $match->team_a;
                $standings[$group->id][$team_h]['played'] += 1;
                $standings[$group->id][$team_a]['played'] += 1;
                $standings[$group->id][$team_h]['goalsFor'] += $pronostic->score_h;
                $standings[$group->id][$team_h]['goalsAgainst'] += $pronostic->score_a;            
                $standings[$group->id][$team_a]['goalsFor'] += $pronostic->score_a;
                $standings[$group->id][$team_a]['goalsAgainst'] += $pronostic->score_h;
                switch ($pronostic->score_h <=> $pronostic->score_a){
                    case 0 : $standings[$group->id][$team_h]['draws'] += 1; $standings[$group->id][$team_a]['draws'] += 1; break;   // tie
                    case 1 : $standings[$group->id][$team_h]['wins'] += 1;  $standings[$group->id][$team_a]['losts'] += 1; break;  // home team wins
                    case -1: $standings[$group->id][$team_h]['losts'] += 1;  $standings[$group->id][$team_a]['wins'] += 1; break;  // home team loses
                }                 
            }
            usort($standings[$group->id],array($this, "cmp_standings"));   
        } 
        return $standings;
    }
    
    // compare team standings in group phase, with values of user pronostics
    function cmp_standings($a,$b){     
        //if both teams have no pronostics yet
        if($a['played'] == 0 && $b['played'] == 0) return 0;
        // Points (3 points for a win, 1 point for a draw, 0 points for a loss)
        $result = ($b['wins']*3+$b['draws'])<=>($a['wins']*3+$a['draws']);
        if ($result !== 0) return $result;
        // Overall goal difference
        $result = ($b['goalsFor']-$b['goalsAgainst'])<=>($a['goalsFor']-$a['goalsAgainst']);
        if ($result !== 0) return $result;
        // Overall goals scored
        $result = $b['goalsFor']<=> $a['goalsFor'];
        if ($result !== 0) return $result;
        // Points in matches between tied teams
        $match = Match::group_match_between($a['team_id'],$b['team_id']);
        if ( $winner_id = $this->pronostic_winner($match->id)) {        
            if($winner_id==$a['team_id']) return -1;
            if($winner_id==$b['team_id']) return 1;
        }
        return 0; 
    }
    
    // get winner of a match according to user pronostics
    function pronostic_winner(int $match_id){
        $pronostic = $this->pronostics->where('match_id',$match_id)->first();
        if ( $pronostic && $pronostic->score_h !== null && $pronostic->score_a !== null ){
            switch ($pronostic->score_h <=> $pronostic->score_a ){
                case 1 : return $pronostic->match->team_h; 
                case 0 : return null;
                case -1 : return $pronostic->match->team_a; 
            }
        }
        return null;
    }
       
    // get the 16 qualified teams from match 49-56
    public function qualified_16(){
        $qualified = [];
        $pronostics = $this->pronostics->where('match_id','>','48')->where('match_id','<','57')->all();
        foreach($pronostics as $pronostic){
            if($pronostic->team_h)  array_push($qualified,$pronostic->team_h);
            if($pronostic->team_a)  array_push($qualified,$pronostic->team_a);
        }
        return $qualified; 
    }
    
    // get the 8 qualified teams from match 57-60
    public function qualified_8(){
        $qualified = [];
        $pronostics = $this->pronostics->where('match_id','>','56')->where('match_id','<','61')->all();
        foreach($pronostics as $pronostic){
            if($pronostic->team_h)  array_push($qualified,$pronostic->team_h);
            if($pronostic->team_a)  array_push($qualified,$pronostic->team_a);
        }
        return $qualified;
    }
    
    // get the 4 qualified teams  from match 61,62
    public function qualified_4(){
        $qualified = [];
        $pronostics = $this->pronostics->where('match_id','>','60')->where('match_id','<','63')->all();
        foreach($pronostics as $pronostic){
            if($pronostic->team_h)  array_push($qualified,$pronostic->team_h);
            if($pronostic->team_a)  array_push($qualified,$pronostic->team_a);
        }
        return $qualified;
    }
    
    // get the 2 qualified teams  from match 64
    public function qualified_2(){
        $qualified = [];
        $pronostic = $this->pronostics->where('match_id','64')->first();        
        if($pronostic->team_h)  array_push($qualified,$pronostic->team_h);
        if($pronostic->team_a)  array_push($qualified,$pronostic->team_a);  
        return $qualified;
    }
    
    // get the 3rd  from match 63
    public function third(){
        $pronostic = $this->pronostics->where('match_id','63')->first();
        if ($pronostic && $pronostic->team_h !== null && $pronostic->team_a !== null && $pronostic->score_h !== null && $pronostic->score_a !== null ){  
            if($pronostic->score_h > $pronostic->score_a) return $pronostic->team_h;
            return $pronostic->team_a;
        }
        return null;
    }
    // get the champion  from match 64
    public function first(){
        $pronostic = $this->pronostics->where('match_id','64')->first();
        if ($pronostic && $pronostic->team_h !== null && $pronostic->team_a !== null && $pronostic->score_h !== null && $pronostic->score_a !== null ){  
            if($pronostic->score_h > $pronostic->score_a) return $pronostic->team_h;
            return $pronostic->team_a;
        }
        return null;
    }
    
    // if the user has filled all the pronostics for the given group
    public function filled_group(Group $group){
        foreach ($group->matches as $match){
            if(is_null($this->score_h($match->id)) || is_null($this->score_a($match->id))) return false;
        }
        return true;
    }   
    
    public function points($matches=null){
        $points = [];
        $point = 0;
        array_push($points,0);
        if(!$matches){
            $matches = Match::orderBy('date')->get();        
        }
        foreach($matches as $match){
            if (is_null($match->score_h) || is_null($match->score_a))   break;   // the match is not finished yet
            else{
                if(is_null($this->score_h($match->id)) || is_null($this->score_a($match->id))) { $point +=0; array_push($points,$point);continue;}  // user have not complete the pronostics for the match
                switch(true){
                    case($match->id<49): // group match[1-48]
                        if(($this->score_h($match->id)<=>$this->score_a($match->id)) == ($match->score_h<=>$match->score_a)){
                            $point += 2;
                            if($this->score_h($match->id) == $match->score_h) $point += 1;
                            if($this->score_a($match->id) == $match->score_a) $point += 1;
                        } 
                        array_push($points,$point);
                        break;
                    case($match->id>48 && $match->id<57): // qualified [49-56] 1/8
                        if (  in_array($match->team_h,$this->qualified_16()) )   $point += 4;
                        if (  in_array($match->team_a,$this->qualified_16()) )   $point += 4;
                        array_push($points,$point);
                        break;
                    case($match->id>56 && $match->id<61):  // Quarts de finale  [57-60] 1/4  [61-62]
                        if (  in_array($match->team_h,$this->qualified_8()) )   $point += 6;
                        if (  in_array($match->team_a,$this->qualified_8()) )   $point += 6;
                        array_push($points,$point);
                        break;
                    case($match->id>60 && $match->id<63):  // demi [61-62]
                        if (  in_array($match->team_h,$this->qualified_4()) )   $point += 8;
                        if (  in_array($match->team_a,$this->qualified_4()) )   $point += 8;
                        array_push($points,$point);
                        break;
                    case($match->id==63):  // 3rd
                        // check if teams are correct
                        if( $match->score_h > $match->score_a)  $third = $match->team_h;
                        else $third = $match->team_a;
                        if (  $this->third() == $third) $point += 10;
                        array_push($points,$point);
                        break;
                    case($match->id==64): // final
                        if (  in_array($match->team_h,$this->qualified_2()) )   $point += 10;
                        if (  in_array($match->team_a,$this->qualified_2()) )   $point += 10;
                        // if the champion is right
                        if( $match->score_h > $match->score_a)  $first = $match->team_h;
                        else $first = $match->team_a;
                        if (  $this->first() == $first)   $point += 20;
                        array_push($points,$point);
                        break;                        
                }
            }            
        }
        return $points;
    }
    
    // create initial pronostics for user
    public function create_pronostics(){
        for($i =1;$i<65;$i++){
            $pronostic = Pronostic::firstOrCreate(
                ['user_id'=>$this->id,'match_id'=>$i]
            );
        }
    }
    
    public function update_knockouts(){
        $knockout_matches = Match::where('id','>',48)->orderBy('date')->get();
        $standings = $this->standings();
        foreach($knockout_matches as $match){            
            $pronostic = $this->pronostics->where('match_id',$match->id)->first();
            $team = $this->getPronosticKnockoutTeam($match->type, $match->team_h_description, $standings);
            if($team !== $pronostic->team_h){
                $pronostic->update(['team_h'=>$team, 'score_h'=>null]);
            }
            $team = $this->getPronosticKnockoutTeam($match->type, $match->team_a_description, $standings);
            if($team !== $pronostic->team_a){
                $pronostic->update(['team_a'=>$team, 'score_a'=>null]);            
            }
        }
    }
    // update table pronostics team for knockout match
    public function getPronosticKnockoutTeam(string $match_type, string $matchteam, array $standings) {
        switch ($match_type) {
            default:
                return null;
            case 'qualified':                
                if ( is_string($matchteam)) {   
                    // "name": 49, "type": "qualified", "home_team": "winner_a", "away_team": "runner_b",
                    $splitted = explode('_',$matchteam);
                    // get the group
                    $group = Group::where('name', strtoupper($splitted[1]))->first();
                    if (!$group) {
                        throw new Exception('Group not found in '.matchteam);
                    }
                    // pronostic group winner team 
                    if ( $this->pronostic_finished($group) ){
                        if ($splitted[0] === 'winner'){
                            return $standings[$group->id][0]['team_id'];
                        }
                        else{
                            return $standings[$group->id][1]['team_id'];
                        }
                    }
                    return null;
                }
                throw new Error('matchteam variable should be a string ' + matchteam + ' given');

            case 'winner':
                $pronostic = $this->pronostics->where( 'match_id',intval($matchteam) )->first();  
                if ($pronostic->score_h !== null && $pronostic->score_a !== null) {
                    if ($pronostic->score_h > $pronostic->score_a){
                        return $pronostic->team_h;
                    }
                    else {
                        return $pronostic->team_a;
                    }
                }
                return null;

            case 'loser':
                $pronostic = $this->pronostics->where( 'match_id',intval($matchteam) )->first();
                if ($pronostic->score_h !== null && $pronostic->score_a !== null) {
                    if ($pronostic->score_h > $pronostic->score_a){
                        return $pronostic->team_a;
                    }
                    else {
                        return $pronostic->team_h;
                    }
                }
                return null;
        }
    }
    public function pronostic_finished(Group $group){
        foreach($group->matches as $match){
            $pronostic = $this->pronostics->where('match_id',$match->id)->first();
            if(is_null($pronostic->score_h) || is_null($pronostic->score_a)){
                return false;
            }            
        }
        return true;
    }         

}
