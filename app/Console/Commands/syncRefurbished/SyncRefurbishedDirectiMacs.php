<?php

namespace App\Console\Commands\syncRefurbished;

use Illuminate\Console\Command;
use Lunar\FieldTypes\Text;
use Lunar\Models\Product;
use Lunar\Models\ProductOptionValue;

class SyncRefurbishedDirectiMacs extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'app:sync-refurbished-direct-imacs';

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
        $items = collect(json_decode(file_get_contents(storage_path('app/pricelists/refurbished/RefurbishedDirect_iMacs.json'))));
        $products = $this->structureData($items);

        foreach ($products as $product) {
            // Create options array
            $variantOptionIds = [];

            foreach ($product['options'] as $option) {
                $variantOptionIds[] = ProductOptionValue::where('name->nl', $option)->first()->id;
            }

            // Get product
            $result = Product::search($product['model'])->first();

            // Get product variant based on options
            $variant = $this->getVariant($result, $variantOptionIds);

            if ($variant) {
                // Sync stock
                $variant->stock = intval($product['stock']);

                // Add Refurbished Direct URL to product page
                $variant->attribute_data['refurbisheddirect_product_id'] = new Text($product['product_id']);
                $variant->attribute_data['refurbisheddirect_stock'] = new Text($product['stock']);
                $variant->attribute_data['refurbisheddirect_price'] = new Text(round(($product['price'] - (($product['price'] / 100) * 7)) * 1.21, 2));
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

            if (str_contains($item->Conditie, 'Nieuw') || str_contains($item->Conditie, 'Refurbished door fabrikant') || str_contains($item->Conditie, 'Uitstekend') || str_contains($item->Conditie, 'Uitmuntend')) {
                $product['options']['refurbished_conditie'] = 'Zo goed als nieuw';
            } elseif (str_contains($item->Conditie, 'Zeer goed')) {
                $product['options']['refurbished_conditie'] = 'Licht gebruikt';
            } elseif (str_contains($item->Conditie, 'Goed')) {
                $product['options']['refurbished_conditie'] = 'Zichtbaar gebruikt';
            }

            if (str_contains($item->Model, 'iMac 24 Inch')) {
                $product['model'] = 'Refurbished iMac 24"';
            }

            if (str_contains($item->{'Model uitgave'}, '2021')) {
                $product['model'] .= ' (2021)';
            }

            if (str_contains($item->Processor, 'M1')) {
                $product['options']['processor'] = 'M1 (8-core CPU/';

                if (str_contains($item->{'Grafische kaart'}, '7-core')) {
                    $product['options']['processor'] .= '7-core GPU)';
                } elseif (str_contains($item->{'Grafische kaart'}, '8-core')) {
                    $product['options']['processor'] .= '8-core GPU)';
                }
            }

            $product['options']['werkgeheugen'] = $item->Werkgeheugen;

            if (str_contains($item->Opslag, '256GB')) {
                $product['options']['opslagcapaciteit'] = '256 GB';
            } elseif (str_contains($item->Opslag, '512GB')) {
                $product['options']['opslagcapaciteit'] = '512 GB';
            } elseif (str_contains($item->Opslag, '1TB')) {
                $product['options']['opslagcapaciteit'] = '1 TB';
            } elseif (str_contains($item->Opslag, '2TB')) {
                $product['options']['opslagcapaciteit'] = '2 TB';
            }

            if (str_contains($item->Kleur, 'Blue')) {
                $product['options']['kleur'] = 'Blauw';
            } elseif (str_contains($item->Kleur, 'Green')) {
                $product['options']['kleur'] = 'Groen';
            } elseif (str_contains($item->Kleur, 'Silver')) {
                $product['options']['kleur'] = 'Zilver';
            } elseif (str_contains($item->Kleur, 'Pink')) {
                $product['options']['kleur'] = 'Roze';
            } elseif (str_contains($item->Kleur, 'Orange')) {
                $product['options']['kleur'] = 'Oranje';
            } elseif (str_contains($item->Kleur, 'Yellow')) {
                $product['options']['kleur'] = 'Geel';
            } elseif (str_contains($item->Kleur, 'Purple')) {
                $product['options']['kleur'] = 'Paars';
            }

            $product['stock'] = $item->Stock;

            $product['product_id'] = $item->Product_id;

            $product['price'] = preg_replace('/[^0-9]/', '', $item->Price) / 100;

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
