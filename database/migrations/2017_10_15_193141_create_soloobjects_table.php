<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class CreateSoloobjectsTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('soloobjects', function (Blueprint $table) {
            $table->increments('id');
            $table->string('object_name');
            $table->integer('cost_hour')->nullable();
            $table->integer('sequence_time');
             $table->integer('break_time')->nullable();
            $table->integer('max_guests');
            $table->time('hour_start');
            $table->time('hour_end');
            $table->string('system');
            $table->string('day');
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::dropIfExists('soloobjects');
    }
}
