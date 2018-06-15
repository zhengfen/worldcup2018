<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Match;
use App\Group;
use App\Knockout;
use Zttp\Zttp;
use Carbon\Carbon;

class MatchController extends Controller
{
    protected $rules = [
        'time' => 'required|date',
        'team_h' =>'nullable|integer',
        'team_a' =>'nullable|integer',
        'score_h' =>'nullable|integer',
        'score_a' =>'nullable|integer',
        'pen_h' =>'nullable|integer',
        'pen_a' =>'nullable|integer',
    ];
    public function __construct()
    {
        $this->middleware('auth')->only(['create', 'store','edit','update','delete']);
    }
    public function index()
    {
        $groups = Group::with(['matches', 'teams','matches.homeTeam','matches.awayTeam','matches.stadium'])->get(); 
        $knockouts = Knockout::with(['matches','matches.homeTeam','matches.awayTeam','matches.stadium'])->get(); 
        return view('matches.index',[
            'groups' => $groups,
            'knockouts'=>$knockouts,
            'page'=>'matches',
        ]);
    }
    
    public function update_score_home(Request $request){
        Match::find($request->match_id)->update(['score_h'=>$request->score_h]);
    }
    
    public function update_score_away(Request $request){
        Match::find($request->match_id)->update(['score_a'=>$request->score_a]);
    }
    
    public function update_scores(Request $request){
        Match::find($request->match_id)->update(['score_h'=>$request->score_h,'score_a'=>$request->score_a ]);
    }   
    
    public function update_scores_json(){
        $url = 'https://raw.githubusercontent.com/lsv/fifa-worldcup-2018/master/data.json';
        $response = Zttp::get($url)->json();
        // group matches
        $groups = $response['groups'];
        foreach(['a','b','c','d','e','f','g','h'] as $key=>$value){
            foreach($groups[$value]['matches'] as $match){
                if( $match['home_result'] == null || $match['home_result'] ==null ) continue;
                Match::find($match['name'])->update([
                    'score_h'=>$match['home_result'], 
                    'score_a'=>$match['away_result']
                    ]);
            }
        }
        // knockout matches
        $knockout = $response['knockout'];        
        foreach(['round_16','round_8','round_4','round_2_loser','round_2'] as $key=>$value){
            foreach($knockout[$value]['matches'] as $match){
                if( $match['home_result'] == null || $match['home_result'] ==null ) continue;
                Match::find($match['name'])->update([
                    'team_h'=>$match['home_team'], 
                    'team_a'=>$match['away_team'], 
                    'score_h'=>$match['home_result'], 
                    'score_a'=>$match['away_result']
                ]);
            }
        }
    }
   
}
