<?php

namespace App;

use Illuminate\Database\Eloquent\Model;

class Knockout extends Model
{
    public $timestamps = false;
    protected $fillable = ['id','name','slug'];
    //Relationships
    public function matches(){
        return $this->hasMany('App\Match')->orderBy('id');
    }
}
