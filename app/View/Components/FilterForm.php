<?php

namespace App\View\Components;

use Closure;
use Illuminate\Contracts\View\View;
use Illuminate\View\Component;

class FilterForm extends Component
{
    /**
     * Create a new component instance.
     */
    public string $action;
    public bool $showCustomer;
    public function __construct(string $action = '', bool $showCustomer = true)
    {
        $this->action = $action;
        $this->showCustomer = $showCustomer;
    }

    /**
     * Get the view / contents that represent the component.
     */
    public function render(): View|Closure|string
    {
        return view('components.filter-form');
    }
}
