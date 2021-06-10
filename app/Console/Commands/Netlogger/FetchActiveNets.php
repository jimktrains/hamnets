<?php

namespace App\Console\Commands\Netlogger;

use Illuminate\Console\Command;
use GuzzleHttp;
use DB;

class FetchActiveNets extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'netlogger:fetch-active-nets';

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
      // TODO: Handle errors. Maybe send me an email?
      $client = new GuzzleHttp\Client();
      $res = $client->request('GET', 'http://www.netlogger.org/api/GetActiveNets.php', ['http_errors' => true]);
      $body = $res->getBody();

      // $body = file_get_contents('GetActiveNets.php.xml');
      $NetLoggerXML = simplexml_load_string($body);

      if ($NetLoggerXML) {
        foreach ($NetLoggerXML->ServerList->Server as $Server) {
          foreach ($Server->Net as $Net) {
            $row = [
              "created_at"      => DB::raw('now()'),
              "updated_at"      => DB::raw('now()'),
              "NetName"         => (string)$Net->NetName,
              "AltNetName"      => (string)$Net->AltNetName,
              "Logger"          => (string)$Net->Logger,
              "Frequency_raw"   => (string)$Net->Frequency,
              "NetControl"      => (string)$Net->NetControl,
              "Date"            => (string)$Net->Date . "Z",
              "Mode"            => (string)$Net->Mode,
              "Band"            => (string)$Net->Band,
              "SubscriberCount" => (int)$Net->SubscriberCount,
            ];

            $freq = null;
            if (preg_match('/^(\d+(.\d+)?)\s*[kK][hH][Zz]$/', $row['Frequency_raw'], $matches)) {
              $freq = floatval($matches[1]) * 1000;
            } elseif (preg_match('/^(\d+(.\d+)?)\s*[mM][hH][Zz]$/', $row['Frequency_raw'], $matches)) {
              $freq = floatval($matches[1]) * 1000000;
            } elseif (preg_match('/^(\d+\.\d+)$/', $row['Frequency_raw'], $matches)) {
              $freq = floatval($matches[1]) * 1000000;
            } elseif (preg_match('/^(\d+\.\d+)\.(\d+)$/', $row['Frequency_raw'], $matches)) {
              $l = strlen($matches[2]);
              $freq = (floatval($matches[1]) * 1000000) + (floatval($matches[2]) * pow(10, 3-$l));
            }
            $row['Frequency'] = $freq;
            DB::table('netlogger_log')->insert($row);
          }
        }
      }
        return 0;
    }
}
