<?php

namespace App\Http\Livewire;

use Livewire\Component;

class CheckoutOpenPage extends Component
{
    public function mount()
    {
        session()->flash('message', ['type' => 'error', 'message' => 'De betaling staat nog open. Probeer de betaling opnieuw uit te voeren.']);

        return redirect()->route('checkout.view');
    }
}
