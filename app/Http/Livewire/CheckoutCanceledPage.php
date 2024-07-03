<?php

namespace App\Http\Livewire;

use Livewire\Component;

class CheckoutCanceledPage extends Component
{
    public function mount()
    {
        session()->flash('message', ['type' => 'error', 'message' => 'De betaling is geannuleerd. Probeer de betaling opnieuw uit te voeren.']);

        return redirect()->route('checkout.view');
    }
}
