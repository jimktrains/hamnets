<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;
use Illuminate\Support\Facades\DB;

class CreateBandTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
      DB::connection()->getPdo()->exec("create table band (
        band_id serial not null primary key,
        frequencies int4range not null,
        name text not null,
        country text not null
      )");

        DB::connection()->getPdo()->exec("create view netwithband as select net.*, b.name as band from net join band b on primary_frequency <@ frequencies");
        DB::connection()->getPdo()->exec("drop materialized view if exists nextnet");
        DB::connection()->getPdo()->exec("create materialized view nextnet as
select net_id,
       name,
       band,
       primary_frequency,
       start_timestamp::time as start_time,
       end_timestamp::time as end_time,
       start_timestamp,
       end_timestamp,
       end_timestamp_is_estimated
from (
  select net_id,
         name,
         band,
         primary_frequency,
         (start_time + d) at time zone timezone as start_timestamp,
         coalesce(end_time + d + case when start_time > end_time then interval '1 day' else interval '0 seconds' end, start_time + interval '1 hour' + d) at time zone timezone as end_timestamp,
         end_time is null as end_timestamp_is_estimated
  from netwithband
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
        Schema::dropIfExists('band');
    }
}
