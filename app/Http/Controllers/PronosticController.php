<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use App\Match;
use App\Group;
use App\Knockout;
use App\Pronostic;
use App\User;
use Carbon\Carbon;

class PronosticController extends Controller
{
    protected $rules = [
        'team_h' =>'nullable|integer',
        'team_a' =>'nullable|integer',
        'score_h' =>'nullable|integer',
        'score_a' =>'nullable|integer',
        'pen_h' =>'nullable|integer',
        'pen_a' =>'nullable|integer',
    ];
    
    public function __construct()
    {
        $this->middleware('auth');
    }
    
    public function index()
    {
        $groups = Group::with(['matches','teams','matches.homeTeam','matches.awayTeam','matches.stadium'])->get(); 
        $knockouts = Knockout::with(['matches','matches.homeTeam','matches.awayTeam','matches.stadium'])->get();  
        $pronostics = Auth::user()->pronostics;
        // construct standings array for all groups
        $standings = Auth::user()->standings($groups);   
        return view('pronostics.index',[
            'groups' => $groups,
            'knockouts'=>$knockouts,
            'pronostics'=>$pronostics,
            'page'=>'pronostics',
            'standings'=>$standings,
        ]);
    } 
    // get pronostics 
    public function index_json()
    {          
        $pronostics = Auth::user()->pronostics;
        return $pronostics;        
    }
    
    // ajax udpate    
    public function update_scores(Request $request){
        //validate
        $match = Match::find($request->match_id);
        if  ( $match->date->gt(Carbon::now()->addHours(24)) ) {
            $pronostic = Pronostic::updateOrCreate(
                ['user_id'=>auth()->id(),'match_id'=>$request->match_id],
                ['score_h'=>$request->score_h,'score_a'=>$request->score_a]
                );
            Auth::user()->update_knockouts();
        }
    }

    
}
