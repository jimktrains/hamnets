<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\Net;
use App\Models\NetLoggerLog;
use App\Models\NextNet;
use App\Models\Band;

class Welcome extends Controller
{
  public function index(Request $Request)
  {
    $timezone = $Request->session()->get("timezone", "America/New_York");
    $gridsquare = $Request->session()->get("gridsquare");
    $selectedBands = $Request->session()->get('bands');

    $hoursAhead = $Request->input('hours_ahead', $Request->session()->get("hours_ahead", 1));
    $Request->session()->put("hours_ahead", $hoursAhead);

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

    $Nets = Net::filterBand($selectedBands)
      ->orderInTz($timezone)
      ->get();

    $NextNets = NextNet::forTz($timezone)
      ->upcoming($hoursAhead)
      ->filterBand($selectedBands)
      ->orderby('start_timestamp');

    $NowNets = NextNet::forTz($timezone)
      ->ongoing()
      ->filterBand($selectedBands)
      ->orderby('primary_frequency');

    $CoverageNets = null;
    if (!empty($gridsquare)) {
      $CoverageNets = Net::whereGridSquare($gridsquare)
        ->filterBand($selectedBands)
        ->selectRaw("distinct on (net.net_id) net.*")
        ->orderBy('net.net_id')
        ->get();

      $NextNets = $NextNets->whereGridSquare($gridsquare);
      $NowNets = $NowNets->whereGridSquare($gridsquare);
    }
    $NextNets = $NextNets->get();
    $NowNets = $NowNets->get();

    $NetLoggerLogs = NetLoggerLog::current($timezone, $selectedBands)->get();

    if (!empty($NetLoggerLogs)) {
      $NowNets = $NowNets->concat($NetLoggerLogs)->sortBy('primary_frequency');
    }

    return view(
        'welcome',
        compact(
            'Nets',
            'NextNets',
            'NowNets',
            'CoverageNets',
            'hoursAhead',
            'gridsquare',
            'NetLoggerLogs',
        )
    );
  }
}
