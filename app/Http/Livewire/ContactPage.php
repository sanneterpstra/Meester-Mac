<?php

namespace App\Http\Livewire;

use App\Events\ContactFormSubmitted;
use Livewire\Component;
use Spatie\Honeypot\Http\Livewire\Concerns\HoneypotData;
use Spatie\Honeypot\Http\Livewire\Concerns\UsesSpamProtection;
use Vdhicts\ValidationRules\Rules\DutchPhone;

class ContactPage extends Component
{
    use UsesSpamProtection;

    public HoneypotData $extraFields;

    public $fields = [
        'first_name' => '',
        'last_name' => '',
        'email' => '',
        'phone' => '',
        'message' => '',
    ];

    public $showSuccessMessage = false;

    public function messages()
    {
        return [
            'fields.first_name.required' => 'Vul je voornaam in',
            'fields.last_name.required' => 'Vul je achternaam in',
            'fields.email.required' => 'Vul je e-mailadres in',
            'fields.email.email' => 'Vul een geldig e-mailadres in.',
            'fields.phone.required' => 'Vul je telefoonnummer in',
            'fields.phone' => 'Geen geldig telefoonnummer',
            'fields.message.required' => 'Vul het bericht in',
        ];
    }

    public function rules()
    {
        return [
            'fields.first_name' => 'required',
            'fields.last_name' => 'required',
            'fields.email' => 'required|email:rfc,dns',
            'fields.phone' => [new DutchPhone],
            'fields.message' => 'required',
        ];
    }

    public function mount()
    {
        $this->extraFields = new HoneypotData();
    }

    public function updated($propertyName)
    {
        $this->validateOnly($propertyName);

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

    public function submit(): void
    {
        $this->protectAgainstSpam();

        $formData = $this->validate();

        ContactFormSubmitted::dispatch($formData);

        $this->showSuccessMessage = true;
        $this->dispatchBrowserEvent('success', ['true']);
        $this->fields = [
            'first_name' => '',
            'last_name' => '',
            'email' => '',
            'phone' => '',
            'message' => '',
        ];
    }

    public function render()
    {
        return view('livewire.site.contact-page');
    }
}
