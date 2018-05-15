<?php

use Illuminate\Database\Seeder;

class TeamsSeeder extends Seeder
{
    /**
     * Run the database seeds.
     *
     * @return void
     */
    public function run()
    {
        // A  country name in french
        DB::table('teams')->insert([
            'name' => 'Russie',
            'abr' => 'rus',
            'group_id' => 1,
        ]);
        
        DB::table('teams')->insert([
            'name' => 'Arabie Saoudite',
            'abr' => 'ksa',
            'group_id' => 1,
        ]);
        
        DB::table('teams')->insert([
            'name' => 'Uruguay',
            'abr' => 'uru',
            'group_id' => 1,
        ]);
        
        DB::table('teams')->insert([
            'name' => 'Egypte',
            'abr' => 'egy',
            'group_id' => 1,
        ]);
        //B
        DB::table('teams')->insert([
            'name' => 'Portugal',
            'abr' => 'por',
            'group_id' => 2,
        ]);
        
        DB::table('teams')->insert([
            'name' => 'Espagne',
            'abr' => 'esp',
            'group_id' => 2,
        ]);
        
        DB::table('teams')->insert([
            'name' => 'Maroc',
            'abr' => 'mar',
            'group_id' => 2,
        ]);
        
        DB::table('teams')->insert([
            'name' => 'Ri Iran',
            'abr' => 'irn',
            'group_id' => 2,
        ]);
        //C
        DB::table('teams')->insert([
            'name' => 'France',
            'abr' => 'fra',
            'group_id' => 3,
        ]);
        
        DB::table('teams')->insert([
            'name' => 'Australie',
            'abr' => 'aus',
            'group_id' => 3,
        ]);
        
        DB::table('teams')->insert([
            'name' => 'Pérou',
            'abr' => 'per',
            'group_id' => 3,
        ]);
        
        DB::table('teams')->insert([
            'name' => 'Danemark',
            'abr' => 'den',
            'group_id' => 3,
        ]);
        //D
        DB::table('teams')->insert([
            'name' => 'Argentine',
            'abr' => 'arg',
            'group_id' => 4,
        ]);
        
        DB::table('teams')->insert([
            'name' => 'Islande',
            'abr' => 'isl',
            'group_id' => 4,
        ]);
        
        DB::table('teams')->insert([
            'name' => 'Croatie',
            'abr' => 'cro',
            'group_id' => 4,
        ]);
        
        DB::table('teams')->insert([
            'name' => 'Nigeria',
            'abr' => 'nga',
            'group_id' => 4,
        ]);
        //E
        DB::table('teams')->insert([
            'name' => 'Brésil',
            'abr' => 'bra',
            'group_id' => 5,
        ]);
        
        DB::table('teams')->insert([
            'name' => 'Suisse',
            'abr' => 'sui',
            'group_id' => 5,
        ]);
        
        DB::table('teams')->insert([
            'name' => 'Costa Rica',
            'abr' => 'crc',
            'group_id' => 5,
        ]);
        
        DB::table('teams')->insert([
            'name' => 'Serbie',
            'abr' => 'srb',
            'group_id' => 5,
        ]);
        //F
        DB::table('teams')->insert([
            'name' => 'Allemagne',
            'abr' => 'ger',
            'group_id' => 6,
        ]);
        
        DB::table('teams')->insert([
            'name' => 'Mexique',
            'abr' => 'mex',
            'group_id' => 6,
        ]);
        
        DB::table('teams')->insert([
            'name' => 'Suède',
            'abr' => 'swe',
            'group_id' => 6,
        ]);
        
        DB::table('teams')->insert([
            'name' => 'République de Corée',
            'abr' => 'kor',
            'group_id' => 6,
        ]);
        //G
        DB::table('teams')->insert([
            'name' => 'Belgique',
            'abr' => 'bel',
            'group_id' => 7,
        ]);
        
        DB::table('teams')->insert([
            'name' => 'Panama',
            'abr' => 'pan',
            'group_id' => 7,
        ]);
        
        DB::table('teams')->insert([
            'name' => 'Tunisie',
            'abr' => 'tun',
            'group_id' => 7,
        ]);
        
        DB::table('teams')->insert([
            'name' => 'Angleterre',
            'abr' => 'eng',
            'group_id' => 7,
        ]);
        // H
        DB::table('teams')->insert([
            'name' => 'Pologne',
            'abr' => 'pol',
            'group_id' => 8,
        ]);
        DB::table('teams')->insert([
            'name' => 'Sénégal',
            'abr' => 'sen',
            'group_id' => 8,
        ]);
        DB::table('teams')->insert([
            'name' => 'Colombie',
            'abr' => 'col',
            'group_id' => 8,
        ]);
        DB::table('teams')->insert([
            'name' => 'Japon',
            'abr' => 'jpn',
            'group_id' => 8,
        ]);  
        //
        
       
    }
}
