<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\Net as NetModel;
use App\Models\Band;

class NetIndex extends Controller
{

  function index(Request $Request)
  {
    $timezone = $Request->session()->get("timezone");
    $gridsquare = $Request->session()->get("gridsquare");
    $selectedBands = $Request->session()->get('bands');

    $term = $Request->input('term');

    $searchFreq = null;
    if (is_numeric(str_replace(' ', '', $term))) {
      $searchFreq = floatval(str_replace(' ', '', $term));
      if (log10($searchFreq) < 7) {
        $searchFreq *= pow(10, 7 - ceil(log10($searchFreq)));
      }

      $term = null;
    }

    $Nets = NetModel::filterBand($selectedBands)->searchFrequency($searchFreq);

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
