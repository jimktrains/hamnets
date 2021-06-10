<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class ChangeNetFrequencyToBigint extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::table('net', function (Blueprint $table) {
          $table->biginteger('primary_frequency')->nullable()->change();
          $table->biginteger('secondary_frequency')->nullable()->change();
        });

        Schema::table('netlogger_log', function (Blueprint $table) {
          $table->biginteger('Frequency')->nullable()->change();
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::table('netlogger_log', function (Blueprint $table) {
          $table->int('Frequency')->nullable()->change();
        });

        Schema::table('biginteger', function (Blueprint $table) {
          $table->int('primary_frequency')->nullable()->change();
          $table->int('secondary_frequency')->nullable()->change();
        });
    }
}
