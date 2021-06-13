<?php

namespace App\Console\Commands\RepeaterBook;

use Illuminate\Console\Command;
use DB;

class ImportCSV extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'repeaterbook:import-csv {csv}';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Command description';

    /**
     * Create a new command instance.
     *
     * @return void
     */
    public function __construct()
    {
        parent::__construct();
    }

    /**
     * Execute the console command.
     *
     * @return int
     */
    public function handle()
    {
      DB::transaction(function () {
        $filename = $this->argument('csv');

        $handle = fopen($filename, 'r');
        $current_line = [];
        $line_num = -1;
        while (($fields = fgetcsv($handle, 1000, ",")) !== FALSE) {
          $line_num++;
          if ($line_num == 0) {
            continue;
          }

          if (empty($fields)) {
            continue;
          }

          $fields = array_map('trim', $fields);

          $field1 = floatval($fields[0]);
          if (empty($field1)) {
            $current_line = array_merge($current_line, $fields);
            continue;
          }

          if (!empty($current_line)) {
            $this->import($current_line);
          }

          $current_line = $fields;
        }
        $this->import($current_line);
      });
      DB::table('repeaterbook_raw')
        ->where('nets', '')
        ->update(['nets'=>null]);

        return 0;
    }

    public function import($fields)
    {
      # Since the nets field isn't quoted, we need to figure out all of
      # the fields, specifically that last_update field that's after the
      # nets field. So, we can extract everything we need based on relative
      # position, then just mush everything together for the nets.
      $data = [
        "state"          => array_shift($fields),
        "id"             => array_shift($fields),
        "output_freq"    => array_shift($fields),
        "input_freq"     => array_shift($fields),
        "offset"         => array_shift($fields),
        "uplink_tone"    => array_shift($fields),
        "downlink_tone"  => array_shift($fields),
        "location"       => array_shift($fields),
        "county"         => array_shift($fields),
        "lat"            => array_shift($fields),
        "long"           => array_shift($fields),
        "call"           => array_shift($fields),
        "use"            => array_shift($fields),
        "op_status"      => array_shift($fields),
        "mode"           => array_shift($fields),
        "digital_access" => array_shift($fields),
        "echolink"       => array_shift($fields),
        "irlp"           => array_shift($fields),
        "allstar"        => array_shift($fields),
        "coverage"       => array_shift($fields),
        "last_update"    => array_pop($fields),
      ];

      $data['nets'] = trim(implode(',', $fields));

      DB::table('repeaterbook_raw')->upsert($data, ['state', 'id']);
    }
}
