<?php

namespace App\Http\Livewire\Components;

use Livewire\Component;
use Lunar\Models\CollectionGroup;

class ShopNavigation extends Component
{
    /**
     * The search term for the search input.
     *
     * @var string
     */
    public $term = null;

    public $shop_collections = null;

    /**
     * {@inheritDoc}
     */
    protected $queryString = [
        'term',
    ];

    public function mount()
    {
        $shop_collection_group = CollectionGroup::whereHandle('main')->with('collections')->first();
        $this->shop_collections = $shop_collection_group->collections->where('parent_id', null)->sortBy('_lft');
    }

    public function render()
    {
        return view('livewire.components.shop-navigation');
    }
}
