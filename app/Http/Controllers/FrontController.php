<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Match;
use App\Pronostic;
use App\User;
use Illuminate\Support\Facades\DB;
use Carbon\Carbon; 

class FrontController extends Controller
{

    /**
     * Show the application dashboard.
     *
     * @return \Illuminate\Http\Response
     */
    public function index()
    {
        return view('home',[
            'page' => 'home',
        ]);
    }
    
    public function welcome()
    {
        return view('welcome',[
            'page' => 'home',
        ]);
    }
    
     public function ranking(Request $request)
    {   
        $colorArray = array("ff0000","00ff00", "000000", "00b7ef", "800000", "ff6600", "808000", "008080", "0000ff", "666699", "808080", "ff9900", "99cc00", "33cccc", "800080", "ff00ff", "ffcc00", "ffff00", "00ff00", "00ffff", "00ccff", "c0c0c0", "ff99cc", "ffcc99", "ccffcc", "ccffff", "cc99ff", "5877ad", "5da4de", "045def", "a45208", "4e874f", "4d5e10", "4d5e10", "4d5e10", "9e4a10");
        $colorNum = count($colorArray);
        // $users = User::where('status',1)->get(); // for alro
        $users = User::all()->except(env('ADMIN_ID')); 
        $dataset = array();
        $matches = Match::orderBy('date')->get();   
        foreach($users as $key=>$user) {
            array_push( $dataset, ['label'=>$user->name,'data'=>$user->points($matches),'backgroundColor'=>'rgba(0, 0, 0, 0)','borderColor'=>'#'.$colorArray[$key%$colorNum], 'borderWidth'=>1]); 
        }        
        if ($request->wantsJson()) {
            return json_encode($dataset);         
        }
        usort($dataset, function ($a,$b){ return end($b['data']) <=> end($a['data']); });   
        return view('ranking',[
            'page' => 'ranking',
            'dataset' => $dataset,
        ]);
    }

    public function vue(){
        return view('vue');
    }
    
    public function statistics(){
        
    }
    
}

        // statistics for each match 
        /*
        $today = Carbon::now();
        $tomorrow = Carbon::now()->addHours(24); 
        $matches = Match::where('date','<',$tomorrow)->where('date','>', $today)->get();
        foreach($matches as $match){
         //   $pronostics_count = Pronostic::where('match_id',$match->id)->count();
         //   $count_tie = DB::table('pronostics')->where('match_id',$match->id)->whereColumn('score_h','score_a')->count();
         //   $count_h = DB::table('pronostics')->where('match_id',$match->id)->whereColumn('score_h','>','score_a')->count();
         //   $count_a = DB::table('pronostics')->where('match_id',$match->id)->whereColumn('score_h','<','score_a')->count(); 
            $pronostics = Pronostic::where('match_id',$match->id)->get();
            $count_h = 0;
            $count_a = 0;
            $count_tie = 0;
            foreach($pronostics as $pronostic){
                switch ($pronostic->score_h <=> $pronostic->score_a){
                    case 0 : $count_tie +=1; break;   // tie
                    case 1 : $count_h +=1; break;  // home team wins
                    case -1: $count_a +=1; break;  // home team loses
                }  
            }
        }
         */