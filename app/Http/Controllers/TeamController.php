<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Team;

class TeamController extends Controller
{
    public function index(){
        $teams = Team::all();// ->groupBy('group_id');  
        return view('teams.index',['teams'=>$teams,
            
        ]);
    }
}
