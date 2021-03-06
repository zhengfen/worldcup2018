<?php

namespace App\Http\Controllers;
use Illuminate\Http\Request;

use App\User;
use App\Pronostic; 

class AdminController extends Controller
{
    public function __construct()
    {
        $this->middleware('auth');
    }
    public function index()
    {
        $users = User::all()->except(env('ADMIN_ID'));   // admin's id is 3
        return view('admin',[
            'users' => $users,
            'page' => 'admin',
        ]);
    }
    
    public function toggle_status(Request $request){
        $user = User::find($request->user_id);
        if ($user) {
            if ($user->status == 0)  $user->update(['status'=>1]);
            else $user->update(['status'=>0]);
        }
        return response()->json(['status'=>$user->status]);
    }
    // delete the users did not put pronostics
    public function clear_users(){
        $users = User::all()->except(env('ADMIN_ID'))->except(env('SUPER_ADMIN_ID')); 
        foreach($users as $user){
            $delete = true; 
            foreach($user->pronostics as $pronostic){
                if ( $pronostic->score_h !== null && $pronostic->score_a !== null) { 
                    $delete = false; 
                    break;
                }
            }
            if ($delete==true){
                foreach($user->pronostics as $pronostic){
                    $pronostic->delete();
                }
                $user->delete();
            }
        }        
    }
    // delete pronostics from unexist users
    public function clear_pronostics(){
        $pronostics = Pronostic::all(); 
        $users_id = User::all()->pluck('id')->toArray();
        foreach($pronostics as $pronostic){
            if (! in_array($pronostic->user_id, $users_id) ){
                $pronostic->delete();
            }
        }
    }
}
