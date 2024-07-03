<?php

namespace App\Console\Commands;

use Illuminate\Console\Command;
use Lunar\Hub\Actions\Pricing\UpdatePrices;
use Lunar\Models\Product;
use Lunar\Models\ProductOption;
use Lunar\Models\ProductOptionValue;

class SyncAlsoIphones extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'app:sync-also-iphones';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Command description';

    /**
     * Execute the console command.
     */
    public function handle()
    {
        $items = collect(json_decode(file_get_contents('ctomacs.json')));
        $products = $this->structureData($items);

        foreach ($products as $product) {
            $variantOptionIds = [];

            foreach ($product['options'] as $option => $optionValue) {

                $productOption = ProductOption::where('handle', $option)->first();

                if ($productOption) {
                    $productOptionValue = ProductOptionValue::where('product_option_id', $productOption->id)
                        ->where('name->nl', $optionValue)
                        ->first();

                    print_r($optionValue."\n");
                    $variantOptionIds[$productOption->id] = $productOptionValue->id;
                }
            }

            $result = Product::search('"'.$product['model'].'"')->first();

            $optionIds = $result->variants->map(function ($variant) {
                return $variant->values->map(function ($value) {
                    return $value->option->id;
                })->flatten();
            })->unique()->flatten()->toArray();

            $variantOptionIds = array_filter($variantOptionIds, function ($k) use ($optionIds) {
                return in_array($k, $optionIds);
            }, ARRAY_FILTER_USE_KEY);

            $variant = $this->getVariant($result, $variantOptionIds);
            if ($variant) {
                $variant->stock = intval($product['stock']);

                // Add Refurbished Direct URL to product page
                $variant->attribute_data['also_product_id'] = new \Lunar\FieldTypes\Text($product['also_product_id']);

                // Sync price
                $pricing['EUR'] = [
                    'id' => $variant->getPrices()->first()->id,
                    'price' => (0.05 * round(($product['price'] * 1.21) * 20)),
                    'currency_id' => 1,
                    'tier' => 1,
                    'compare_price' => 0,
                ];
                app(UpdatePrices::class)->execute($variant, collect($pricing));

                $variant->save();
            }
        }
    }

    protected function getVariant($product, $optionValueIds)
    {
        return $product->variants->first(function ($variant) use ($optionValueIds) {
            return ! $variant->values->pluck('id')
                ->diff(
                    collect($optionValueIds)->values()
                )->count();
        });
    }

    protected function structureData($items)
    {
        $products = [];
        foreach ($items as $item) {
            $product['options'] = [];

            if (str_contains($item->ShortDescription, 'iPhone')) {
                if (str_contains($item->ShortDescription, 'DEMO')) {
                    continue;
                }
                $product['model'] = 'iPhone';
            } else {
                continue;
            }

            if (str_contains($item->ShortDescription, 'iPhone SE 3rd gen')) {
                $product['model'] .= ' SE (2022)';
            } elseif (str_contains($item->ShortDescription, 'iPhone 13')) {
                if (str_contains($item->ShortDescription, 'mini')) {
                    continue;
                } else {
                    $product['model'] .= ' 13';
                }
            } elseif (str_contains($item->ShortDescription, 'iPhone 14')) {
                if (str_contains($item->ShortDescription, 'Pro')) {
                    continue;
                } elseif (str_contains($item->ShortDescription, 'Plus')) {
                    $product['model'] .= ' 14 Plus';
                } else {
                    $product['model'] .= ' 14';
                }
            } elseif (str_contains($item->ShortDescription, 'iPhone 15')) {
                if (str_contains($item->ShortDescription, 'Pro')) {
                    if (str_contains($item->ShortDescription, 'Max')) {
                        $product['model'] .= ' 15 Pro Max';
                    } else {
                        $product['model'] .= ' 15 Pro';
                    }
                } elseif (str_contains($item->ShortDescription, 'Plus')) {
                    $product['model'] .= ' 15 Plus';
                } else {
                    $product['model'] .= ' 15';
                }
            } else {
                continue;
            }

            if (str_contains($item->ShortDescription, ' 64GB ')) {
                $product['options']['opslagcapaciteit'] = '64 GB';
            } elseif (str_contains($item->ShortDescription, ' 128GB ')) {
                $product['options']['opslagcapaciteit'] = '128 GB';
            } elseif (str_contains($item->ShortDescription, ' 256GB ')) {
                $product['options']['opslagcapaciteit'] = '256 GB';
            } elseif (str_contains($item->ShortDescription, ' 512GB ')) {
                $product['options']['opslagcapaciteit'] = '512 GB';
            } elseif (str_contains($item->ShortDescription, ' 1TB ')) {
                $product['options']['opslagcapaciteit'] = '1 TB';
            } elseif (str_contains($item->ShortDescription, ' 2TB ')) {
                $product['options']['opslagcapaciteit'] = '2 TB';
            }

            if (str_contains($item->ShortDescription, 'Space Gray') || str_contains($item->ShortDescription, 'Space Grey')) {
                $product['options']['kleur'] = 'Spacegrijs';
            } elseif (str_contains($item->ShortDescription, 'Silver')) {
                $product['options']['kleur'] = 'Zilver';
            } elseif (str_contains($item->ShortDescription, 'Gold')) {
                $product['options']['kleur'] = 'Goud';
            } elseif (str_contains($item->Description, 'Blue Titanium')) {
                $product['options']['kleur'] = 'Blauw titanium';
            } elseif (str_contains($item->Description, 'Black Titanium')) {
                $product['options']['kleur'] = 'Zwart titanium';
            } elseif (str_contains($item->Description, 'White Titanium')) {
                $product['options']['kleur'] = 'Wit titanium';
            } elseif (str_contains($item->Description, 'Natural Titanium')) {
                $product['options']['kleur'] = 'Naturel titanium';
            } elseif (str_contains($item->ShortDescription, 'Midnight')) {
                $product['options']['kleur'] = 'Middernacht';
            } elseif (str_contains($item->ShortDescription, 'Space Black')) {
                $product['options']['kleur'] = 'Ruimtezwart';
            } elseif (str_contains($item->ShortDescription, 'Deep Purple')) {
                $product['options']['kleur'] = 'Dieppaars';
            } elseif (str_contains($item->ShortDescription, 'Starlight')) {
                $product['options']['kleur'] = 'Sterrenlicht';
            } elseif (str_contains($item->ShortDescription, 'Black')) {
                $product['options']['kleur'] = 'Zwart';
            } elseif (str_contains($item->ShortDescription, 'Pink')) {
                $product['options']['kleur'] = 'Roze';
            } elseif (str_contains($item->ShortDescription, 'PRODUCT RED') || str_contains($item->ShortDescription, 'PRODUCTRED')) {
                $product['options']['kleur'] = 'Rood';
            } elseif (str_contains($item->ShortDescription, 'Blue')) {
                $product['options']['kleur'] = 'Blauw';
            } elseif (str_contains($item->ShortDescription, 'Green')) {
                $product['options']['kleur'] = 'Groen';
            } elseif (str_contains($item->ShortDescription, 'Purple')) {
                $product['options']['kleur'] = 'Paars';
            } elseif (str_contains($item->ShortDescription, 'Yellow')) {
                $product['options']['kleur'] = 'Geel';
            }

            $product['stock'] = $item->AvailableQuantity;

            $product['also_product_id'] = $item->ProductID;

            $product['price'] = preg_replace('/[^0-9]/', '', number_format((float) $item->NetRetailPrice, 2, '.', '')) / 100;

            $products[] = $product;
        }

        return $products;
    }
}
