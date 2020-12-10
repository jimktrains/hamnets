<?php

namespace App\View\Components;

use Illuminate\View\Component;
use Illuminate\Http\Request;

class GridsquareFilter extends Component
{
    public $gridsquare;

    /**
     * Create a new component instance.
     *
     * @return void
     */
    public function __construct(Request $Request)
    {
      $this->gridsquare = $Request->session()->get('gridsquare');
    }

    /**
     * Get the view / contents that represent the component.
     *
     * @return \Illuminate\Contracts\View\View|string
     */
    public function render()
    {
        return view('components.gridsquare-filter');
    }
}
