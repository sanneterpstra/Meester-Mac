<?php

namespace App\Http\Livewire\Components\AdviceAppointmentForm;

use App\Events\AdviceAppointmentCreated;
use Carbon\Carbon;
use Google\Service\Calendar;
use Google_Client;
use Illuminate\Support\Facades\Http;
use Spatie\LivewireWizard\Components\StepComponent;
use Vdhicts\ValidationRules\Rules\DutchPhone;

class ContactInfo extends StepComponent
{
    public $fields = [
        'first_name' => '',
        'last_name' => '',
        'email' => '',
        'phone' => '',
    ];

    public function messages()
    {
        return [
            'fields.first_name.required' => 'Vul je voornaam in',
            'fields.last_name.required' => 'Vul je achternaam in',
            'fields.email.required' => 'Vul je e-mailadres in',
            'fields.email.email' => 'Vul een geldig emailadres in',
            'fields.phone.required' => 'Vul je telefoonnummer in',
            'fields.phone' => 'Vul een geldig telefoonnummer in',
        ];
    }

    public function rules()
    {
        return [
            'fields.first_name' => 'required',
            'fields.last_name' => 'required',
            'fields.email' => 'required|email:rfc,dns',
            'fields.phone' => ['required', new DutchPhone],
        ];
    }

    public function submit()
    {
        $this->validate();

        $steps = $this->state()->all();

        $client = new Google_Client();
        $client->useApplicationDefaultCredentials();
        $client->setApplicationName('Lunarshop');
        $client->authorize();
        $scopes = [Calendar::CALENDAR];
        $client->setScopes($scopes);

        $service = new Calendar($client);
        $client->setSubject('help@meestermac.nl');

        $date = Carbon::createFromFormat('d/m/Y H:i', $steps['advice-appointment-date-time-step']['fields']['date'].' '.$steps['advice-appointment-date-time-step']['fields']['time']);

        $event = new Calendar\Event([
            'summary' => 'Adviesgesprek: '.$this->fields['first_name'].' '.$this->fields['last_name'],
            'description' => $steps['advice-appointment-date-time-step']['fields']['message'].'

Telefoonnummer: '.$this->fields['phone'],
            'start' => [
                'dateTime' => date('c', strtotime($date)),
                'timeZone' => 'Europe/Amsterdam',
            ],
            'end' => [
                'dateTime' => date('c', strtotime($date->addHours(1))),
                'timeZone' => 'Europe/Amsterdam',
            ],
        ]);

        $event = $service->events->insert('help@meestermac.nl', $event);

        AdviceAppointmentCreated::dispatch($steps);

        $this->nextStep();
    }

    public function stepInfo(): array
    {
        return [
            'label' => 'Contactgegevens',
        ];
    }

    public function updated($propertyName)
    {
        $validated = $this->validateOnly($propertyName);

        if ($propertyName === 'fields.first_name' || $propertyName === 'fields.last_name') {
            $this->fields['first_name'] = ucfirst($this->fields['first_name']);
            $this->fields['last_name'] = $this->titleCase($this->fields['last_name']);
        }
    }

    protected function titleCase($string, $delimiters = [' ', '-'], $exceptions = ['van', 'der', 'de', '‘t', 'ten', 'ter', 'den'])
    {
        /*
         * Exceptions in lower case are words you don't want converted
         * Exceptions all in upper case are any words you don't want converted to title case
         *   but should be converted to upper case, e.g.:
         *   king henry viii or king henry Viii should be King Henry VIII
         */
        $string = mb_convert_case($string, MB_CASE_TITLE, 'UTF-8');
        foreach ($delimiters as $dlnr => $delimiter) {
            $words = explode($delimiter, $string);
            $newwords = [];
            foreach ($words as $wordnr => $word) {
                if (in_array(mb_strtoupper($word, 'UTF-8'), $exceptions)) {
                    // check exceptions list for any words that should be in upper case
                    $word = mb_strtoupper($word, 'UTF-8');
                } elseif (in_array(mb_strtolower($word, 'UTF-8'), $exceptions)) {
                    // check exceptions list for any words that should be in upper case
                    $word = mb_strtolower($word, 'UTF-8');
                }
                array_push($newwords, $word);
            }
            $string = implode($delimiter, $newwords);
        }//foreach

        return $string;
    }

    protected function getAddress()
    {
        $response = Http::withHeaders([
            'token' => 'b178c681-c12b-493b-9b57-ff1fa6611061',
        ])->get('https://json.api-postcode.nl', [
            'postcode' => $this->fields['postalcode'],
            'number' => $this->fields['line_two'],
        ]);
        if ($response->status() == 200) {
            $response = $response->json();
            $this->fields['address'] = $response['street'];
            $this->fields['city'] = $response['city'];
        }
    }

    public function render()
    {
        return view('livewire.components.advice-appointment-form.contact-info');
    }
}
