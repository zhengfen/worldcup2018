<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Team;
use App\Group;

class GroupController extends Controller
{
    public function index(){
        $groups = Group::all();
        $group_teams = Team::all()->groupBy('group_id'); 
        return view('groups.index',['groups'=>$groups,   
            'group_teams'=>$group_teams,
        ]);
    }
}
