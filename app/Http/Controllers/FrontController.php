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
    
     public function ranking()
    {   
        return view('ranking',[
            'page' => 'ranking',
        ]);
    }
    // provide data for ranking  $.getJSON("/ranking_json", function (result) 
    public function ranking_json()
    {
        $colorArray = array("ff0000","00ff00", "000000", "00b7ef", "800000", "ff6600", "808000", "008080", "0000ff", "666699", "808080", "ff9900", "99cc00", "33cccc", "800080", "ff00ff", "ffcc00", "ffff00", "00ff00", "00ffff", "00ccff", "c0c0c0", "ff99cc", "ffcc99", "ccffcc", "ccffff", "cc99ff", "5877ad", "5da4de", "045def", "a45208", "4e874f", "4d5e10", "4d5e10", "4d5e10", "9e4a10");
        $colorNum = count($colorArray);
      //  $users = User::where('status',1)->get(); // for alro
        $users = User::all()->except(env('ADMIN_ID')); 
        $dataset = array();
        foreach($users as $key=>$user) {
            array_push( $dataset, ['label'=>$user->name,'data'=>$user->points(),'backgroundColor'=>'#ffffff','borderColor'=>'#'.$colorArray[$key%$colorNum], 'borderWidth'=>1]); 
        }
        return json_encode($dataset);  
    }

    public function vue(){
        return view('vue');
    }
}
