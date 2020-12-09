<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\Net as NetModel;
use App\Models\Band;

class Net extends Controller
{
  public function tile(Request $Request, $net_id, $x, $y, $z)
  {
    $Net = NetModel::where('net_id', $net_id)->first();
    if (empty($Net)) {
      throw new ModelNotFoundException;
    }

    return $Net->getTile($x, $y, $z);
  }

  public function show(Request $Request, $net_id)
  {
    $Net = NetModel::where('net_id', $net_id)->first();
    if (empty($Net)) {
      throw new ModelNotFoundException;
    }

    return view(
        'net',
        compact('Net')
    );
  }
}
