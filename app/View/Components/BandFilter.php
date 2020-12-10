<?php

namespace App\View\Components;

use Illuminate\View\Component;
use App\Models\Band;
use Illuminate\Http\Request;

class BandFilter extends Component
{
    public $bands;
    public $selectedBands;
    /**
     * Create a new component instance.
     *
     * @return void
     */
    public function __construct(Request $Request)
    {
      $this->selectedBands = $Request->session()->get('bands', []);
      $this->bands = Band::havingNets()->get()->pluck('name');
    }

    /**
     * Get the view / contents that represent the component.
     *
     * @return \Illuminate\Contracts\View\View|string
     */
    public function render()
    {
        return view('components.band-filter');
    }
}
