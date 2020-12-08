<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;
use Illuminate\Support\Facades\DB;

class AddFrequencyUrlToNet extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
  public function up()
  {
    DB::connection()->getPdo()->exec("drop materialized view if exists nextnet");
    DB::connection()->getPdo()->exec("drop view if exists netwithband");

    Schema::table('net', function (Blueprint $table) {
        $table->text('primary_frequency_url')->nullable();
        $table->boolean('primary_frequency_is_repeater')->default(false);
        $table->text('secondary_frequency_url')->nullable();
        $table->boolean('secondary_frequency_is_repeater')->default(false);

        $table->text('mode')->nullable();

        $table->integer('arrl_net_id')->nullable();
        $table->text('arrl_state')->nullable();
        $table->text('arrl_area')->nullable();
        $table->text('arrl_region')->nullable();
        $table->text('arrl_section')->nullable();
        $table->text('arrl_coverage')->nullable();
        $table->text('arrl_traffic_handling')->nullable();
        $table->text('arrl_net_manager')->nullable();
        $table->text('arrl_net_manager_url')->nullable();
        $table->boolean('arrl_national_traffic_affiliated')->nullable();

        $table->text('primary_repeater_gridsquare')->nullable();
        $table->text('secondary_repeater_gridsquare')->nullable();
        $table->integer('primary_repeaterbook_state_id')->nullable();
        $table->integer('primary_repeaterbook_repeater_id')->nullable();
        $table->integer('secondary_repeaterbook_state_id')->nullable();
        $table->integer('secondary_repeaterbook_repeater_id')->nullable();


        $table->dropColumn('national_traffic_affiliated');
    });

    DB::connection()->getPdo()->exec("create extension if not exists postgis");
    DB::connection()->getPdo()->exec("create table gadm36 (gid serial primary key,
gid_0 text,
name_0 text,
gid_1 text,
name_1 text,
varname_1 text,
nl_name_1 text,
type_1 text,
engtype_1 text,
cc_1 text,
hasc_1 varchar(80));
");
    DB::connection()->getPdo()->exec("select addgeometrycolumn('','gadm36','geom','4326','multipolygon',2)");
    DB::connection()->getPdo()->exec("create index on gadm36 using gist(geom)");

    Schema::create('coverage', function (Blueprint $table) {
      $table->integer('net_id');
      $table->integer('gid');

      $table->foreign('gid')->references('gid')->on('gadm36');
      $table->foreign('net_id')->references('net_id')->on('net');
      $table->primary(['gid', 'net_id']);
    });

    DB::connection()->getPdo()->exec("drop view if exists netwithband");
    DB::connection()->getPdo()->exec("
create or replace function maidenhead2bbox(m text)
returns box2d as $$
declare lat double precision;
declare lon double precision;
declare latd double precision;
declare lond double precision;
begin
lat = (ascii(substring(m from 2 for 1)) - 65) * 10 + substring(m from 4 for 1)::integer + (ascii(substring(m from 6 for 1)) - 97) / 24.0 + 1 / 48.0 - 90;
lon = (ascii(substring(m from 1 for 1)) - 65) * 20 + substring(m from 3 for 1)::integer * 2 + (ascii(substring(m from 5 for 1)) - 97) / 12.0 + 1 / 24.0 - 180;

lond = -0.04166666666665000000;
latd = -0.02083333333335000000;


return ST_SetSRID(ST_MakeBox2D(ST_Point(lon + lond , lat + latd), ST_Point(lon - lond, lat - latd)), 4326);
end;
$$ language 'plpgsql'
immutable;
");

    DB::connection()->getPdo()->exec("
create or replace function maidenhead2centroid(m text)
returns geometry as $$
declare lat double precision;
declare lon double precision;
begin
lat = (ascii(substring(m from 2 for 1)) - 65) * 10 + substring(m from 4 for 1)::integer + (ascii(substring(m from 6 for 1)) - 97) / 24.0 + 1 / 48.0 - 90;
lon = (ascii(substring(m from 1 for 1)) - 65) * 20 + substring(m from 3 for 1)::integer * 2 + (ascii(substring(m from 5 for 1)) - 97) / 12.0 + 1 / 24.0 - 180;

return ST_SetSRID(ST_Point(lon, lat), 4326);
end;
$$ language 'plpgsql'
immutable;
");


    DB::connection()->getPdo()->exec("
create view netwithband as
select net.*,
       b.name as band
from net
join band b on primary_frequency <@ frequencies
");

    DB::connection()->getPdo()->exec("drop materialized view if exists nextnet");
    DB::connection()->getPdo()->exec("
create materialized view nextnet as
select net_id,
       name,
       band,
       url,
       primary_frequency,
       primary_frequency_is_repeater,
       secondary_frequency,
       secondary_frequency_is_repeater,

       primary_repeater_gridsquare,
       secondary_repeater_gridsquare,
       primary_repeaterbook_state_id,
       primary_repeaterbook_repeater_id,
       secondary_repeaterbook_state_id,
       secondary_repeaterbook_repeater_id,

       start_timestamp::time as start_time,
       end_timestamp::time as end_time,
       start_timestamp,
       end_timestamp,
       end_timestamp_is_estimated
from (
select net_id,
       name,
       band,
       url,
       primary_frequency,
       primary_frequency_is_repeater,
       secondary_frequency,
       secondary_frequency_is_repeater,

       primary_repeater_gridsquare,
       secondary_repeater_gridsquare,
       primary_repeaterbook_state_id,
       primary_repeaterbook_repeater_id,
       secondary_repeaterbook_state_id,
       secondary_repeaterbook_repeater_id,

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

    DB::connection()->getPdo()->exec("create index on net using gist ((st_buffer(maidenhead2centroid(primary_repeater_gridsquare), 1.0, 16)))");
    DB::connection()->getPdo()->exec("create index on net using gist ((st_buffer(maidenhead2centroid(secondary_repeater_gridsquare), 1.0, 16)))");
  }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
  public function down()
  {
    DB::connection()->getPdo()->exec("drop materialized view if exists nextnet");
    DB::connection()->getPdo()->exec("drop view if exists netwithband");

    Schema::table('net', function (Blueprint $table) {
        $table->dropColumn('primary_frequency_url');
        $table->dropColumn('primary_frequency_is_repeater');
        $table->dropColumn('secondary_frequency_url');
        $table->dropColumn('secondary_frequency_is_repeater');

        $table->dropColumn('mode');

        $table->dropColumn('arrl_net_id');
        $table->dropColumn('arrl_state');
        $table->dropColumn('arrl_area');
        $table->dropColumn('arrl_region');
        $table->dropColumn('arrl_section');
        $table->dropColumn('arrl_traffic_handling');
        $table->dropColumn('arrl_net_manager');
        $table->dropColumn('arrl_net_manager_url');
        $table->dropColumn('arrl_national_traffic_affiliated');

        $table->dropColumn('repeater_gridsquare');

        $table->dropColumn('repeaterbook_primary_url');
        $table->dropColumn('repeaterbook_secondary_url');

        $table->boolean('national_traffic_affiliated')->nullable();
    });

    DB::connection()->getPdo()->exec("drop table state_coverage");
    DB::connection()->getPdo()->exec("drop table gadm36;");


    DB::connection()->getPdo()->exec("create view netwithband as
select net.*,
       b.name as band
from net
join band b on primary_frequency <@ frequencies");

    DB::connection()->getPdo()->exec("create materialized view nextnet as
select net_id,
       name,
       band,
       url,
       primary_frequency,
       secondary_frequency,
       start_timestamp::time as start_time,
       end_timestamp::time as end_time,
       start_timestamp,
       end_timestamp,
       end_timestamp_is_estimated
from (
select net_id,
       name,
       band,
       url,
       primary_frequency,
       secondary_frequency,
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
}
