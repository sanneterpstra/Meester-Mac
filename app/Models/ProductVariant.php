<?php

namespace App\Models;

use Iksaku\Laravel\MassUpdate\MassUpdatable;
use Lunar\Base\Traits\Searchable;

class ProductVariant extends \Lunar\Models\ProductVariant
{
    use MassUpdatable, Searchable;
}
