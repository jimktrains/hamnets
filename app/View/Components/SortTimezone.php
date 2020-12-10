<?php

namespace App\View\Components;

use Illuminate\View\Component;
use Illuminate\Http\Request;

class SortTimezone extends Component
{
  public $timezones;
  public $selectedTz;

    /**
     * Create a new component instance.
     *
     * @return void
     */
  public function __construct(Request $Request)
  {
    $this->selectedTz = $Request->session()->get('timezone', 'America/New_York');
    $this->timezones = [
               'UTC',
               'America/New_York',
               'America/Chicago',
               'America/Denver',
               'America/Phoenix',
               'America/Los_Angeles',
               'America/Nome',
               'Pacific/Honolulu',
           ];
  }

    /**
     * Get the view / contents that represent the component.
     *
     * @return \Illuminate\Contracts\View\View|string
     */
  public function render()
  {
    return view('components.sort-timezone');
  }
}
