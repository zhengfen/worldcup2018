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
    
    protected function dataset(){
        $colorArray = array("ff0000","00ff00", "000000", "00b7ef", "800000", "ff6600", "808000", "008080", "0000ff", "666699", "808080", "ff9900", "99cc00", "33cccc", "800080", "ff00ff", "ffcc00", "ffff00", "00ff00", "00ffff", "00ccff", "c0c0c0", "ff99cc", "ffcc99", "ccffcc", "ccffff", "cc99ff", "5877ad", "5da4de", "045def", "a45208", "4e874f", "4d5e10", "4d5e10", "4d5e10", "9e4a10");
        $colorNum = count($colorArray);
        // $users = User::where('status',1)->get(); // for alro
        $users = User::all()->except(env('ADMIN_ID')); 
        $dataset = array();
        $matches = Match::orderBy('date')->get();   
        foreach($users as $key=>$user) {
            array_push( $dataset, ['label'=>$user->name,'data'=>$user->points($matches),'backgroundColor'=>'rgba(0, 0, 0, 0)','borderColor'=>'#'.$colorArray[$key%$colorNum], 'borderWidth'=>1]); 
        } 
        return $dataset; 
    }
    
    public function ranking(Request $request)
    {   
        $dataset = $this->dataset(); 
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
    
    public function slides(Request $request)
    {   
        // slide 1 : ranking page
        $dataset = $this->dataset(); 
        usort($dataset, function ($a,$b){ return end($b['data']) <=> end($a['data']); });          
        // slide 2 : matches
        $num = env('SLIDES_MATCH_NUM',5); 
        $matches = Match::with(['homeTeam','awayTeam']);
        $matches_p = $matches->where('date','<',Carbon::now()->subHours(2)->toDateTimeString())->orderBy('date','desc')->take($num)->get()->sortBy('date');          
        $matches_n = $matches->where('date','>',Carbon::now()->subHours(2)->toDateTimeString())->orderBy('date')->take($num)->get(); 
        $statistics = Match::statistics_group($matches_p);
        // slide 3
        $num = env('DELTA_MATCH_NUM',3); // three matches per day..
        $dataset_delta = array();
        $count = count($dataset[0]['data']);
        foreach($dataset as $data) {
            array_push($dataset_delta, [
                'label' => $data['label'],
                'point' => ( $count-1 > $num ? (end($data['data'])-$data['data'][$count-1-$num]) : end($data['data']))   // array_sum(array_slice($data['data'], 0-$num, $num))
            ]);
        }
        usort($dataset_delta, function ($a,$b){ return $b['point'] <=> $a['point']; }); 
        return view('slides',[
            'dataset' => $dataset,
            'matches_p'=>$matches_p,
            'matches_n'=>$matches_n,
            'dataset_delta'=>$dataset_delta,
            'statistics'=>$statistics, 
        ]);
    }
}