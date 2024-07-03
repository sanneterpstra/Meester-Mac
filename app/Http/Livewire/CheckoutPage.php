<?php

namespace App\Http\Livewire;

use Exception;
use Http;
use Illuminate\Support\Collection;
use Livewire\Component;
use Livewire\ComponentConcerns\PerformsRedirects;
use Log;
use Lunar\Facades\CartSession;
use Lunar\Facades\Payments;
use Lunar\Facades\ShippingManifest;
use Lunar\Models\Cart;
use Lunar\Models\CartAddress;
use Lunar\Models\Country;
use Spatie\MailcoachSdk\Facades\Mailcoach;
use Vdhicts\ValidationRules\Rules\DutchPhone;
use Vdhicts\ValidationRules\Rules\DutchPostalCode;
use Webcraft\Lunar\Mollie\Enums\PaymentMethod;

class CheckoutPage extends Component
{
    use PerformsRedirects;

    /**
     * The Cart instance.
     */
    public ?Cart $cart;

    public $subscribeToNewsletter = true;

    public $termsAndConditionsAccepted = false;

    /**
     * The payment method instance.
     */
    public ?PaymentMethod $paymentMethod;

    /**
     * The shipping address instance.
     */
    public ?CartAddress $shipping = null;

    /**
     * The billing address instance.
     */
    public ?CartAddress $billing = null;

    /**
     * The current checkout step.
     */
    public int $currentStep = 1;

    /**
     * Whether the shipping address is the billing address too.
     */
    public bool $shippingIsBilling = true;

    /**
     * The chosen shipping option.
     *
     * @var string|int
     */
    public $chosenShipping = null;

    /**
     * The checkout steps.
     */
    public array $steps = [
        'shipping_address' => 1,
        'shipping_option' => 2,
        'billing_address' => 3,
        'payment' => 4,
    ];

    /**
     * {@inheritDoc}
     */
    protected $listeners = [
        'cartUpdated' => 'refreshCart',
        'selectedShippingOption' => 'refreshCart',
    ];

    /**
     * {@inheritDoc}
     */
    public function rules()
    {
        return array_merge(
            $this->getAddressValidation('shipping'),
            $this->getAddressValidation('billing'),
            [
                'shippingIsBilling' => 'boolean',
                'chosenShipping' => 'required',
                'termsAndConditionsAccepted' => 'accepted',
            ]
        );
    }

    /**
     * Return the address validation rules for a given type.
     *
     * @param  string  $type
     * @return array
     */
    protected function getAddressValidation($type)
    {
        return [
            "{$type}.first_name" => 'required',
            "{$type}.last_name" => 'required',
            "{$type}.line_one" => 'required',
            "{$type}.line_two" => 'required',
            "{$type}.country_id" => 'required',
            "{$type}.city" => 'required',
            "{$type}.postcode" => ['required', new DutchPostalCode],
            "{$type}.company_name" => 'nullable',
            "{$type}.delivery_instructions" => 'nullable',
            "{$type}.contact_email" => 'required|email:rfc,dns',
            "{$type}.contact_phone" => ['nullable', new DutchPhone],
        ];
    }

    public function messages()
    {
        return array_merge(
            $this->getAddressValidationMessages('shipping'),
            $this->getAddressValidationMessages('billing'),
            [
                'termsAndConditionsAccepted.accepted' => 'De algemene voorwaarden zijn niet geaccepteerd.',
            ]
        );
    }

    public function getAddressValidationMessages($type)
    {
        return [
            "{$type}.first_name.required" => 'Er is geen voornaam ingevuld',
            "{$type}.last_name.required" => 'Er is geen achternaam ingevuld',
            "{$type}.line_one.required" => 'Er is geen adres ingevuld',
            "{$type}.line_two.required" => 'Er is geen huisnummer ingevuld',
            "{$type}.country_id.required" => 'Er is geen land geselecteerd',
            "{$type}.city.required" => 'Er is geen plaatsnaam ingevuld',
            "{$type}.contact_email.required" => 'Er is geen e-mailadres ingevuld',
            "{$type}.contact_email.email" => 'Dit is geen geldig e-mailadres',
        ];
    }

    public function getPaymentMethods(): array
    {
        return array_map(fn ($paymentMethod) => PaymentMethod::from($paymentMethod), config('lunar.mollie.payment_methods'));
    }

