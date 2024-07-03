<?php

namespace App\Listeners;

use App\Mail\AdviceAppointmentCreated;
use App\Mail\ContactFormSubmitted;
use App\Mail\HelpAppointmentCreated;
use Mail;

class AdminNotificationSubscriber
{
    public function onContactFormSubmitted($event)
    {
        Mail::to(config('app.admin_emailaddress'))
            ->send(new ContactFormSubmitted($event->formData['fields']));
    }

    public function onHelpAppointmentCreated($event)
    {
        Mail::to(config('app.admin_emailaddress'))
            ->send(new HelpAppointmentCreated($event->formData));
    }

    public function onAdviceAppointmentCreated($event)
    {
        Mail::to(config('app.admin_emailaddress'))
            ->send(new AdviceAppointmentCreated($event->formData));
    }

    public function subscribe($events)
    {
        $events->listen(
            'App\Events\ContactFormSubmitted',
            'App\Listeners\AdminNotificationSubscriber@onContactFormSubmitted'
        );

        $events->listen(
            'App\Events\HelpAppointmentCreated',
            'App\Listeners\AdminNotificationSubscriber@onHelpAppointmentCreated'
        );

        $events->listen(
            'App\Events\AdviceAppointmentCreated',
            'App\Listeners\AdminNotificationSubscriber@onAdviceAppointmentCreated'
        );
    }
}
