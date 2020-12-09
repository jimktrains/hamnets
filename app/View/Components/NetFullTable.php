<?php

namespace App\View\Components;

use Illuminate\View\Component;
use App\Model\Net;

class NetFullTable extends Component
{

    /**
     * @var Collection[Net]
     */
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
        return view('components.net-full-table');
    }
}
