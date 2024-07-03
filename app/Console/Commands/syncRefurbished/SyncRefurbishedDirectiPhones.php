<?php

namespace App\Console\Commands\syncRefurbished;

use Illuminate\Console\Command;
use Lunar\FieldTypes\Text;
use Lunar\Models\Product;
use Lunar\Models\ProductOption;
use Lunar\Models\ProductOptionValue;

class SyncRefurbishedDirectiPhones extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'app:sync-refurbished-direct-iphones';

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
        $items = collect(json_decode(file_get_contents(storage_path('app/pricelists/refurbished/RefurbishedDirect_iPhones.json'))));
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
            if ($variant) {
                // Sync stock
                $variant->stock = intval($product['stock']);

                // Add Refurbished Direct URL to product page
                $variant->attribute_data['refurbisheddirect_product_id'] = new Text($product['product_id']);
                $variant->attribute_data['refurbisheddirect_stock'] = new Text($product['stock']);
                $variant->attribute_data['refurbisheddirect_price'] = new Text(round($product['price'] * 1.21, 2));
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

            if (str_contains($item->Kleur, 'Black')) {
                $product['options']['kleur'] = 'Zwart';
            } elseif (str_contains($item->Kleur, 'Graphite')) {
                $product['options']['kleur'] = 'Grafiet';
            } elseif (str_contains($item->Kleur, 'Space Gray')) {
                $product['options']['kleur'] = 'Spacegrijs';
            } elseif (str_contains($item->Kleur, 'Pacific Blue')) {
                $product['options']['kleur'] = 'Oceaanblauw';
            } elseif (str_contains($item->Kleur, 'Blue')) {
                $product['options']['kleur'] = 'Blauw';
            } elseif (str_contains($item->Kleur, 'Alpine Green')) {
                $product['options']['kleur'] = 'Alpengroen';
            } elseif (str_contains($item->Kleur, 'Midnight Green')) {
                $product['options']['kleur'] = 'Middernacht groen';
            } elseif (str_contains($item->Kleur, 'Green')) {
                $product['options']['kleur'] = 'Groen';
            } elseif (str_contains($item->Kleur, 'Starlight')) {
                $product['options']['kleur'] = 'Sterrenlicht';
            } elseif (str_contains($item->Kleur, 'Silver')) {
                $product['options']['kleur'] = 'Zilver';
            } elseif (str_contains($item->Kleur, 'Gold')) {
                $product['options']['kleur'] = 'Goud';
            } elseif (str_contains($item->Kleur, 'Pink')) {
                $product['options']['kleur'] = 'Roze';
            } elseif (str_contains($item->Kleur, 'Roze')) {
                $product['options']['kleur'] = 'Roze';
            } elseif (str_contains($item->Kleur, 'Coral')) {
                $product['options']['kleur'] = 'Koraal';
            } elseif (str_contains($item->Kleur, 'Yellow')) {
                $product['options']['kleur'] = 'Geel';
            } elseif (str_contains($item->Kleur, 'Purple')) {
                $product['options']['kleur'] = 'Paars';
            } elseif (str_contains($item->Kleur, 'White')) {
                $product['options']['kleur'] = 'Wit';
            } elseif (str_contains($item->Kleur, 'Red')) {
                $product['options']['kleur'] = 'Rood';
            } elseif (str_contains($item->Kleur, 'Midnight')) {
                $product['options']['kleur'] = 'Middernacht';
            }

            if ($item->Model === 'iPhone X') {
                continue;
            } elseif (str_contains($item->Model, 'iPhone XR')) {
                $product['model'] = 'Refurbished iPhone XR';
            } elseif (str_contains($item->Model, 'iPhone XS')) {
                if (str_contains($item->Model, 'iPhone XS Max')) {
                    $product['model'] = 'Refurbished iPhone XS Max';
                } else {
                    $product['model'] = 'Refurbished iPhone XS';
                }
            } elseif (str_contains($item->Model, 'iPhone 11')) {
                if (str_contains($item->Model, 'iPhone 11 Pro Max')) {
                    $product['model'] = 'Refurbished iPhone 11 Pro Max';
                } elseif (str_contains($item->Model, 'iPhone 11 Pro')) {
                    $product['model'] = 'Refurbished iPhone 11 Pro';
                } else {
                    $product['model'] = 'Refurbished iPhone 11';
                }
            } elseif (str_contains($item->Model, 'iPhone SE (2020)')) {
                $product['model'] = 'Refurbished iPhone SE (2020)';
            } elseif (str_contains($item->Model, 'iPhone 12')) {
                if (str_contains($item->Model, 'iPhone 12 Pro Max')) {
                    $product['model'] = 'Refurbished iPhone 12 Pro Max';
                } elseif (str_contains($item->Model, 'iPhone 12 Pro')) {
                    $product['model'] = 'Refurbished iPhone 12 Pro';
                } elseif (str_contains($item->Model, 'iPhone 12 mini')) {
                    $product['model'] = 'Refurbished iPhone 12 Mini';
                } else {
                    $product['model'] = 'Refurbished iPhone 12';
                }
            } elseif (str_contains($item->Model, 'iPhone 13')) {
                if (str_contains($item->Model, 'iPhone 13 mini')) {
                    $product['model'] = 'Refurbished iPhone 13 Mini';
                    if (str_contains($item->Kleur, 'Black')) {
                        $product['options']['kleur'] = 'Middernacht';
                    } elseif (str_contains($item->Kleur, 'White')) {
                        $product['options']['kleur'] = 'Sterrenlicht';
                    }
                } elseif (str_contains($item->Model, 'iPhone 13 Pro')) {
                    if (str_contains($item->Model, 'Max')) {
                        $product['model'] = 'Refurbished iPhone 13 Pro Max';
                    } else {
                        $product['model'] = 'Refurbished iPhone 13 Pro';
                    }
                    if (str_contains($item->Kleur, 'Midnight Green')) {
                        $product['options']['kleur'] = 'Alpengroen';
                    } elseif (str_contains($item->Kleur, 'Blue')) {
                        $product['options']['kleur'] = 'Sierra Blauw';
                    }
                } else {
                    $product['model'] = 'Refurbished iPhone 13';
                    if (str_contains($item->Kleur, 'Black')) {
                        $product['options']['kleur'] = 'Middernacht';
                    } elseif (str_contains($item->Kleur, 'White')) {
                        $product['options']['kleur'] = 'Sterrenlicht';
                    }
                }
            } elseif (str_contains($item->Model, 'iPhone SE (2022)')) {
                $product['model'] = 'Refurbished iPhone SE (2022)';

                if (str_contains($item->Kleur, 'Black')) {
                    $product['options']['kleur'] = 'Middernacht';
                } elseif (str_contains($item->Kleur, 'White')) {
                    $product['options']['kleur'] = 'Sterrenlicht';
                }
            } elseif (str_contains($item->Model, 'iPhone 14')) {
                if (property_exists($item, 'Simkaart formaat')) {
                    if (str_contains($item->{'Simkaart formaat'}, 'eSIM (digitaal)')) {
                        continue;
                    }
                } elseif (str_contains($item->Model, 'iPhone 14 Plus')) {
                    $product['model'] = 'Refurbished iPhone 14 Plus';
                    if (str_contains($item->Kleur, 'Black')) {
                        $product['options']['kleur'] = 'Middernacht';
                    } elseif (str_contains($item->Kleur, 'White')) {
                        $product['options']['kleur'] = 'Sterrenlicht';
                    }
                } elseif (str_contains($item->Model, 'iPhone 14 Pro Max')) {
                    $product['model'] = 'Refurbished iPhone 14 Pro Max';
                } elseif (str_contains($item->Model, 'iPhone 14 Pro')) {
                    $product['model'] = 'Refurbished iPhone 14 Pro';
                    if (str_contains($item->Kleur, 'Black')) {
                        $product['options']['kleur'] = 'Ruimtezwart';
                    } elseif (str_contains($item->Kleur, 'Purple')) {
                        $product['options']['kleur'] = 'Dieppaars';
                    }
                } else {
                    $product['model'] = 'Refurbished iPhone 14';
                    if (str_contains($item->Kleur, 'Black')) {
                        $product['options']['kleur'] = 'Middernacht';
                    } elseif (str_contains($item->Kleur, 'White')) {
                        $product['options']['kleur'] = 'Sterrenlicht';
                    }
                }
            }

            if (str_contains($item->Opslag, '64GB')) {
                $product['options']['opslagcapaciteit'] = '64 GB';
            } elseif (str_contains($item->Opslag, '128GB')) {
                $product['options']['opslagcapaciteit'] = '128 GB';
            } elseif (str_contains($item->Opslag, '256GB')) {
                $product['options']['opslagcapaciteit'] = '256 GB';
            } elseif (str_contains($item->Opslag, '512GB')) {
                $product['options']['opslagcapaciteit'] = '512 GB';
            } elseif (str_contains($item->Opslag, '1TB')) {
                $product['options']['opslagcapaciteit'] = '1 TB';
            } elseif (str_contains($item->Opslag, '2TB')) {
                $product['options']['opslagcapaciteit'] = '2 TB';
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
