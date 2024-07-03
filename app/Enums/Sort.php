<?php

namespace App\Enums;

enum Sort: string
{
    case STOCK = 'op_voorraad';
    case PRICE = 'laagste_prijs';

    public function friendlyName()
    {
        return match ($this) {
            Sort::PRICE => 'Prijs',
            Sort::STOCK => 'Voorraad',
        };
    }

    public function value()
    {
        return match ($this) {
            Sort::PRICE => ['price:asc', 'stock:desc'],
            Sort::STOCK => ['stock:desc', 'price:asc'],
        };
    }
}
