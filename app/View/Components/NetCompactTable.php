<?php

namespace App\View\Components;

use Illuminate\View\Component;

class NetCompactTable extends Component
{
    public $Nets;
    /**
     * Create a new component instance.
     *
     * @return void
     */
    public function __construct($nets)
    {
        $this->Nets = $nets;
    }

    /**
     * Get the view / contents that represent the component.
     *
     * @return \Illuminate\Contracts\View\View|string
     */
    public function render()
    {
        return view('components.net-compact-table');
    }
}
