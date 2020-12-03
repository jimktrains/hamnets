<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;
use Illuminate\Support\Facades\DB;

class CreateNetTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('net', function (Blueprint $table) {
            $table->id("net_id");
            $table->timestamps();

            $table->text("name");
            $table->text("description")->nullable();
            $table->text("url")->nullable();
            $table->integer("primary_frequency");
            $table->integer("secondary_frequency")->nullable();
            $table->text("mode");
            $table->time("start_time");
            $table->time("end_time")->nullable();
            $table->text("timezone");
            $table->text("recurrence_rule");
            $table->boolean("active");

            $table->boolean("national_traffic_affiliated");

            $table->boolean("sunday");
            $table->boolean("monday");
            $table->boolean("tuesday");
            $table->boolean("wednesday");
            $table->boolean("thursday");
            $table->boolean("friday");
            $table->boolean("saturday");
        });
        DB::connection()->getPdo()->exec("drop materialized view if exists nextnet");
        DB::connection()->getPdo()->exec("create materialized view nextnet as
select net_id,
       name,
       primary_frequency,
       start_timestamp::time as start_time,
       end_timestamp::time as end_time,
       start_timestamp,
       end_timestamp,
       end_timestamp_is_estimated
from (
  select net_id,
         name,
         primary_frequency,
         (start_time + d) at time zone timezone as start_timestamp,
         (coalesce(end_time, start_time + interval '1 hour') + d) at time zone timezone as end_timestamp,
         end_time is null as end_timestamp_is_estimated
  from net
  cross join
    (select current_date + make_interval(days := i) as d
     from generate_series(0, 6) as s(i))x
  where
    (sunday and extract(dow from d) = 0) or
    (monday and extract(dow from d) = 1) or
    (tuesday and extract(dow from d) = 2) or
    (wednesday and extract(dow from d) = 3) or
    (thursday and extract(dow from d) = 4) or
    (friday and extract(dow from d) = 5) or
    (saturday and extract(dow from d) = 6)
)x
");
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        DB::connection()->getPdo()->exec("drop materialized view if exists nextnet");
        Schema::dropIfExists('net');
    }
}
