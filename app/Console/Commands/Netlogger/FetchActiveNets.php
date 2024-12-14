<?php

namespace App\Console\Commands\Netlogger;

use Illuminate\Console\Command;
use GuzzleHttp;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;

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
      $res = $client->request('GET', 'https://www.netlogger.org/api/GetActiveNets.php', [
        'https_errors' => true,
        'headers' => [
          'User-Agent' => 'hamnets.org',
        ],
      ]);
      $body = $res->getBody();

      // $body = file_get_contents('GetActiveNets.php.xml');
      $NetLoggerXML = simplexml_load_string($body);

      $err = (string) $NetLoggerXML->Error;
      if (!empty($err)) {
        $msg = "Error getting active nets: " . $err;
        Log::error($msg);
        $this->error($msg);
        $NetLoggerXML = null;
      }

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
            $netloggerLogId = DB::table('netlogger_log')->insertGetId($row, 'netlogger_log_id');

            $query = [
              'ServerName' => (string) $Server->ServerName,
              'NetName'    => (string) $Net->NetName,
            ];
            $client = new GuzzleHttp\Client();
            $res = $client->request('GET', 'https://www.netlogger.org/api/GetCheckins.php', [
              'query' => $query,
              'headers' => [
                'User-Agent' => 'hamnets.org',
              ],
              'https_errors' => true
            ]);
            $body = $res->getBody();
            $CheckIns = simplexml_load_string($body);
            $err = (string) $CheckIns->Error;
            if (!empty($err)) {
              $msg = "Error getting checkins: " . $err . " " . json_encode($query);
              Log::error($msg);
              $this->error($msg);
              continue;
            }
            foreach ($CheckIns->CheckinList->Checkin as $Checkin) {
              $row = [
                'created_at'       => DB::raw('now()'),
                'updated_at'       => DB::raw('now()'),
                'netlogger_log_id' => $netloggerLogId,
                'SerialNo'         => (int)$Checkin->SerialNo,
                'State'            => (string)$Checkin->State,
                'CityCountry'      => (string)$Checkin->CityCountry,
                'County'           => (string)$Checkin->County,
                'Zip'              => (string)$Checkin->Zip,
                'Grid'             => (string)$Checkin->Grid,
                'Country'          => (string)$Checkin->Country,
              ];
              DB::table('netlogger_checkins')->insert($row);
            }
          }
        }
      }
        return 0;
    }
}
