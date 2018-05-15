<?php

use Illuminate\Database\Seeder;

class GroupSeeder extends Seeder
{
    /**
     * Run the database seeds.
     *
     * @return void
     */
    public function run()
    {
        $groups = ['A','B','C','D','E','F','G','H'];
        foreach ($groups as $group){
            App\Group::create(['name'=>$group]);            
        } 
    }
}
