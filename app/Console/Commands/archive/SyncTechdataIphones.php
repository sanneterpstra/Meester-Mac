<?php

namespace App\Console\Commands;

use Illuminate\Console\Command;
use Lunar\Hub\Actions\Pricing\UpdatePrices;
use Lunar\Models\Product;
use Lunar\Models\ProductOption;
use Lunar\Models\ProductOptionValue;

class SyncTechdataIphones extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'app:sync-techdata-iphones';

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
        $items = collect(json_decode(file_get_contents('techdata_iphones_test.json')));
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
                $variant->attribute_data['techdata_product_id'] = new \Lunar\FieldTypes\Text($product['techdata_product_id']);

                // // Sync price
                // $pricing['EUR'] = [
                //     'id' => $variant->getPrices()->first()->id,
                //     'price' => (0.05*round(($product['price']*1.21)*20)),
                //     'currency_id' => 1,
                //     'tier' => 1,
                //     'compare_price' => 0,
                // ];
                // app(UpdatePrices::class)->execute($variant, collect($pricing));

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

            if (str_contains($item->name, 'iPhone')) {
                $product['model'] = 'iPhone';
            } else {
                continue;
            }

            if (str_contains($item->name, 'Apple iPhone SE (3rd generation)')) {
                $product['model'] .= ' SE (2022)';
            } elseif (str_contains($item->name, 'Apple iPhone 13')) {
                $product['model'] .= ' 13';
            } elseif (str_contains($item->name, 'Apple iPhone 14')) {
                if (str_contains($item->name, 'Pro')) {
                    continue;
                } elseif (str_contains($item->name, 'Plus')) {
                    $product['model'] .= ' 14 Plus';
                } else {
                    $product['model'] .= ' 14';
                }
            } elseif (str_contains($item->name, 'Apple iPhone 15')) {
                if (str_contains($item->name, 'Pro')) {
                    if (str_contains($item->name, 'Max')) {
                        $product['model'] .= ' 15 Pro Max';
                    } else {
                        $product['model'] .= ' 15 Pro';
                    }
                } elseif (str_contains($item->name, 'Plus')) {
                    $product['model'] .= ' 15 Plus';
                } else {
                    $product['model'] .= ' 15';
                }
            } else {
                continue;
            }

            if (str_contains($item->description, ' 64 GB ')) {
                $product['options']['opslagcapaciteit'] = '64 GB';
            } elseif (str_contains($item->description, ' 128 GB ')) {
                $product['options']['opslagcapaciteit'] = '128 GB';
            } elseif (str_contains($item->description, ' 256 GB ')) {
                $product['options']['opslagcapaciteit'] = '256 GB';
            } elseif (str_contains($item->description, ' 512 GB ')) {
                $product['options']['opslagcapaciteit'] = '512 GB';
            } elseif (str_contains($item->description, ' 1 TB ')) {
                $product['options']['opslagcapaciteit'] = '1 TB';
            } elseif (str_contains($item->description, ' 2 TB ')) {
                $product['options']['opslagcapaciteit'] = '2 TB';
            }

            if (str_contains($item->description, 'Space Gray') || str_contains($item->description, 'Space Grey')) {
                $product['options']['kleur'] = 'Spacegrijs';
            } elseif (str_contains($item->description, 'silver')) {
                $product['options']['kleur'] = 'Zilver';
            } elseif (str_contains($item->description, 'Gold')) {
                $product['options']['kleur'] = 'Goud';
            } elseif (str_contains($item->description, 'blue titanium')) {
                $product['options']['kleur'] = 'Blauw titanium';
            } elseif (str_contains($item->description, 'black titanium')) {
                $product['options']['kleur'] = 'Zwart titanium';
            } elseif (str_contains($item->description, 'white titanium')) {
                $product['options']['kleur'] = 'Wit titanium';
            } elseif (str_contains($item->description, 'natural titanium')) {
                $product['options']['kleur'] = 'Naturel titanium';
            } elseif (str_contains($item->description, 'midnight')) {
                $product['options']['kleur'] = 'Middernacht';
            } elseif (str_contains($item->description, 'space black')) {
                $product['options']['kleur'] = 'Ruimtezwart';
            } elseif (str_contains($item->description, 'deep purple')) {
                $product['options']['kleur'] = 'Dieppaars';
            } elseif (str_contains($item->description, 'starlight')) {
                $product['options']['kleur'] = 'Sterrenlicht';
            } elseif (str_contains($item->description, 'black')) {
                $product['options']['kleur'] = 'Zwart';
            } elseif (str_contains($item->description, 'white')) {
                $product['options']['kleur'] = 'Wit';
            } elseif (str_contains($item->description, 'pink')) {
                $product['options']['kleur'] = 'Roze';
            } elseif (str_contains($item->description, 'red') || str_contains($item->description, 'PRODUCTRED')) {
                $product['options']['kleur'] = 'Rood';
            } elseif (str_contains($item->description, 'blue')) {
                $product['options']['kleur'] = 'Blauw';
            } elseif (str_contains($item->description, 'green')) {
                $product['options']['kleur'] = 'Groen';
            } elseif (str_contains($item->description, 'purple')) {
                $product['options']['kleur'] = 'Paars';
            } elseif (str_contains($item->description, 'yellow')) {
                $product['options']['kleur'] = 'Geel';
            }

            $product['stock'] = $item->stock;

            $product['techdata_product_id'] = $item->partnr;

            $products[] = $product;
        }

        return $products;
    }
}
