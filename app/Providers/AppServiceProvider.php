<?php

namespace App\Providers;

use App\Http\Livewire\Components\AdviceAppointmentForm\Completed as AdviceAppointmentFormCompleted;
use App\Http\Livewire\Components\AdviceAppointmentForm\ContactInfo as AdviceAppointmentFormContactInfo;
use App\Http\Livewire\Components\AdviceAppointmentForm\DateTimeStep as AdviceAppointmentFormDateTimeStep;
use App\Http\Livewire\Components\AdviceAppointmentWizardComponent;
use App\Http\Livewire\Components\HelpAppointmentForm\Completed;
use App\Http\Livewire\Components\HelpAppointmentForm\ContactInfo;
use App\Http\Livewire\Components\HelpAppointmentForm\DateTimeStep;
use App\Http\Livewire\Components\HelpAppointmentForm\ProblemDescriptionStep;
use App\Http\Livewire\Components\HelpAppointmentWizardComponent;
use App\Hub\Components\Slots\TrackingNumberSlot;
use App\Modifiers\ShippingModifier;
use Illuminate\Support\ServiceProvider;
use Livewire\Livewire;
use Lunar\Base\ShippingModifiers;
use Lunar\Facades\ModelManifest;
use Lunar\Hub\Facades\Slot;
use Lunar\Models\ProductVariant;

class AppServiceProvider extends ServiceProvider
{
    /**
     * Register any application services.
     */
    public function register(): void
    {
        Slot::register('order.show', TrackingNumberSlot::class);
    }

    /**
     * Bootstrap any application services.
     */
    public function boot(ShippingModifiers $shippingModifiers): void
    {
        $shippingModifiers->add(
            ShippingModifier::class
        );

        $models = collect([
            ProductVariant::class => \App\Models\ProductVariant::class,
        ]);

        ModelManifest::register($models);

        Livewire::component('hub.components.slots.tracking-number-slot', TrackingNumberSlot::class);

        Livewire::component('help-appointment-wizard-component', HelpAppointmentWizardComponent::class);
        Livewire::component('date-time-step', DateTimeStep::class);
        Livewire::component('problem-description-step', ProblemDescriptionStep::class);
        Livewire::component('contact-info', ContactInfo::class);
        Livewire::component('completed', Completed::class);

        Livewire::component('advice-appointment-wizard-component', AdviceAppointmentWizardComponent::class);
        Livewire::component('advice-appointment-date-time-step', AdviceAppointmentFormDateTimeStep::class);
        Livewire::component('advice-appointment-contact-info', AdviceAppointmentFormContactInfo::class);
        Livewire::component('advice-appointment-completed', AdviceAppointmentFormCompleted::class);
    }
}
