<?php

use Illuminate\Database\Seeder;

class KnockoutSeeder extends Seeder
{
    /**
     * Run the database seeds.
     *
     * @return void
     */
    public function run()
    {
        $slugs = ['round_16','round_8','round_4','round_2_loser','round_2'];
        $names = ['Round of 16','Quarter-finals','Semi-finals','Third place play-off','Final'];
        foreach($slugs as $key=>$slug){
            App\Knockout::create(['id'=>$key+1,'name'=>$names[$key],'slug'=>$slug]);    
        }
    }
}
