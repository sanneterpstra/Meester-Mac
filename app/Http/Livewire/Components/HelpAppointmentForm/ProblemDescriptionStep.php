<?php

namespace App\Http\Livewire\Components\HelpAppointmentForm;

use Spatie\LivewireWizard\Components\StepComponent;

class ProblemDescriptionStep extends StepComponent
{
    public $fields = [
        'problem' => '',
    ];

    public function messages()
    {
        return [
            'fields.problem.required' => 'Beschrijf het probleem',
        ];
    }

    public function rules()
    {
        return [
            'fields.problem' => 'required',
        ];
    }

    public function updated($propertyName)
    {
        $this->validateOnly($propertyName);
    }

    public function submit()
    {
        $this->validate();

        $this->nextStep();
    }

    public function stepInfo(): array
    {
        return [
            'label' => 'Probleem',
        ];
    }

    public function render()
    {
        return view('livewire.components.help-appointment-form.problem-description-step');
    }
}
