<?php

namespace App;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Facades\DB;

class Team extends Model
{
    public $timestamps = false;
    protected $fillable = ['name','abr','group_id','iso'];

    // Relationships 
    public function group(){
        return $this->belongsTo('App\Group', 'group_id');
    }    
    // accessor
    public function getImagePathAttribute(){
        return ($this->id && $this->abr)? asset('images/teams/'.$this->abr.'.png') :false;
    }  
       
}
