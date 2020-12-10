<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;

class GlobalPreferences
{
    /**
     * Handle an incoming request.
     *
     * @param  \Illuminate\Http\Request $request
     * @param  \Closure                 $next
     * @return mixed
     */
  public function handle(Request $Request, Closure $next)
  {
    $timezone = $Request->input("timezone", $Request->session()->get("timezone", "America/New_York"));
    $Request->session()->put('timezone', $timezone);

    $gridsquare = $Request->input("gridsquare", $Request->session()->get("gridsquare"));
    $Request->session()->put('gridsquare', $gridsquare);

    $selectedBands = $Request->input('bands', $Request->session()->get('bands', []));
    $selectedBands = array_filter($selectedBands, function ($x) {
        return $x !== '☃';
    });
    $Request->session()->put('bands', $selectedBands);

    return $next($Request);
  }
}
