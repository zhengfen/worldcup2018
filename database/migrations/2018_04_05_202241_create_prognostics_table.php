<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class CreatePrognosticsTable extends Migration
{
    /**
     * Run the migrations.
     * 
     * @return void
     */
    public function up()
    {
        Schema::create('prognostics', function (Blueprint $table) {
            $table->increments('id');
            $table->unsignedSmallInteger('user_id');
            $table->unsignedSmallInteger('match_id');
            $table->unsignedSmallInteger('team_h')->nullable();
            $table->unsignedSmallInteger('team_a')->nullable();
            $table->unsignedSmallInteger('score_h')->nullable();
            $table->unsignedSmallInteger('score_a')->nullable();
            $table->unsignedSmallInteger('pen_h')->nullable();
            $table->unsignedSmallInteger('pen_a')->nullable();
            $table->timestamps();
            $table->unique(['user_id', 'match_id']);
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::dropIfExists('prognostics');
    }
}
