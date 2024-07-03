<?php

namespace App\Http\Livewire;

use Livewire\Component;

class TermsPage extends Component
{
    public function render()
    {
        return view('livewire.shop.terms')
            ->layout('layouts.storefront');
    }
}
