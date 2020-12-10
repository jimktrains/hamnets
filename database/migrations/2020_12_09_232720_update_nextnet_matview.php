<?php

use Illuminate\Database\Migrations\Migration;

class UpdateNextnetMatview extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
  public function up()
  {


    DB::connection()->getPdo()->exec("drop materialized view nextnet");
    DB::connection()->getPdo()->exec("drop view netwithband");
    DB::connection()->getPdo()->exec("create materialized view netschedule as
      select net_id,
       (net.start_time::interval + x_1.d) at time zone timezone as start_timestamp,
coalesce(x_1.d + net.end_time + make_interval(days := (net.start_time > net.end_time)::int),
        (x_1.d + net.start_time) + '1 hour'::interval
      ) at time zone timezone as end_timestamp,
       net.end_time is null as end_timestamp_is_estimated
from net
cross join
  (select current_date + make_interval(days => s.i) as d
   from generate_series(0, 6) s(i)) x_1
where (net.sunday and date_part('dow', x_1.d) = 0)
   or (net.monday and date_part('dow', x_1.d) = 1)
   or (net.tuesday and date_part('dow', x_1.d) = 2)
   or (net.wednesday and date_part('dow', x_1.d) = 3)
   or (net.thursday and date_part('dow', x_1.d) = 4)
   or (net.friday and date_part('dow', x_1.d) = 5)
   or (net.saturday and date_part('dow', x_1.d) = 6)
");
  }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
  public function down()
  {
  }
}
