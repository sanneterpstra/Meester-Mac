<?php

namespace App\Http\Livewire\Components;

use App\Http\Livewire\Components\AdviceAppointmentForm\Completed;
use App\Http\Livewire\Components\AdviceAppointmentForm\ContactInfo;
use App\Http\Livewire\Components\AdviceAppointmentForm\DateTimeStep;
use Spatie\LivewireWizard\Components\WizardComponent;

class AdviceAppointmentWizardComponent extends WizardComponent
{
    public function steps(): array
    {
        return [
            DateTimeStep::class,
            ContactInfo::class,
            Completed::class,
        ];
    }
}
