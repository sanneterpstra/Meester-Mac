<?php

namespace App\Http\Livewire\Components\HelpAppointmentForm;

use Carbon\Carbon;
use Exception;
use Google\Client;
use Google\Service\Calendar;
use Illuminate\Support\Facades\Log;
use Illuminate\View\View;
use Spatie\LivewireWizard\Components\StepComponent;

class DateTimeStep extends StepComponent
{
    public $fields = [
        'date' => '',
        'time' => '',
    ];

    public $datesAvailable = false;

    public $unavailableDates = [];

    public function messages(): array
    {
        return [
            'fields.date.required' => 'Kies een datum',
            'fields.time.required' => 'Kies een tijd',
        ];
    }

    public function rules(): array
    {
        return [
            'fields.date' => 'required',
            'fields.time' => 'required',
        ];
    }

    public function updated($propertyName): void
    {
        $this->validateOnly($propertyName);
    }

    public function submit(): void
    {
        $this->validate();

        $this->nextStep();
    }

    public function stepInfo(): array
    {
        return [
            'label' => 'Datum',
        ];
    }

    public function loadUnavailabledates(): void
    {
        $this->unavailableDates = $this->getUnavailableDates();
        $this->datesAvailable = true;
    }

    public function getUnavailableDates()
    {
        try {
            $client = new Client();
            $client->useApplicationDefaultCredentials();
            $client->setApplicationName('Lunarshop');
            $client->authorize();
            $scopes = [Calendar::CALENDAR];
            $client->setScopes($scopes);

            $service = new Calendar($client);
            $optParams = [
                'singleEvents' => true,
                'timeMin' => date('c'),
                'timeMax' => date('c', strtotime('+30 days')),
            ];
            $client->setSubject('help@meestermac.nl');
            $events = $service->events->listEvents('help@meestermac.nl', $optParams);

            $eventsDates = [];
            foreach ($events->getItems() as $event) {
                $eventsDates[] = date('d/m/Y', strtotime($event->getStart()['dateTime']));
            }

            $datesDisabled = collect($eventsDates);
            $startDateTime = Carbon::now(); // Today
            $datesDisabled->push($startDateTime->format('d/m/Y')); // Add today to unavailable dates

            return $datesDisabled;

        } catch (Exception $e) {
            Log::critical('Agenda kon niet worden geladen bij Hulp formulier. Foutmelding: '.$e->getMessage());
        }
    }

    public function render(): View
    {
        return view('livewire.components.help-appointment-form.date-time-step', [
            'unavailableDates' => $this->datesAvailable ? $this->unavailableDates : [],
        ]);
    }
}
