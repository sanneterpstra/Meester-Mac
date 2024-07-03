<?php

namespace App\Console\Commands\syncRefurbished;

use Illuminate\Console\Command;
use Lunar\FieldTypes\Text;
use Lunar\Models\Product;
use Lunar\Models\ProductOption;
use Lunar\Models\ProductOptionValue;

class SyncForzaRefurbishedMacBooks extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'app:sync-forza-refurbished-mac-books';

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
        $items = collect(json_decode(file_get_contents(storage_path('app/pricelists/refurbished/ForzaRefurbished_MacBooks.json'))));
        $products = $this->structureData($items);

        foreach ($products as $product) {
            $variantOptionIds = [];

            foreach ($product['options'] as $option => $optionValue) {

                $productOption = ProductOption::where('handle', $option)->first();

                if ($productOption) {
                    $productOptionValue = ProductOptionValue::where('product_option_id', $productOption->id)
                        ->where('name->nl', $optionValue)
                        ->first();

                    if ($productOptionValue) {
                        $variantOptionIds[$productOption->id] = $productOptionValue->id;
                    }
                }
            }

            $result = Product::search($product['model'])->first();

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
                $variant->attribute_data['forza_product_id'] = new Text($product['product_id']);
                $variant->attribute_data['forza_stock'] = new Text($product['stock']);
                $variant->attribute_data['forza_price'] = new Text(round($product['price'] * 1.21, 2));
                $variant->save();

                print_r('Updated: '.$variant->sku."\n");
            }
        }
    }

    protected function structureData($items)
    {
        $products = [];
        foreach ($items as $item) {
            if (! preg_match('/(M1|M2|M3)/', $item->processor)) {
                continue;
            }

            $product['options'] = [];

            if (str_contains($item->product_grade, 'Zo goed als nieuw')) {
                $product['options']['refurbished_conditie'] = 'Zo goed als nieuw';
            } elseif (str_contains($item->product_grade, 'Licht gebruikt')) {
                $product['options']['refurbished_conditie'] = 'Licht gebruikt';
            } elseif (str_contains($item->product_grade, 'Zichtbaar gebruikt')) {
                $product['options']['refurbished_conditie'] = 'Zichtbaar gebruikt';
            }

            if (str_contains($item->product_name, 'MacBook Pro')) {
                $product['model'] = 'Refurbished MacBook Pro';
            } elseif (str_contains($item->product_name, 'MacBook Air')) {
                $product['model'] = 'Refurbished MacBook Air';
            }

            if (str_contains($item->product_name, '13 Inch')) {
                $product['model'] .= ' 13"';
            } elseif (str_contains($item->product_name, '14 Inch')) {
                $product['model'] .= ' 14"';
            } elseif (str_contains($item->product_name, '15 Inch')) {
                $product['model'] .= ' 15"';
            } elseif (str_contains($item->product_name, '16 Inch')) {
                $product['model'] .= ' 16"';
            }

            if (str_contains($item->product_name, '2020')) {
                $product['model'] .= ' (2020)';
            } elseif (str_contains($item->product_name, '2021')) {
                $product['model'] .= ' (2021)';
            } elseif (str_contains($item->product_name, '2022')) {
                $product['model'] .= ' (2022)';
            } elseif (str_contains($item->product_name, '2023')) {
                $product['model'] .= ' (2023)';
            }

            if (str_contains($item->processor, 'M1')) {
                if (str_contains($item->processor, 'Pro')) {
                    $product['options']['system-on-a-chip-processor'] = 'M1 Pro';
                } elseif (str_contains($item->processor, 'Max')) {
                    $product['options']['system-on-a-chip-processor'] = 'M1 Max';
                } elseif (str_contains($item->processor, 'Ultra')) {
                    $product['options']['system-on-a-chip-processor'] = 'M1 Ultra';
                } else {
                    $product['options']['system-on-a-chip-processor'] = 'M1';
                }

                // if(str_contains($item->processor, "8-core")) {
                //     $product['options']['system-on-a-chip-processor'] .= " (8-core CPU/";
                // } elseif(str_contains($item->processor, "10-core")) {
                //     $product['options']['system-on-a-chip-processor'] .= " (10-core CPU/";
                // } elseif(str_contains($item->processor, "20-core")) {
                //     $product['options']['system-on-a-chip-processor'] .= " (20-core CPU/";
                // }

                // if(str_contains($item->{'Grafische kaart'}, "7-core")) {
                //     $product['options']['system-on-a-chip-processor'] .= "7-core GPU)";
                // } elseif(str_contains($item->{'Grafische kaart'}, "8-core")) {
                //     $product['options']['system-on-a-chip-processor'] .= "8-core GPU)";
                // } elseif(str_contains($item->{'Grafische kaart'}, "14-core")) {
                //     $product['options']['system-on-a-chip-processor'] .= "14-core GPU)";
                // } elseif(str_contains($item->{'Grafische kaart'}, "16-core")) {
                //     $product['options']['system-on-a-chip-processor'] .= "16-core GPU)";
                // } elseif(str_contains($item->{'Grafische kaart'}, "24-core")) {
                //     $product['options']['system-on-a-chip-processor'] .= "24-core GPU)";
                // } elseif(str_contains($item->{'Grafische kaart'}, "32-core")) {
                //     $product['options']['system-on-a-chip-processor'] .= "32-core GPU)";
                // } else {
                //     $product['options']['system-on-a-chip-processor'] .= "64-core GPU)";
                // }
            }

            if (str_contains($item->processor, 'M2')) {
                // if(str_contains($item->processor, "Pro")) {
                //     $product['options']['system-on-a-chip-processor'] = "M2 Pro";
                // } elseif(str_contains($item->processor, "Max")) {
                //     $product['options']['system-on-a-chip-processor'] = "M2 Max";
                // } elseif(str_contains($item->processor, "Ultra")) {
                //     $product['options']['system-on-a-chip-processor'] = "M2 Ultra";
                // } else {
                //     $product['options']['system-on-a-chip-processor'] = "M2";
                // }

                // if(str_contains($item->processor, "8-core")) {
                //     $product['options']['system-on-a-chip-processor'] .= " (8-core CPU/";
                // } elseif(str_contains($item->processor, "10-core")) {
                //     $product['options']['system-on-a-chip-processor'] .= " (10-core CPU/";
                // } elseif(str_contains($item->processor, "12-core")) {
                //     $product['options']['system-on-a-chip-processor'] .= " (12-core CPU/";
                // } else {
                //     $product['options']['system-on-a-chip-processor'] .= " (24-core CPU/";
                // }

                // if(str_contains($item->{'Grafische kaart'}, "8-core")) {
                //     $product['options']['system-on-a-chip-processor'] .= "8-core GPU)";
                // } elseif(str_contains($item->{'Grafische kaart'}, "10-core")) {
                //     $product['options']['system-on-a-chip-processor'] .= "10-core GPU)";
                // } elseif(str_contains($item->{'Grafische kaart'}, "16-core")) {
                //     $product['options']['system-on-a-chip-processor'] .= "16-core GPU)";
                // } elseif(str_contains($item->{'Grafische kaart'}, "19-core")) {
                //     $product['options']['system-on-a-chip-processor'] .= "19-core GPU)";
                // } elseif(str_contains($item->{'Grafische kaart'}, "30-core")) {
                //     $product['options']['system-on-a-chip-processor'] .= "30-core GPU)";
                // } elseif(str_contains($item->{'Grafische kaart'}, "38-core")) {
                //     $product['options']['system-on-a-chip-processor'] .= "38-core GPU)";
                // } elseif(str_contains($item->{'Grafische kaart'}, "60-core")) {
                //     $product['options']['system-on-a-chip-processor'] .= "60-core GPU)";
                // } else {
                //     $product['options']['system-on-a-chip-processor'] .= "76-core GPU)";
                // }
            }

            if (str_contains($item->product_memory, '8GB')) {
                $product['options']['werkgeheugen'] = '8 GB';
            } elseif (str_contains($item->product_memory, '16GB')) {
                $product['options']['werkgeheugen'] = '16 GB';
            } elseif (str_contains($item->product_memory, '24GB')) {
                $product['options']['werkgeheugen'] = '24 GB';
            } elseif (str_contains($item->product_memory, '32GB')) {
                $product['options']['werkgeheugen'] = '32 GB';
            } elseif (str_contains($item->product_memory, '64GB')) {
                $product['options']['werkgeheugen'] = '64 GB';
            } elseif (str_contains($item->product_memory, '96GB')) {
                $product['options']['werkgeheugen'] = '96 GB';
            } elseif (str_contains($item->product_memory, '128GB')) {
                $product['options']['werkgeheugen'] = '128 GB';
            }

            if (str_contains($item->product_storage, '128GB')) {
                $product['options']['opslagcapaciteit'] = '128 GB';
            } elseif (str_contains($item->product_storage, '256GB')) {
                $product['options']['opslagcapaciteit'] = '256 GB';
            } elseif (str_contains($item->product_storage, '512GB')) {
                $product['options']['opslagcapaciteit'] = '512 GB';
            } elseif (str_contains($item->product_storage, '1TB')) {
                $product['options']['opslagcapaciteit'] = '1 TB';
            } elseif (str_contains($item->product_storage, '2TB')) {
                $product['options']['opslagcapaciteit'] = '2 TB';
            } elseif (str_contains($item->product_storage, '4TB')) {
                $product['options']['opslagcapaciteit'] = '4 TB';
            } elseif (str_contains($item->product_storage, '8TB')) {
                $product['options']['opslagcapaciteit'] = '8 TB';
            }

            if (str_contains($item->product_color, 'Space Gray')) {
                $product['options']['kleur'] = 'Spacegrijs';
            } elseif (str_contains($item->product_color, 'Silver')) {
                $product['options']['kleur'] = 'Zilver';
            } elseif (str_contains($item->product_color, 'Gold')) {
                $product['options']['kleur'] = 'Goud';
            }

            $product['stock'] = $item->stock;

            $product['product_id'] = $item->sku;

            $product['price'] = preg_replace('/[^0-9]/', '', $item->price) / 100;

            $products[] = $product;
        }

        return $products;
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
}
