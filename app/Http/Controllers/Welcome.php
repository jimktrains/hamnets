<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\Net;
use App\Models\NextNet;
use App\Models\Band;

class Welcome extends Controller
{
  public function index(Request $Request)
  {
    $timezone = $Request->input("timezone", $Request->session()->get("timezone", "America/New_York"));
    $Request->session()->put("timezone", $timezone);

    $gridsquare = $Request->input("gridsquare", $Request->session()->get("gridsquare"));
    $Request->session()->put("gridsquare", $gridsquare);

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

    $CoverageNets = null;
    if (!empty($gridsquare)) {
      $CoverageNets = Net::whereGridSquare($gridsquare)
        ->get();
    }

    return view(
        'welcome',
        compact(
            'Nets',
            'NextNets',
            'NowNets',
            'CoverageNets',
            'gridsquare',
            'timezone',
            'timezones',
            'hoursAhead',
            'licenseClasses',
            'licenseClass',
            'bands',
            'selectedBands'
        )
    );
  }
}
