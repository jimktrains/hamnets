<?php

use Illuminate\Support\Facades\Route;
use Illuminate\Http\Request;
use App\Models\Net;
use App\Models\NextNet;
use App\Models\Band;

/*
 |--------------------------------------------------------------------------
 | Web Routes
 |--------------------------------------------------------------------------
 |
 | Here is where you can register web routes for your application. These
 | routes are loaded by the RouteServiceProvider within a group which
 | contains the "web" middleware group. Now create something great!
 |
 */
Route::get('/csv', function (Request $Request) {
  $buffer=fopen("php://memory", "rw");

  $fields = [];
  foreach (Net::all() as $Net) {
    $a = $Net->toArray();
    if (empty($fields)) {
      $fields = array_keys($a);
      fputcsv($buffer, $fields);
    }
    fputcsv($buffer, $a);
  }
  fseek($buffer, 0);

  header('Content-Type: text/plain');
  header('Content-Disposition: attachment; filename="ham-nets-'.date('Y-m-d-H-i').'.csv"');

  while (!feof($buffer)) {
    print(fread($buffer, 8192));
  }
});

Route::get('/', function (Request $Request) {
  $timezone = $Request->input("timezone", $Request->session()->get("timezone", "America/New_York"));
  $Request->session()->put("timezone", $timezone);

  $hoursAhead = $Request->input('hours_ahead', $Request->session()->get("hours_ahead", 1));
  $Request->session()->put("hours_ahead", $hoursAhead);

  $selectedBands = $Request->input('bands', $Request->session()->get('bands', []));
  $Request->session()->put('bands', $selectedBands);

  $timezones = [
      'UTC',
      'America/New_York',
      'America/Chicago',
      'America/Denver',
      'America/Phoenix',
      'America/Los_Angeles',
      'America/Nome',
      'Pacific/Honolulu',
  ];

  $licenseClasses = [
      "Any",
      "US Novice",
      "US Technician",
      "US General",
      "US Advanced",
      "US Extra",
  ];
  $licenseClass = $Request->input('license_class', 'Any');

  $bands = Band::havingNets()->get()->pluck('name');


  $filterBand = function ($query, $selectedBands) {
    return $query->whereIn('band', $selectedBands);
  };

  $Nets = Net::when($selectedBands, $filterBand)
    ->orderInTz($timezone)
    ->get();

  $NextNets = NextNet::forTz($timezone)
    ->upcoming($hoursAhead)
    ->when($selectedBands, $filterBand)
    ->orderby('start_timestamp')
    ->get();

  $NowNets = NextNet::forTz($timezone)
    ->ongoing()
    ->when($selectedBands, $filterBand)
    ->orderby('primary_frequency')
    ->get();

  return view(
      'welcome',
      compact(
          'Nets',
          'NextNets',
          'NowNets',
          'timezone',
          'timezones',
          'hoursAhead',
          'licenseClasses',
          'licenseClass',
          'bands',
          'selectedBands'
      )
  );
});
