<?php

use Illuminate\Database\Migrations\Migration;

class ChangeBandToBigint extends Migration
{
  /**
   * Run the migrations.
   *
   * @return void
   */
  public function up()
  {
    DB::connection()->getPdo()->exec("alter table band alter frequencies type int8range using int8range(lower(frequencies), upper(frequencies))");
  }

  /**
   * Reverse the migrations.
   *
   * @return void
   */
  public function down()
  {
    DB::connection()->getPdo()->exec("alter table band alter frequencies type int4range using int4range(lower(frequencies)::int, upper(frequencies)::int)");
  }
}
