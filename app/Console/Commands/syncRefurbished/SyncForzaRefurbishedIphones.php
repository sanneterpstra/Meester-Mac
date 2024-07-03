<?php

namespace App\Console\Commands\syncRefurbished;

use Illuminate\Console\Command;
use Lunar\FieldTypes\Text;
use Lunar\Models\Product;
use Lunar\Models\ProductOption;
use Lunar\Models\ProductOptionValue;

class SyncForzaRefurbishedIphones extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'app:sync-forza-refurbished-iphones';

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
        $items = collect(json_decode(file_get_contents(storage_path('app/pricelists/refurbished/ForzaRefurbished_iPhones.json'))));
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

            $product['options'] = [];

            if (str_contains($item->product_grade, 'Zo goed als nieuw')) {
                $product['options']['refurbished_conditie'] = 'Zo goed als nieuw';
            } elseif (str_contains($item->product_grade, 'Licht gebruikt')) {
                $product['options']['refurbished_conditie'] = 'Licht gebruikt';
            } elseif (str_contains($item->product_grade, 'Zichtbaar gebruikt')) {
                $product['options']['refurbished_conditie'] = 'Zichtbaar gebruikt';
            }

            if (str_contains($item->product_name, 'iPhone XR')) {
                $product['model'] = 'Refurbished iPhone XR';
            } elseif (str_contains($item->product_name, 'iPhone XS')) {
                if (str_contains($item->product_name, 'iPhone XS Max')) {
                    $product['model'] = 'Refurbished iPhone XS Max';
                } else {
                    $product['model'] = 'Refurbished iPhone XS';
                }
            } elseif (str_contains($item->product_name, 'iPhone 11')) {
                if (str_contains($item->product_name, 'iPhone 11 Pro Max')) {
                    $product['model'] = 'Refurbished iPhone 11 Pro Max';
                } elseif (str_contains($item->product_name, 'iPhone 11 Pro')) {
                    $product['model'] = 'Refurbished iPhone 11 Pro';
                } else {
                    $product['model'] = 'Refurbished iPhone 11';
                }
            } elseif (str_contains($item->product_name, 'iPhone SE (2020)')) {
                $product['model'] = 'Refurbished iPhone SE (2020)';
            } elseif (str_contains($item->product_name, 'iPhone 12')) {
                if (str_contains($item->product_name, 'iPhone 12 Pro Max')) {
                    $product['model'] = 'Refurbished iPhone 12 Pro Max';
                } elseif (str_contains($item->product_name, 'iPhone 12 Pro')) {
                    $product['model'] = 'Refurbished iPhone 12 Pro';
                } elseif (str_contains($item->product_name, 'iPhone 12 Mini')) {
                    $product['model'] = 'Refurbished iPhone 12 Mini';
                } else {
                    $product['model'] = 'Refurbished iPhone 12';
                }
            } elseif (str_contains($item->product_name, 'iPhone 13')) {
                if (str_contains($item->product_name, 'iPhone 13 Mini')) {
                    $product['model'] = 'Refurbished iPhone 13 Mini';
                } elseif (str_contains($item->product_name, 'iPhone 13 Pro Max')) {
                    $product['model'] = 'Refurbished iPhone 13 Pro Max';
                } elseif (str_contains($item->product_name, 'iPhone 13 Pro')) {
                    $product['model'] = 'Refurbished iPhone 13 Pro';
                } else {
                    $product['model'] = 'Refurbished iPhone 13';
                }
            } elseif (str_contains($item->product_name, 'iPhone SE (2022)')) {
                $product['model'] = 'Refurbished iPhone SE (2022)';
            } elseif (str_contains($item->product_name, 'iPhone 14')) {
                if (str_contains($item->product_name, '(eSIM toestel)')) {
                    continue;
                } elseif (str_contains($item->product_name, 'iPhone 14 Plus')) {
                    $product['model'] = 'Refurbished iPhone 14 Plus';
                } elseif (str_contains($item->product_name, 'iPhone 14 Pro Max')) {
                    $product['model'] = 'Refurbished iPhone 14 Pro Max';
                } elseif (str_contains($item->product_name, 'iPhone 14 Pro')) {
                    $product['model'] = 'Refurbished iPhone 14 Pro';
                } else {
                    $product['model'] = 'Refurbished iPhone 14';
                }
            } elseif (str_contains($item->product_name, 'iPhone 15')) {
                if (str_contains($item->product_name, 'iPhone 15 Pro Max')) {
                    $product['model'] = 'Refurbished iPhone 15 Pro Max';
                } elseif (str_contains($item->product_name, 'iPhone 15 Pro')) {
                    $product['model'] = 'Refurbished iPhone 15 Pro';
                } elseif (str_contains($item->product_name, 'iPhone 15 Plus')) {
                    $product['model'] = 'Refurbished iPhone 15 Plus';
                } else {
                    $product['model'] = 'Refurbished iPhone 15';
                }
            }

            if (str_contains($item->product_storage, '64GB')) {
                $product['options']['opslagcapaciteit'] = '64 GB';
            } elseif (str_contains($item->product_storage, '128GB')) {
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

            $product['options']['kleur'] = $item->product_color;

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
