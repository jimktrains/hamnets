<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;

class UsBands extends Seeder
{
    /**
     * Run the database seeds.
     *
     * @return void
     */
    public function run()
    {
      $bands = [
        ["160m",    1800000,   2000000],
        [ "80m",    3500000,   4000000],
        [ "60m",    5331000,   5406000],
        [ "40m",    7000000,   7300000],
        [ "30m",   10100000,  10150000],
        [ "20m",   14000000,  14350000],
        [ "17m",   18068000,  18168000],
        [ "15m",   21000000,  21450000],
        [ "12m",   24890000,  24990000],
        [ "10m",   28000000,  29700000],
        [  "6m",   50000000,  54000000],
        [  "2m",  144000000, 148000000],
        [ "70cm", 420000000, 450000000],
      ];

      foreach ($bands as $band)
      {
        DB::table('band')->insert([
          'frequencies' => "[{$band[1]}, {$band[2]}]",
          'name' => $band[0],
          'country' => 'US',
        ]);
      }
    }
}
