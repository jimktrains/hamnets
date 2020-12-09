<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\Net as NetModel;
use App\Models\Band;

class NetIndex extends Controller
{

  function index(Request $Request)
  {
    $timezone = $Request->input("timezone", $Request->session()->get("timezone", "America/New_York"));
    $Request->session()->put("timezone", $timezone);

    $gridsquare = $Request->input("gridsquare", $Request->session()->get("gridsquare"));
    $Request->session()->put("gridsquare", $gridsquare);

    $selectedBands = $Request->input('bands', $Request->session()->get('bands', []));
    if (in_array('all', $selectedBands)) {
      $selectedBands = [];
    }
    $Request->session()->put('bands', $selectedBands);

    $bands = Band::havingNets()->get()->pluck('name');
    $filterBand = function ($query, $selectedBands) {
      return $query->whereIn('band', $selectedBands);
    };

    $term = $Request->input('term');

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


    $Nets = NetModel::when($selectedBands, $filterBand);

    if (!empty($gridsquare)) {
      $Nets = $Nets->whereGridSquare($gridsquare);
    }

    if (!empty($term)) {
      $Nets = $Nets->searchName($term);
    } else {
      $Nets = $Nets->orderInTz($timezone);
    }

    $Nets = $Nets->get();

    return view(
        'netindex',
        compact(
            'Nets',
            'timezones',
            'timezone',
            'gridsquare',
            'bands',
            'selectedBands',
            'term'
        )
    );
  }

  public function csv(Request $Request)
  {
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
  }
}
