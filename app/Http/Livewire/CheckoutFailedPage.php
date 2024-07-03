<?php

namespace App\Http\Livewire;

use Livewire\Component;

class CheckoutFailedPage extends Component
{
    public function mount()
    {
        session()->flash('message', ['type' => 'error', 'message' => 'De betaling is mislukt. Probeer de betaling opnieuw uit te voeren.']);

        return redirect()->route('checkout.view');
    }
}
