<?php

namespace App\Modifiers;

use Lunar\DataTypes\Price;
use Lunar\DataTypes\ShippingOption;
use Lunar\Facades\ShippingManifest;
use Lunar\Models\Cart;
use Lunar\Models\TaxClass;

class ShippingModifier
{
    public function handle(Cart $cart)
    {
        // Get the tax class
        $taxClass = TaxClass::getDefault();

        ShippingManifest::addOption(
            new ShippingOption(
                name: 'Normale verzending',
                description: 'Normale verzending',
                identifier: 'NORZEN',
                price: new Price(695, $cart->currency, 1),
                taxClass: $taxClass
            )
        );
    }
}
