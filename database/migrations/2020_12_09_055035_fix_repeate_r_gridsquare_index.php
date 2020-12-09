<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;
use Illuminate\Support\Facades\DB;

class FixRepeateRGridsquareIndex extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {

    DB::connection()->getPdo()->exec("drop index net_st_buffer_idx");
    DB::connection()->getPdo()->exec("drop index net_st_buffer_idx1");
    DB::connection()->getPdo()->exec("create index net_p_r_gs_idx on net using gist (st_transform(st_buffer(maidenhead2centroid(primary_repeater_gridsquare), 0.75, 16), 3857))");
    DB::connection()->getPdo()->exec("create index net_s_r_gs_idx on net using gist (st_transform(st_buffer(maidenhead2centroid(secondary_repeater_gridsquare), 0.75, 16), 3857))");
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {

      DB::connection()->getPdo()->exec("drop index net_p_r_gs_idx");
      DB::connection()->getPdo()->exec("drop index net_s_r_gs_idx");
      DB::connection()->getPdo()->exec("create index net_st_buffer_idx on net using gist ((st_buffer(maidenhead2centroid(primary_repeater_gridsquare), 1.0, 16)))");
      DB::connection()->getPdo()->exec("create index net_st_buffer_idx1 on net using gist ((st_buffer(maidenhead2centroid(secondary_repeater_gridsquare), 1.0, 16)))");
    }
}
