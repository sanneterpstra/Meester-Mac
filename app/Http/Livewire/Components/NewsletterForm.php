<?php

namespace App\Http\Livewire\Components;

use Illuminate\Support\Facades\Log;
use Livewire\Component;
use Spatie\Honeypot\Http\Livewire\Concerns\HoneypotData;
use Spatie\Honeypot\Http\Livewire\Concerns\UsesSpamProtection;
use Spatie\MailcoachSdk\Exceptions\InvalidData;
use Spatie\MailcoachSdk\Facades\Mailcoach;

class NewsletterForm extends Component
{
    use UsesSpamProtection;

    public HoneypotData $extraFields;

    public $email = '';

    public $success = false;

    protected $rules = [
        'email' => 'required|email:rfc,dns,',
    ];

    protected $messages = [
        'email.required' => 'Je emailadres is verplicht.',
        'email.email' => 'Vul een geldig e-mailadres in',
    ];

    public function mount()
    {
        $this->extraFields = new HoneypotData();
    }

    public function submit()
    {
        $this->protectAgainstSpam();

        $this->validate();

        try {
            Mailcoach::createSubscriber(
                emailListUuid: '056bedc2-7ee7-4e0d-ba09-b2f7c41a6131',
                attributes: [
                    'email' => $this->email,
                    'tags' => ['Footer form'],
                ]
            );

            $this->success = true;

        } catch (InvalidData $exception) {
            Log::error('Could not subscribe customer to newsletter: '.$exception->getMessage());
            foreach ($exception->errors['errors'] as $errorType => $messages) {
                foreach ($messages as $message) {
                    $this->addError($errorType, $message);
                }
            }
        }
    }

    public function render()
    {
        return view('livewire.components.newsletter-form');
    }
}
