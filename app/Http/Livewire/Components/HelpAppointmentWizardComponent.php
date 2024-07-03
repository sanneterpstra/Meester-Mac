<?php

namespace App\Http\Livewire\Components;

use App\Http\Livewire\Components\HelpAppointmentForm\Completed;
use App\Http\Livewire\Components\HelpAppointmentForm\ContactInfo;
use App\Http\Livewire\Components\HelpAppointmentForm\DateTimeStep;
use App\Http\Livewire\Components\HelpAppointmentForm\ProblemDescriptionStep;
use Spatie\LivewireWizard\Components\WizardComponent;

class HelpAppointmentWizardComponent extends WizardComponent
{
    public function steps(): array
    {
        return [
            DateTimeStep::class,
            ProblemDescriptionStep::class,
            ContactInfo::class,
            Completed::class,
        ];
    }
}
