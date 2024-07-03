<?php

namespace App\Http\Livewire;

use Livewire\Component;

class PaymentMethods extends Component
{
    public function render()
    {
        return view('livewire.shop.payment_methods')
            ->layout('layouts.storefront');
    }
}
