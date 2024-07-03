<?php

namespace App\Http\Livewire\Components\HelpAppointmentForm;

use Spatie\LivewireWizard\Components\StepComponent;

class Completed extends StepComponent
{
    public function render()
    {
        return view('livewire.components.help-appointment-form.completed');
    }
}
