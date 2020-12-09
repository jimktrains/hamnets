<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;
use Illuminate\Support\Facades\DB;

class AddPgTrgmToNet extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
      DB::connection()->getPdo()->exec("create extension pg_trgm");
      DB::connection()->getPdo()->exec("create index net_name_trgm_idx on net using gist (name gist_trgm_ops)");
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
      DB::connection()->getPdo()->exec("drop index net_name_trgm_idx");
      DB::connection()->getPdo()->exec("drop extension pg_trgm");
    }
}
