<?php

namespace App\Http\Livewire\Components\HelpAppointmentForm;

use App\Events\HelpAppointmentCreated;
use Carbon\Carbon;
use Google\Service\Calendar;
use Google_Client;
use Illuminate\Support\Facades\Http;
use Spatie\LivewireWizard\Components\StepComponent;
use Vdhicts\ValidationRules\Rules\DutchPhone;
use Vdhicts\ValidationRules\Rules\DutchPostalCode;

class ContactInfo extends StepComponent
{
    public $fields = [
        'first_name' => '',
        'last_name' => '',
        'email' => '',
        'phone' => '',
        'postalcode' => '',
        'housenumber' => '',
        'address' => '',
        'city' => '',
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
            'fields.postalcode.required' => 'Vul je postcode in',
            'fields.postalcode' => 'Vul een geldige postcode in',
            'fields.housenumber.required' => 'Vul je huisnummer in',
            'fields.address.required' => 'Vul je adres in',
            'fields.city.required' => 'Vul je plaatsnaam in',
        ];
    }

    public function rules()
    {
        return [
            'fields.first_name' => 'required',
            'fields.last_name' => 'required',
            'fields.email' => 'required|email:rfc,dns',
            'fields.phone' => ['required', new DutchPhone],
            'fields.postalcode' => ['required', new DutchPostalCode],
            'fields.housenumber' => 'required',
            'fields.address' => 'required',
            'fields.city' => 'required',
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

        $date = Carbon::createFromFormat('d/m/Y H:i', $steps['date-time-step']['fields']['date'].' '.$steps['date-time-step']['fields']['time']);

        $event = new Calendar\Event([
            'summary' => 'Hulp aan huis: '.$this->fields['first_name'].' '.$this->fields['last_name'],
            'location' => $this->fields['address'].' '.$this->fields['housenumber'].' '.$this->fields['postalcode'].' '.$this->fields['city'],
            'description' => $steps['problem-description-step']['fields']['problem'],
            'start' => [
                'dateTime' => date('c', strtotime($date)),
                'timeZone' => 'Europe/Amsterdam',
            ],
            'end' => [
                'dateTime' => date('c', strtotime($date->addHours(3))),
                'timeZone' => 'Europe/Amsterdam',
            ],
        ]);

        $event = $service->events->insert('help@meestermac.nl', $event);

        HelpAppointmentCreated::dispatch($steps);

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

        if ($this->fields['postalcode'] && $this->fields['housenumber'] && $validated) {
            $this->fields['postalcode'] = strtoupper(str_replace(' ', '', $this->fields['postalcode']));
            $this->getAddress();
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
            'number' => $this->fields['housenumber'],
        ]);
        if ($response->status() == 200) {
            $response = $response->json();
            $this->fields['address'] = $response['street'];
            $this->fields['city'] = $response['city'];
        }
    }

    public function render()
    {
        return view('livewire.components.help-appointment-form.contact-info');
    }
}