    public function updated($propertyName)
    {
        if (str_contains($propertyName, 'shipping') || str_contains($propertyName, 'billing')) {
            $type = explode('.', $propertyName)[0];
            $validated = $this->validateOnly($propertyName);

            if (str_contains($propertyName, 'first_name') || str_contains($propertyName, 'last_name')) {
                $this->{$type}['first_name'] = ucfirst($this->{$type}['first_name']);
                $this->{$type}['last_name'] = $this->titleCase($this->{$type}['last_name']);
            }
            if (str_contains($propertyName, 'postcode') || str_contains($propertyName, 'line_two')) {
                if ($this->{$type}['postcode'] && $this->{$type}['line_two'] && $validated) {
                    $this->{$type}['postcode'] = strtoupper(str_replace(' ', '', $this->{$type}['postcode']));
                    $this->getAddress($type);
                }
            }
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

    protected function getAddress($type)
    {
        $response = Http::withHeaders([
            'token' => 'b178c681-c12b-493b-9b57-ff1fa6611061',
        ])->get('https://json.api-postcode.nl', [
            'postcode' => $this->{$type}['postcode'],
            'number' => $this->{$type}['line_two'],
        ]);
        if ($response->status() == 200) {
            $response = $response->json();
            $this->{$type}['line_one'] = $response['street'];
            $this->{$type}['city'] = $response['city'];
        }
    }

    /**
     * {@inheritDoc}
     *
     * @return void
     */
    public function mount()
    {
        if (! $this->cart = CartSession::current()) {
            $this->redirect(route('shop.view'));

            return;
        }

        // Do we have a shipping address?
        $this->shipping = $this->cart->shippingAddress ?: new CartAddress(['country_id' => 156]);

        $this->billing = $this->cart->billingAddress ?: new CartAddress(['country_id' => 156]);

        $this->determineCheckoutStep();
    }

    /**
     * Determines what checkout step we should be at.
     *
     * @return void
     */
    public function determineCheckoutStep()
    {
        $shippingAddress = $this->cart->shippingAddress;
        $billingAddress = $this->cart->billingAddress;

        if ($shippingAddress) {
            if ($shippingAddress->id) {
                $this->currentStep = $this->steps['shipping_address'] + 1;
            }

            // Do we have a selected option?
            if ($this->shippingOption) {
                $this->chosenShipping = $this->shippingOption->getIdentifier();
                $this->currentStep = $this->steps['shipping_option'] + 1;
            } else {
                $this->currentStep = $this->steps['shipping_option'];
                $this->chosenShipping = $this->shippingOptions->first()?->getIdentifier();

                return;
            }
        }

        if ($billingAddress) {
            $this->currentStep = $this->steps['billing_address'] + 1;
        }
    }

    /**
     * {@inheritDoc}
     */
    public function hydrate()
    {
        $this->cart = CartSession::current();
    }

    /**
     * Trigger an event to refresh addresses.
     *
     * @return void
     */
    public function triggerAddressRefresh()
    {
        $this->emit('refreshAddress');
    }

    /**
     * Return the shipping option.
     *
     * @return void
     */
    public function getShippingOptionProperty()
    {
        $shippingAddress = $this->cart->shippingAddress;

        if (! $shippingAddress) {
            return;
        }

        if ($option = $shippingAddress->shipping_option) {
            return ShippingManifest::getOptions($this->cart)->first(function ($opt) use ($option) {
                return $opt->getIdentifier() == $option;
            });
        }

        return null;
    }

    /**
     * Save the address for a given type.
     *
     * @param  string  $type
     * @return void
     */
    public function saveAddress($type)
    {
        $validatedData = $this->validate(
            $this->getAddressValidation($type)
        );

        $address = $this->{$type};

        if ($type == 'billing') {
            $this->cart->setBillingAddress($address);
            $this->billing = $this->cart->billingAddress;
        }

        if ($type == 'shipping') {
            $this->cart->setShippingAddress($address);
            $this->shipping = $this->cart->shippingAddress;

            if ($this->shippingIsBilling) {
                // Do we already have a billing address?
                if ($billing = $this->cart->billingAddress) {
                    $billing->fill($validatedData['shipping']);
                    $this->cart->setBillingAddress($billing);
                } else {
                    $address = $address->only(
                        $address->getFillable()
                    );
                    $this->cart->setBillingAddress($address);
                }

                $this->billing = $this->cart->billingAddress;
            }
        }

        $this->determineCheckoutStep();
    }

    /**
     * Save the selected shipping option.
     *
     * @return void
     */
    public function saveShippingOption()
    {
        $option = $this->shippingOptions->first(fn ($option) => $option->getIdentifier() == $this->chosenShipping);

        CartSession::setShippingOption($option);

        $this->refreshCart();

        $this->determineCheckoutStep();
    }

    /**
     * Refresh the cart instance.
     *
     * @return void
     */
    public function refreshCart()
    {
        $this->cart = CartSession::current();
    }

    public function handleSubmit(?string $paymentMethod = null)
    {
        $this->validate();

        if ($this->subscribeToNewsletter) {
            try {
                $subscriber = Mailcoach::createSubscriber(
                    emailListUuid: '056bedc2-7ee7-4e0d-ba09-b2f7c41a6131',
                    attributes: [
                        'first_name' => $this->cart->shippingAddress->first_name,
                        'last_name' => $this->cart->shippingAddress->last_name,
                        'email' => $this->cart->shippingAddress->contact_email,
                        'tags' => ['Customer'],
                    ]
                )->confirm();
            } catch (Exception $exception) {
                Log::critical('Could not subscribe customer to newsletter: '.$exception->getMessage());
            }
        }

        $payment = Payments::driver('mollie')->cart($this->cart)->withData([
            'description' => trans('lunar::mollie.payment_description'),
            'redirectRoute' => config('lunar.mollie.redirect_route'),
            'webhookUrl' => config('lunar.mollie.override_webhook_url') ?: route(config('lunar.mollie.webhook_route')),
            'method' => $paymentMethod,
        ])->initiatePayment();

        $this->redirect($payment->getCheckoutUrl());
    }

    /**
     * Return the available countries.
     *
     * @return Collection
     */
    public function getCountriesProperty()
    {
        return Country::whereIn('iso3', ['NLD'])->get();
    }

    /**
     * Return available shipping options.
     *
     * @return Collection
     */
    public function getShippingOptionsProperty()
    {
        return ShippingManifest::getOptions(
            $this->cart
        );
    }

    public function render()
    {
        return view('livewire.shop.checkout-page')
            ->layout('layouts.checkout');
    }
}
