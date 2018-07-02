<?php

namespace App;
use Illuminate\Database\Eloquent\Model;
use Carbon\Carbon;
use Illuminate\Support\Facades\DB;

class Match extends Model
{
    public $timestamps = false;
    protected $fillable = ['team_h','team_a','date','score_h','score_a','pen_h','pen_a','stadium_id','type','id','team_h_description','team_a_description'];
    // The attributes that should be mutated to dates.
    protected $dates = ['date'];    
    // The accessors to append to the model's array form.
    protected $appends = ['game_class', 'home_class', 'away_class'];
    //relationships  
    public function group()
    {
        return $this->belongsTo('App\Group','group_id');
    }
    public function stadium()
    {
        return $this->belongsTo('App\Stadium','stadium_id');
    }
    public function homeTeam()
    {
        return $this->belongsTo('App\Team','team_h');
    }
    public function awayTeam()
    {
        return $this->belongsTo('App\Team','team_a');
    }
    // accessors
    public function getGameClassAttribute(){
        return $this->type=='groups'? 'table-groups':'table-knockouts';
    }
    public function getHomeClassAttribute(){
        if ($this->score_h !== null  && $this->score_a !== null)  {
            if ($this->score_h == $this->score_a) {
                return $this->gameClass.'--draw';
            }
            if ($this->score_h > $this->score_a) {
                return $this->gameClass.'--winner';
            }
            if ($this->score_h < $this->score_a) {
                return $this->gameClass.'--loser';
            }
        }
        return '';
    }    
    public function getAwayClassAttribute(){
        if ($this->score_h !== null  && $this->score_a !== null)  {
            if ($this->score_h == $this->score_a) {
                return $this->gameClass.'--draw';
            }
            if ($this->score_h < $this->score_a) {
                return $this->gameclass.'--winner';
            }
            if ($this->score_h > $this->score_a) {
                return $this->gameclass.'--loser';
            }
        }
        return '';        
    }
    
    public function allow_pronostics(){
        return $this->date->gt(Carbon::now()->addHours(24));    
    }
    // the users allowed to update match results
    public function allow_update(){
        if (auth()->guest())  return false;
        if (in_array(auth()->user()->username,['fen','admin','gr'])) return true;
        else return false;        
    }
               
    public function finished(){
        if ($this->score_h !== null  && $this->score_a !== null)  return true;
        return false;
    }
    // pronostic statistics for a single group match 
    public function statistics(){
        $statistics = array();    
        if($this->id<49){
            $pronostics = Pronostic::where('match_id',$this->id)->get();
            if($pronostics->count()>0){
                $count_h = 0;
                $count_a = 0;
                foreach($pronostics as $pronostic){
                    if ($pronostic->score_h !== null && $pronostic->score_a !== null){
                        switch ($pronostic->score_h <=> $pronostic->score_a){
                            case 1 : $count_h +=1; break;  // home team wins
                            case -1: $count_a +=1; break;  // home team loses
                        } 
                    }
                } 
            }
            $statistics['percent_h'] = intval($count_h*100/$pronostics->count());
            $statistics['percent_a'] = intval($count_a*100/$pronostics->count());
            return $statistics;         
        }
        $users_count = \Cache::remember('users_count', 60 ,function () { 
            return User::where('status',1)->count();             
        });
        // from knockout matches
        if( is_null($this->team_h) && is_null($this->team_h) ) return null; 
        // Round of 16
        if($this->id>48 && $this->id<57){
            //  retrieve pronostics for 'Round of 16' from the cache or, if they don't exist, retrieve them from the database and add them to the cache. 
            $pronostics_16 = \Cache::remember('pronostics_16',60 ,function () {
                return Pronostic::where('match_id','>','48')->where('match_id','<','57')->get();
            });
            $count_h = $pronostics_16->where('team_h',$this->team_h)->count() +  $pronostics_16->where('team_a',$this->team_h)->count(); 
            $count_a = $pronostics_16->where('team_h',$this->team_a)->count() +  $pronostics_16->where('team_a',$this->team_a)->count();
            $statistics['percent_h'] = intval($count_h*100/$users_count);
            $statistics['percent_a'] = intval($count_a*100/$users_count);
            return $statistics;    
        }
        // Round of 8, Quarter-finals
        if($this->id>56 && $this->id<61){            
            $pronostics_8 = \Cache::remember('pronostics_8',60 ,function () {
                return Pronostic::where('match_id','>','56')->where('match_id','<','61')->get();
            });
            $count_h = $pronostics_8->where('team_h',$this->team_h)->count() +  $pronostics_8->where('team_a',$this->team_h)->count(); 
            $count_a = $pronostics_8->where('team_h',$this->team_a)->count() +  $pronostics_8->where('team_a',$this->team_a)->count();
            $statistics['percent_h'] = intval($count_h*100/$users_count);
            $statistics['percent_a'] = intval($count_a*100/$users_count);
            return $statistics;    
        }
        // Round of 4, Semi-finals
        if($this->id>60 && $this->id<63){            
            $pronostics_4 = \Cache::remember('pronostics_4',60 ,function () {
                return Pronostic::where('match_id','>','60')->where('match_id','<','63')->get();
            });
            $count_h = $pronostics_4->where('team_h',$this->team_h)->count() +  $pronostics_4->where('team_a',$this->team_h)->count(); 
            $count_a = $pronostics_4->where('team_h',$this->team_a)->count() +  $pronostics_4->where('team_a',$this->team_a)->count();
            $statistics['percent_h'] = intval($count_h*100/$users_count);
            $statistics['percent_a'] = intval($count_a*100/$users_count);
            return $statistics;    
        }
        // Third place play-off
        if($this->id==63){            
            $pronostics_63 = \Cache::remember('pronostics_63',60 ,function () {
                return Pronostic::where('match_id','63')->get();
            });
            $count_h = $pronostics_63->where('team_h',$this->team_h)->count() +  $pronostics_63->where('team_a',$this->team_h)->count(); 
            $count_a = $pronostics_63->where('team_h',$this->team_a)->count() +  $pronostics_63->where('team_a',$this->team_a)->count();
            $statistics['percent_h'] = intval($count_h*100/$users_count);
            $statistics['percent_a'] = intval($count_a*100/$users_count);
            return $statistics;    
        }
        // final
        if($this->id==64){            
            $pronostics_final = \Cache::remember('pronostics_final',60 ,function () {
                return Pronostic::where('match_id','64')->get();
            });
            $count_h = $pronostics_final->where('team_h',$this->team_h)->count() +  $pronostics_final->where('team_a',$this->team_h)->count(); 
            $count_a = $pronostics_final->where('team_h',$this->team_a)->count() +  $pronostics_final->where('team_a',$this->team_a)->count();
            $statistics['percent_h'] = intval($count_h*100/$users_count);
            $statistics['percent_a'] = intval($count_a*100/$users_count);
            return $statistics;    
        }
    }
    
