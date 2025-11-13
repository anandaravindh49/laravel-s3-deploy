<?php

namespace App\View\Components;

use Illuminate\View\Component;

class Sidebar extends Component
{
    /**
     * Create a new component instance.
     */
    public function __construct()
    {
        // add any data you'd like to pass to the component
    }

    /**
     * Get the view / contents that represent the component.
     */
    public function render()
    {
        return view('components.sidebar');
    }
}
