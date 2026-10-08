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
    public ?string $timerange;
    public ?string $fromDay;
    public ?string $fromMonth;
    public ?string $fromYear;
    public ?string $toDay;
    public ?string $toMonth;
    public ?string $toYear;
    public function __construct(string $action = '', bool $showCustomer = true, ?string $timerange = null, ?string $fromDay = null, ?string $fromMonth = null, ?string $fromYear = null, ?string $toDay = null, ?string $toMonth = null, ?string $toYear = null)
    {
        $this->action = $action;
        $this->showCustomer = $showCustomer;
        $this->timerange = $timerange;
        $this->fromDay = $fromDay;
        $this->fromMonth = $fromMonth;
        $this->fromYear = $fromYear;
        $this->toDay = $toDay;
        $this->toMonth = $toMonth;
        $this->toYear = $toYear;
    }

    /**
     * Get the view / contents that represent the component.
     */
    public function render(): View|Closure|string
    {
        return view('components.filter-form');
    }
}
