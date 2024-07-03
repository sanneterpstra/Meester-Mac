<?php

namespace App\Console\Commands\syncRefurbished;

use Illuminate\Console\Command;
use Lunar\FieldTypes\Text;
use Lunar\Models\Product;
use Lunar\Models\ProductOption;
use Lunar\Models\ProductOptionValue;

class SyncRenewdiPhones extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'app:sync-renewd-iphones';

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
        $items = collect(json_decode(file_get_contents(storage_path('app/pricelists/refurbished/Renewd_iPhones.json'))));
        $products = $this->structureData($items);

        foreach ($products as $product) {
            // Create options array
            $variantOptionIds = [];

            foreach ($product['options'] as $option => $optionValue) {

                $productOption = ProductOption::where('handle', $option)->first();

                if ($productOption) {
                    $productOptionValue = ProductOptionValue::where('product_option_id', $productOption->id)
                        ->where('name->nl', $optionValue)
                        ->first();

                    $variantOptionIds[$productOption->id] = $productOptionValue->id;
                }
            }

            // Get product
            $result = Product::search($product['model'])->first();

            // Get product variant based on options
            $variant = $this->getVariant($result, $variantOptionIds);

            // Sync stock
            $variant->stock = intval($product['stock']);

            // Add Refurbished Direct URL to product page
            $variant->attribute_data['renewd_product_id'] = new Text($product['product_id']);
            $variant->attribute_data['renewd_stock'] = new Text($product['stock']);
            $variant->attribute_data['renewd_price'] = new Text(round($product['price'] * 1.21, 2));

            $variant->save();
            print_r('Updated: '.$variant->sku."\n");
        }
    }

    protected function structureData($items)
    {
        $products = [];
        foreach ($items as $item) {
            $product['options'] = [];

            $product['options']['refurbished_conditie'] = 'Zo goed als nieuw';

            if (str_contains($item->description, 'Black')) {
                $product['options']['kleur'] = 'Zwart';
            } elseif (str_contains($item->description, 'Graphite')) {
                $product['options']['kleur'] = 'Grafiet';
            } elseif (str_contains($item->description, 'Space Gray')) {
                $product['options']['kleur'] = 'Spacegrijs';
            } elseif (str_contains($item->description, 'Pacific Blue')) {
                $product['options']['kleur'] = 'Oceaanblauw';
            } elseif (str_contains($item->description, 'Blue')) {
                $product['options']['kleur'] = 'Blauw';
            } elseif (str_contains($item->description, 'Alpine Green')) {
                $product['options']['kleur'] = 'Alpengroen';
            } elseif (str_contains($item->description, 'Midnight Green')) {
                $product['options']['kleur'] = 'Middernacht groen';
            } elseif (str_contains($item->description, 'Green')) {
                $product['options']['kleur'] = 'Groen';
            } elseif (str_contains($item->description, 'Starlight')) {
                $product['options']['kleur'] = 'Sterrenlicht';
            } elseif (str_contains($item->description, 'Silver')) {
                $product['options']['kleur'] = 'Zilver';
            } elseif (str_contains($item->description, 'Gold')) {
                $product['options']['kleur'] = 'Goud';
            } elseif (str_contains($item->description, 'Pink')) {
                $product['options']['kleur'] = 'Roze';
            } elseif (str_contains($item->description, 'Coral')) {
                $product['options']['kleur'] = 'Koraal';
            } elseif (str_contains($item->description, 'Yellow')) {
                $product['options']['kleur'] = 'Geel';
            } elseif (str_contains($item->description, 'Purple')) {
                $product['options']['kleur'] = 'Paars';
            } elseif (str_contains($item->description, 'White')) {
                $product['options']['kleur'] = 'Wit';
            } elseif (str_contains($item->description, 'Red')) {
                $product['options']['kleur'] = 'Rood';
            } elseif (str_contains($item->description, 'Midnight')) {
                $product['options']['kleur'] = 'Middernacht';
            }

            if (str_contains($item->description, 'iPhone XR')) {
                $product['model'] = 'Refurbished iPhone XR';
            } elseif (str_contains($item->description, 'iPhone XS')) {
                if (str_contains($item->description, 'iPhone XS Max')) {
                    $product['model'] = 'Refurbished iPhone XS Max';
                } else {
                    $product['model'] = 'Refurbished iPhone XS';
                }

            } elseif (str_contains($item->description, 'iPhone X')) {
                continue;
            } elseif (str_contains($item->description, 'iPhone 11')) {
                if (str_contains($item->description, 'iPhone 11 Pro Max')) {
                    $product['model'] = 'Refurbished iPhone 11 Pro Max';
                } elseif (str_contains($item->description, 'iPhone 11 Pro')) {
                    $product['model'] = 'Refurbished iPhone 11 Pro';
                } else {
                    $product['model'] = 'Refurbished iPhone 11';
                }

            } elseif (str_contains($item->description, 'iPhone SE2020')) {
                $product['model'] = 'Refurbished iPhone SE (2020)';

            } elseif (str_contains($item->description, 'iPhone 12')) {
                if (str_contains($item->description, 'iPhone 12 Pro Max')) {
                    $product['model'] = 'Refurbished iPhone 12 Pro Max';
                } elseif (str_contains($item->description, 'iPhone 12 Pro')) {
                    $product['model'] = 'Refurbished iPhone 12 Pro';
                } elseif (str_contains($item->description, 'iPhone 12 mini')) {
                    $product['model'] = 'Refurbished iPhone 12 Mini';
                } else {
                    $product['model'] = 'Refurbished iPhone 12';
                }
            } elseif (str_contains($item->description, 'iPhone 13')) {
                if (str_contains($item->description, 'iPhone 13 mini')) {
                    $product['model'] = 'Refurbished iPhone 13 Mini';
                    if (str_contains($item->description, 'Black')) {
                        $product['options']['kleur'] = 'Middernacht';
                    } elseif (str_contains($item->description, 'White')) {
                        $product['options']['kleur'] = 'Sterrenlicht';
                    }
                } elseif (str_contains($item->description, 'iPhone 13 Pro')) {
                    if (str_contains($item->description, 'Max')) {
                        $product['model'] = 'Refurbished iPhone 13 Pro Max';
                    } else {
                        $product['model'] = 'Refurbished iPhone 13 Pro';
                    }
                    if (str_contains($item->description, 'Midnight Green')) {
                        $product['options']['kleur'] = 'Alpengroen';
                    } elseif (str_contains($item->description, 'Blue')) {
                        $product['options']['kleur'] = 'Sierra Blauw';
                    }
                } else {
                    $product['model'] = 'Refurbished iPhone 13';
                    if (str_contains($item->description, 'Black')) {
                        $product['options']['kleur'] = 'Middernacht';
                    } elseif (str_contains($item->description, 'White')) {
                        $product['options']['kleur'] = 'Sterrenlicht';
                    }
                }

            } elseif (str_contains($item->description, 'iPhone SE (3rd gen)')) {
                $product['model'] = 'Refurbished iPhone SE (2022)';

                if (str_contains($item->description, 'Black')) {
                    $product['options']['kleur'] = 'Middernacht';
                } elseif (str_contains($item->description, 'White')) {
                    $product['options']['kleur'] = 'Sterrenlicht';
                }

            } elseif (str_contains($item->description, 'iPhone 14')) {
                if (str_contains($item->{'Simkaart formaat'}, 'eSIM (digitaal)')) {
                    continue;
                } elseif (str_contains($item->description, 'iPhone 14 Plus')) {
                    $product['model'] = 'Refurbished iPhone 14 Plus';
                    if (str_contains($item->description, 'Black')) {
                        $product['options']['kleur'] = 'Middernacht';
                    } elseif (str_contains($item->description, 'White')) {
                        $product['options']['kleur'] = 'Sterrenlicht';
                    }
                } elseif (str_contains($item->description, 'iPhone 14 Pro Max')) {
                    $product['model'] = 'Refurbished iPhone 14 Pro Max';
                } elseif (str_contains($item->description, 'iPhone 14 Pro')) {
                    $product['model'] = 'Refurbished iPhone 14 Pro';
                    if (str_contains($item->description, 'Black')) {
                        $product['options']['kleur'] = 'Ruimtezwart';
                    } elseif (str_contains($item->description, 'Purple')) {
                        $product['options']['kleur'] = 'Dieppaars';
                    }
                } else {
                    $product['model'] = 'Refurbished iPhone 14';
                    if (str_contains($item->description, 'Black')) {
                        $product['options']['kleur'] = 'Middernacht';
                    } elseif (str_contains($item->description, 'White')) {
                        $product['options']['kleur'] = 'Sterrenlicht';
                    }
                }
            } else {
                continue;
            }

            if (str_contains($item->description, ' 64GB')) {
                $product['options']['opslagcapaciteit'] = '64 GB';
            } elseif (str_contains($item->description, ' 128GB')) {
                $product['options']['opslagcapaciteit'] = '128 GB';
            } elseif (str_contains($item->description, ' 256GB')) {
                $product['options']['opslagcapaciteit'] = '256 GB';
            } elseif (str_contains($item->description, ' 512GB')) {
                $product['options']['opslagcapaciteit'] = '512 GB';
            } elseif (str_contains($item->description, ' 1TB')) {
                $product['options']['opslagcapaciteit'] = '1 TB';
            } elseif (str_contains($item->description, ' 2TB')) {
                $product['options']['opslagcapaciteit'] = '2 TB';
            }

            $product['stock'] = $item->stock;

            $product['product_id'] = $item->sku;

            $product['price'] = preg_replace('/[^0-9]/', '', $item->retail_price) / 100;

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
