<?php

namespace App\Pipelines\Order\Creation;

use Closure;
use LemmoTresto\Moneybird\MoneybirdFacade as Moneybird;
use Lunar\Models\Address;
use Lunar\Models\Customer;
use Lunar\Models\Order;

class AttachCustomer
{
    protected function createOrUpdateMoneybirdContact($billingAddress, $moneyBirdContact = null)
    {
        $contact = $moneyBirdContact ? Moneybird::contact()->find($moneyBirdContact->id) : Moneybird::contact();
        $contact->company_name = $billingAddress->company_name;
        $contact->firstname = $billingAddress->first_name;
        $contact->lastname = $billingAddress->last_name;
        $contact->email = $billingAddress->contact_email;
        $contact->address1 = $billingAddress->line_one.' '.$billingAddress->line_two;
        $contact->zipcode = $billingAddress->postcode;
        $contact->city = $billingAddress->city;
        $contact->save();

        return $contact;
    }

    /**
     * @return Closure
     */
    public function handle(Order $order, Closure $next)
    {
        $moneybirdContacts = Moneybird::contact()->get(['query' => $order->billingAddress->contact_email]);

        if (count($moneybirdContacts) !== 0) {
            foreach ($moneybirdContacts as $moneybirdContact) {
                if ($moneybirdContact->email == $order->billingAddress->contact_email) {
                    $contact = $this->createOrUpdateMoneybirdContact($order->billingAddress, $moneybirdContact);
                }
            }
        }

        if (! isset($contact)) {
            $contact = $this->createOrUpdateMoneybirdContact($order->billingAddress);
        }

        $customer = Customer::firstOrCreate([
            'meta->moneybird_customer_number' => $contact->customer_id,
        ], [
            'first_name' => $contact->firstname,
            'last_name' => $contact->lastname,
            'company_name' => $contact->company_name,
        ]);

        $order->customer()->associate($customer);
        $order->save();

        foreach ($order->addresses as $orderAddress) {
            $address = Address::firstOrNew([
                'country_id' => $orderAddress->country_id,
                'first_name' => $orderAddress->first_name,
                'last_name' => $orderAddress->last_name,
                'company_name' => $orderAddress->company_name,
                'line_one' => $orderAddress->line_one,
                'line_two' => $orderAddress->line_two,
                'line_three' => $orderAddress->line_three,
                'postcode' => $orderAddress->postcode,
                'city' => $orderAddress->city,
                'contact_email' => $orderAddress->contact_email,
                'contact_phone' => $orderAddress->contact_phone,
            ], []);

            $customer->addresses()->save($address);
        }

        return $next($order);
    }
}
