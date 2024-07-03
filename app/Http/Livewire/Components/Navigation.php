<?php

namespace App\Http\Livewire\Components;

use Livewire\Component;

class Navigation extends Component
{
    /**
     * The search term for the search input.
     *
     * @var string
     */
    public $term = null;

    /**
     * {@inheritDoc}
     */
    protected $queryString = [
        'term',
    ];

    public function render()
    {
        return view('livewire.components.navigation');
    }
}
