<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;
use Illuminate\Support\Facades\DB;

class IndexTsvectorNetName extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
      DB::connection()->getPdo()->exec("alter table net add slug text generated always as (lower(regexp_replace(name, '[^a-zA-Z0-9]+', '-', 'g'))) stored");
      DB::connection()->getPdo()->exec("alter table net add name_tsvector tsvector generated always as (to_tsvector('english', regexp_replace(name, '[^a-zA-Z0-9]+', ' ', 'g'))) stored");
      DB::connection()->getPdo()->exec("create index net_name_tsvector_idx on net using gin(name_tsvector)");
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
      Schema::table('net', function(Blueprint $table) {
        $table->dropIndex('net_name_tsvector_idx');
        $table->dropColumn('name_tsvector');
        $table->dropColumn('slug');
      });
    }
}
