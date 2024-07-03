<?php

namespace App\Listeners;

use App\Mail\AdviceAppointmentConfirmation;
use App\Mail\ContactFormConfirmation;
use App\Mail\HelpAppointmentConfirmation;
use Mail;

class ClientNotificationSubscriber
{
    public function onContactFormConfirmation($event)
    {
        Mail::to($event->formData['fields']['email'])
            ->send(new ContactFormConfirmation($event->formData['fields']));
    }

    public function onHelpAppointmentConfirmation($event)
    {
        Mail::to($event->formData['contact-info']['fields']['email'])
            ->send(new HelpAppointmentConfirmation($event->formData));
    }

    public function onAdviceAppointmentConfirmation($event)
    {
        Mail::to($event->formData['advice-appointment-contact-info']['fields']['email'])
            ->send(new AdviceAppointmentConfirmation($event->formData));
    }

    public function subscribe($events)
    {
        $events->listen(
            'App\Events\ContactFormSubmitted',
            'App\Listeners\ClientNotificationSubscriber@onContactFormConfirmation'
        );

        $events->listen(
            'App\Events\HelpAppointmentCreated',
            'App\Listeners\ClientNotificationSubscriber@onHelpAppointmentConfirmation'
        );

        $events->listen(
            'App\Events\AdviceAppointmentCreated',
            'App\Listeners\ClientNotificationSubscriber@onAdviceAppointmentConfirmation'
        );
    }
}
