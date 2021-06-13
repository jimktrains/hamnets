<?php

namespace App\Console\Commands\RepeaterBook;

use Illuminate\Console\Command;
use GuzzleHttp;
use GuzzleHttp\Cookie\CookieJar;
use Storage;

class GetCsv extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
  protected $signature = 'repeaterbook:get-csv {session-id-name} {session-id} {rb-email} {rb-uid} {rb-user}';

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
    $stateFipsCodes = [
    // Alabama   AL
        '01',
    // Alaska  AK
        '02',
    // Arizona   AZ
        '04',
    // Arkansas  AR
        '05',
    // California  CA
        '06',
    // Colorado  CO
        '08',
    // Connecticut   CT
        '09',
    // Delaware   DE
        '10',
    // Florida  FL
        '12',
    // Georgia  GA
        '13',
    // Hawaii   HI
        '15',
    // Idaho  ID
        '16',
    // Illinois   IL
        '17',
    // Indiana  IN
        '18',
    // Iowa   IA
        '19',
    // Kansas   KS
        '20',
    // Kentucky   KY
        '21',
    // Louisiana  LA
        '22',
    // Maine  ME
        '23',
    // Maryland   MD
        '24',
    // Massachusetts  MA
        '25',
    // Michigan   MI
        '26',
    // Minnesota  MN
        '27',
    // Mississippi  MS
        '28',
    // Missouri   MO
        '29',
    // Montana  MT
        '30',
    // Nebraska   NE
        '31',
    // Nevada   NV
        '32',
    // New Hampshire  NH
        '33',
    // New Jersey   NJ
        '34',
    // New Mexico   NM
        '35',
    // New York   NY
        '36',
    // North Carolina   NC
        '37',
    // North Dakota   ND
        '38',
    // Ohio   OH
        '39',
    // Oklahoma   OK
        '40',
    // Oregon   OR
        '41',
    // Pennsylvania   PA
        '42',
    // Rhode Island   RI
        '44',
    // South Carolina   SC
        '45',
    // South Dakota   SD
        '46',
    // Tennessee  TN
        '47',
    // Texas  TX
        '48',
    // Utah   UT
        '49',
    // Vermont  VT
        '50',
    // Virginia   VA
        '51',
    // Washington   WA
        '53',
    // West Virginia  WV
        '54',
    // Wisconsin  WI
        '55',
    // Wyoming  WY
        '56',
    // American Samoa   AS
        '60',
    // Guam   GU
        '66',
    // Northern Mariana Islands   MP
        '69',
    // Puerto Rico  PR
        '72',
    // Virgin Islands   VI
        '78',
    ];

    // I could probably figure out how to get this automated, but I'll
    // leave that for later.
    $jar = CookieJar::fromArray([
      $this->argument('session-id-name') => $this->argument('session-id'),
      'joomla_user_state'                => 'logged_in',
      'Repeaterbook_EMAIL'               => $this->argument('rb-email'),
      'Repeaterbook_UID'                 => $this->argument('rb-uid'),
      'Repeaterbook_User'                => $this->argument('rb-user'),
    ], '.repeaterbook.com');

    $client = new GuzzleHttp\Client();
    $filenamePrefix = 'repeaterbook/' . date('Y-m-d') . '/' . date('H:i:s') . '/' ;
    foreach ($stateFipsCodes as $stateFipsCode) {
      $this->info("Working on {$stateFipsCode}");
      $url = "https://www.repeaterbook.com/repeaters/downloads/csv/index_nets.php?func=excel&state_id={$stateFipsCode}&band=&freq=&band6=&loc=%&call=%&status_id=&features=&coverage=&use=%&county_id=%";

      $res = $client->request('GET', $url, [
        'http_errors' => true,
        'cookies'     => $jar,
        'headers'     => ['User-Agent' => 'hamnets.org'],
      ]);
      $body = $res->getBody();

      $filename = $filenamePrefix . $stateFipsCode . ".csv";
      Storage::put($filename, $body);

      // Be nice and don't hammer the server.
      sleep(1);
    }
    return 0;
  }
}
