<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;
use Illuminate\Support\Facades\DB;

class CreateNetloggerLog extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('netlogger_log', function (Blueprint $table) {
            $table->id("netlogger_log_id");
            $table->timestamps();

            $table->text("NetName")->nullable();
            $table->text("AltNetName")->nullable();
            $table->integer("Frequency")->nullable();
            $table->text("Frequency_raw")->nullable();
            $table->text("Logger")->nullable();
            $table->text("NetControl")->nullable();
            $table->timestamptz("Date")->nullable();
            $table->text("Mode")->nullable();
            $table->text("Band")->nullable();
            $table->integer("SubscriberCount")->nullable();
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::dropIfExists('netlogger_log');
    }
}
