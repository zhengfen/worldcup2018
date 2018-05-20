<?php

namespace App;
use Illuminate\Database\Eloquent\Model;

class Pronostic extends Model
{
    protected $fillable = ['user_id','match_id','team_h','team_a','score_h','score_a','pen_h','pen_a'];
    // The relationships to always eager-load.
    protected $with = ['match'];     
    // The accessors to append to the model's array form.
    protected $appends = ['group_name'];
    
    //relationships
    public function user()
    {
        return $this->belongsTo('App\User', 'user_id');
    }
    
    public function match(){
        return $this->belongsTo('App\Match', 'match_id');
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
    public function getGroupNameAttribute(){          
        $group_name = ['A','B','C','D','E','F','G','H'];
        $group_id = $this->match->group_id;
        if($group_id)
            return $group_name[$group_id-1];
        else return null; 
    }
    
}
