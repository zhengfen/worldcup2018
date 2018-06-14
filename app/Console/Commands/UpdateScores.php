<?php
namespace App\Console\Commands; 
use Illuminate\Console\Command;
use Illuminate\Support\Facades\DB;
use App\Match;
use Zttp\Zttp;

class UpdateScores extends Command
{
  /**
   * The name and signature of the console command.
   *
   * @var string
   */
  protected $signature = 'update:scores';
 
  /**
   * The console command description.
   *
   * @var string
   */
  protected $description = 'Update match scores from remote json file';
 
  /**
   * Create a new command instance.
   *
   * @return void
   */
  public function __construct()
  {
    parent::__construct();
  }
 
  /**
   * Execute the console command.
   *
   * @return mixed
   */
  public function handle()
  {
    $url = 'https://raw.githubusercontent.com/lsv/fifa-worldcup-2018/master/data.json';
    $response = Zttp::get($url)->json();
    // group matches
    $groups = $response['groups'];
    foreach(['a','b','c','d','e','f','g','h'] as $key=>$value){
        foreach($groups[$value]['matches'] as $match){
            if( $match['home_result'] == null || $match['home_result'] ==null ) continue;
            Match::find($match['name'])->update(['score_h'=>$match['home_result'], 'score_a'=>$match['away_result']]);
        }
    }
    // knockout matches
    $knockout = $response['knockout'];        
    foreach(['round_16','round_8','round_4','round_2_loser','round_2'] as $key=>$value){
        foreach($knockout[$value]['matches'] as $match){
            if( $match['home_result'] == null || $match['home_result'] ==null ) continue;
            Match::find($match['name'])->update(['score_h'=>$match['home_result'], 'score_a'=>$match['away_result']]);
        }
    }
  }
}