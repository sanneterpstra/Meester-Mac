<?php

namespace App\Http\Livewire\Components\AdviceAppointmentForm;

use Spatie\LivewireWizard\Components\StepComponent;

class Completed extends StepComponent
{
    public function render()
    {
        return view('livewire.components.advice-appointment-form.completed');
    }
}
