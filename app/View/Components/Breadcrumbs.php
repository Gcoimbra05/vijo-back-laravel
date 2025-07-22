<?php

namespace App\View\Components;

use Closure;
use Illuminate\Contracts\View\View;
use Illuminate\View\Component;

class Breadcrumbs extends Component
{
    public array $items;

    public function __construct(array $items = [])
    {
        $this->items = $items;
    }

    public function render()
    {
        return view('components.breadcrumbs');
    }
}
