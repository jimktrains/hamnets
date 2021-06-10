<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateNetloggerCheckins extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('netlogger_checkins', function (Blueprint $table) {
            $table->id();
            $table->timestamps();

            // My intent here isn't to track the people on the net, but
            // instead to be able to generate a coverage map for the net
            // at a later date.
            $table->integer('netlogger_log_id')
                  ->references('netlogger_log_id')
                  ->on('netlogger_log');
            $table->integer('SerialNo')->nullable();
            $table->text('State')->nullable();
            $table->text('CityCountry')->nullable();
            $table->text('County')->nullable();
            $table->text('Zip')->nullable();
            $table->text('Grid')->nullable();
            $table->text('Country')->nullable();
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::dropIfExists('netlogger_checkins');
    }
}
