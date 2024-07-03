<?php

namespace App\Http\Livewire;

use Livewire\Component;

class ShipmentAndReturnsPage extends Component
{
    public function render()
    {
        return view('livewire.shop.shipments_and_returns')
            ->layout('layouts.storefront');
    }
}
