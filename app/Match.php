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
        return $this->date->gt(Carbon::now()->addDay());    //addDay();  // addDays(2)
    }
    
    public function allow_update(){
        if (auth()->guest())  return false;
        $allowed_users = ['fen','admin'];  // array of username
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
}