    // pronostic statistics for group matches 
    public static function statistics_group($matches=null){
        $statistics = array();
        if(!$matches){
            $matches = Match::where('id','<',49)->orderBy('date','asc')->get();        
        }
        foreach($matches as $match){
            $statistics[$match->id] = array();       
            $pronostics = Pronostic::where('match_id',$match->id)->get();
            if($pronostics->count()>0){
                $count_h = 0;
                $count_a = 0;
                foreach($pronostics as $pronostic){
                    if ($pronostic->score_h !== null && $pronostic->score_a !== null){
                        switch ($pronostic->score_h <=> $pronostic->score_a){
                            case 1 : $count_h +=1; break;  // home team wins
                            case -1: $count_a +=1; break;  // home team loses
                        } 
                    }
                } 
                $statistics[$match->id]['percent_h'] = intval($count_h*100/$pronostics->count());
                $statistics[$match->id]['percent_a'] = intval($count_a*100/$pronostics->count()); 
            }
        }
        return $statistics; 
    }
    
    public static function update_knockouts(){
        $knockout_matches = Match::where('id','>',48)->orderBy('date')->get();
        foreach($knockout_matches as $match){            
            $team = $match->getKnockoutTeam($match->type, $match->team_h_description);
            if($team !== $match->team_h){
                $match->update(['team_h'=>$team]);
            }
            $team = $match->getKnockoutTeam($match->type, $match->team_a_description);
            if($team !== $match->team_a){
                $match->update(['team_a'=>$team]);            
            }
        }
    }
    
    // update table matches team for knockout match
    public function getKnockoutTeam(string $match_type, string $matchteam) {
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
                    // group winner team 
                    if ( $group->finished() ){
                        if ($splitted[0] === 'winner'){
                            return $group->standings()[0]['team_id']; 
                        }
                        else{
                            return $group->standings()[1]['team_id'];
                        }
                    }
                    return null;
                }
                throw new Error('matchteam variable should be a string ' + matchteam + ' given');

            case 'winner':
                $match = Self::where( 'id',intval($matchteam) )->first();  
                if ($match->score_h !== null && $match->score_a !== null) {
                    if ($match->score_h > $match->score_a){
                        return $match->team_h;
                    }
                    else {
                        return $match->team_a;
                    }
                }
                return null;

            case 'loser':
                $match = Self::where( 'id',intval($matchteam) )->first();  
                if ($match->score_h !== null && $match->score_a !== null) {
                    if ($match->score_h > $match->score_a){
                        return $match->team_a;
                    }
                    else {
                        return $match->team_h;
                    }
                }
                return null;
        }
    }
       
}
