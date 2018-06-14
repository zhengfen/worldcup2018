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
        return $this->type=='table-groups'? 'groups':'table-knockouts';
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
    
    public function allow_update(){
        if (auth()->guest())  return false;
        $allowed_users = ['fen','admin','gr'];  // array of username
        if (in_array(auth()->user()->username,$allowed_users)) return true;
        else return false;        
    }
    
    // get the group match between two teams
    static function group_match_between(int $team_h,int $team_a){
        if ( intval(($team_h-1)/4) !== intval(($team_a-1)/4) ) return null; // if the two teams are not in the same group, try to allowed too many database query
        return $match = DB::table('matches')->where([
            ['team_h',$team_h],
            ['team_a',$team_a],
            ])->orWhere([
            ['team_h',$team_a],
            ['team_a',$team_h],
            ])->orderBy('id')->first();
    }
    
    public function winner(int $match_id){
        $match = DB::table('matches')->where('id',$match_id)->first();
        switch ($match->score_h <=> $match->score_a ){
            case 1 : return team_h; 
            case 0 : return null;
            case -1 : return team_a;    
        }
    }
        
    public function finished(){
        if ($this->score_h !== null  && $this->score_a !== null)  return true;
        return false;
    }
    
    public function statistics(){
        $statistics = array();       
        $pronostics = Pronostic::where('match_id',$this->id)->get();
        if($pronostics->count()>0){
            $count_h = 0;
            $count_a = 0;
            $count_tie = 0;
            foreach($pronostics as $pronostic){
                if ($pronostic->score_h !== null && $pronostic->score_a !== null){
                    switch ($pronostic->score_h <=> $pronostic->score_a){
                        case 0 : $count_tie +=1; break;   // tie
                        case 1 : $count_h +=1; break;  // home team wins
                        case -1: $count_a +=1; break;  // home team loses
                    } 
                }
            } 
        }
        $statistics['percent_h'] = intval($count_h*100/$pronostics->count());
        $statistics['percent_a'] = intval($count_a*100/$pronostics->count());
        $statistics['percent_tie'] = intval($count_tie*100/$pronostics->count());    
        return $statistics; 
    }
    
    // pronostic statistics for each match 
    public static function statistics_group($matches=null){
        $statistics = array();
        if(!$matches){
            $matches = Match::where('id','<',49)->orderBy('date','asc')->get();        
        }
        foreach($matches as $match){
            $statistics[$match->id] = array();
         //   $pronostics_count = Pronostic::where('match_id',$match->id)->count();
         //   $count_tie = DB::table('pronostics')->where('match_id',$match->id)->whereColumn('score_h','score_a')->count();
         //   $count_h = DB::table('pronostics')->where('match_id',$match->id)->whereColumn('score_h','>','score_a')->count();
         //   $count_a = DB::table('pronostics')->where('match_id',$match->id)->whereColumn('score_h','<','score_a')->count(); 
        //    SELECT COUNT(*) FROM pronostics WHERE match_id=1 UNION SELECT COUNT(*) FROM pronostics WHERE match_id=1 AND score_h > score_a UNION SELECT COUNT(*) FROM pronostics WHERE match_id=1 AND score_h < score_a
            $pronostics = Pronostic::where('match_id',$match->id)->get();
            if($pronostics->count()>0){
                $count_h = 0;
                $count_a = 0;
                $count_tie = 0;
                foreach($pronostics as $pronostic){
                    if ($pronostic->score_h !== null && $pronostic->score_a !== null){
                        switch ($pronostic->score_h <=> $pronostic->score_a){
                            case 0 : $count_tie +=1; break;   // tie
                            case 1 : $count_h +=1; break;  // home team wins
                            case -1: $count_a +=1; break;  // home team loses
                        } 
                    }
                } 
                $statistics[$match->id]['percent_h'] = intval($count_h*100/$pronostics->count());
                $statistics[$match->id]['percent_a'] = intval($count_a*100/$pronostics->count());
                $statistics[$match->id]['percent_tie'] = intval($count_tie*100/$pronostics->count());    
            }
        }
        return $statistics; 
    }
}
