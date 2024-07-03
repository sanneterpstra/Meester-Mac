<?php

namespace App\Console\Commands;

use Illuminate\Console\Command;
use Lunar\Hub\Actions\Pricing\UpdatePrices;
use Lunar\Models\Product;
use Lunar\Models\ProductOption;
use Lunar\Models\ProductOptionValue;

class SyncAlsoMacBooks extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'app:sync-also-mac-books';

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
        $csv = new \ParseCsv\Csv();
        $csv->auto('also.csv');
        $items = $csv->data;
        $products = $this->structureData($items);

        foreach ($products as $product) {
            $variantOptionIds = [];
            print_r($product);
            print_r("\n");
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
            $item = (object) $item;
            $item->Description = str_replace("\u{00a0}", ' ', $item->Description);
            $item->Description = str_replace("\u{2011}", '-', $item->Description);
            if (
                str_contains($item->Description, 'Azerty') ||
                str_contains($item->ShortDescription, 'Azerty') ||
                str_contains($item->Description, 'French') ||
                str_contains($item->Description, 'BUNDLE') ||
                str_contains($item->Description, 'M1 Pro') ||
                str_contains($item->Description, 'M1 Max')
            ) {
                continue;
            }

            // if(str_contains($item->Description, 'MacBook Pro')) {
            //     $product['model'] = "MacBook Pro";
            // }
            if (str_contains($item->Description, 'MacBook Air 13')) {
                if (str_contains($item->Description, 'VESA') || str_contains($item->Description, '+')) {
                    continue;
                }
                $product['model'] = 'MacBook Air';
            } else {
                continue;
            }

            if (
                str_contains($item->Description, 'FCP') ||
                str_contains($item->Description, 'FC Pro') ||
                str_contains($item->Description, 'LogPro') ||
                str_contains($item->Description, 'FC') ||
                str_contains($item->Description, 'LP') ||
                str_contains($item->Description, 'Final Cut Pro') ||
                str_contains($item->Description, 'Logic Pro')
            ) {
                if (
                    (str_contains($item->Description, 'No FCP') && str_contains($item->Description, 'No LP')) ||
                    (str_contains($item->Description, 'No FC Pro') && str_contains($item->Description, 'No LogPro')) ||
                    (str_contains($item->Description, 'NFC') && str_contains($item->Description, 'NLP')) ||
                    (str_contains($item->Description, 'No Final Cut Pro') && str_contains($item->Description, 'No Logic Pro')) ||
                    (str_contains($item->Description, 'Without Final Cut Pro') && str_contains($item->Description, 'Without Logic Pro')) ||
                    (str_contains($item->Description, 'Without Final Cut Pro') && str_contains($item->Description, 'Without Logic Pr'))
                ) {
                    print_r($item->Description);
                    print_r("\n");
                } else {
                    continue;
                }
            }

            $product['options'] = [];

            if (str_contains($item->ShortDescription, ' 13 ') || str_contains($item->ShortDescription, ' MBP13 ')) {
                if (str_contains($item->ShortDescription, 'M1')) {
                    $product['model'] .= ' 13" (2020)';
                } else {
                    $product['model'] .= ' 13" (2022)';
                }
            } elseif (str_contains($item->ShortDescription, ' 14 ')) {
                $product['model'] .= ' 14" (2023)';
            } elseif (str_contains($item->ShortDescription, ' 15 ')) {
                $product['model'] .= ' 15" (2023)';
            } elseif (str_contains($item->ShortDescription, ' 16 ')) {
                $product['model'] .= ' 16" (2023)';
            }

            if (str_contains($item->ShortDescription, 'M1')) {
                if (str_contains($item->Description, 'iMac')) {
                    $product['options']['system-on-a-chip-processor'] = 'M1 (8-core CPU/';

                    if (
                        str_contains($item->Description, '8-core CPU and 7-core GPU') ||
                        str_contains($item->Description, '8c CPU and 7c GPU') ||
                        str_contains($item->Description, '8c CPU&7c GPU')
                    ) {
                        $product['options']['system-on-a-chip-processor'] .= '7-core GPU)';
                    } elseif (
                        str_contains($item->Description, '8-core CPU and 8-core GPU') ||
                        str_contains($item->Description, '8c CPU and 8c GPU') ||
                        str_contains($item->Description, '8-core CPU and 8-core GPU') ||
                        str_contains($item->Description, '8c CPU&8c GPU')
                    ) {
                        $product['options']['system-on-a-chip-processor'] .= '8-core GPU)';
                    }

                }
            }

            if (str_contains($item->ShortDescription, 'M2')) {
                if (str_contains($item->ShortDescription, 'M2Pro') || str_contains($item->ShortDescription, 'M2P')) {
                    $product['options']['system-on-a-chip-processor'] = 'M2 Pro';
                } elseif (
                    str_contains($item->ShortDescription, 'M2Max') ||
                    str_contains($item->Description, 'M2 Max')
                ) {
                    $product['options']['system-on-a-chip-processor'] = 'M2 Max';
                } elseif (
                    str_contains($item->ShortDescription, 'M2U') ||
                    str_contains($item->Description, 'M2 Ultra')
                ) {
                    $product['options']['system-on-a-chip-processor'] = 'M2 Ultra';
                } else {
                    $product['options']['system-on-a-chip-processor'] = 'M2';
                }

                if (
                    str_contains($item->ShortDescription, 'M2 8') ||
                    str_contains($item->Description, '8core CPU')
                ) {
                    $product['options']['system-on-a-chip-processor'] .= ' (8-core CPU/';
                } elseif (
                    str_contains($item->ShortDescription, 'M2Pro 10c') ||
                    str_contains($item->ShortDescription, 'M2P10c')) {
                    $product['options']['system-on-a-chip-processor'] .= ' (10-core CPU/';
                } elseif (
                    str_contains($item->ShortDescription, 'M2Pro 12c') ||
                    str_contains($item->ShortDescription, 'M2P12c') ||
                    str_contains($item->ShortDescription, 'M2Max 12c') ||
                    str_contains($item->Description, 'M2 Max 12C') ||
                    str_contains($item->Description, 'Max chip with 12-core CPU')
                ) {
                    $product['options']['system-on-a-chip-processor'] .= ' (12-core CPU/';
                } elseif (
                    str_contains($item->Description, 'M2 Ultra 24C') ||
                    str_contains($item->Description, 'M2 Ultra chip with 24-core CPU')
                ) {
                    $product['options']['system-on-a-chip-processor'] .= ' (24-core CPU/';
                }

                if (
                    str_contains($item->Description, '8core CPU and 8core GPU') ||
                    str_contains($item->Description, '8C CPU/8C GPU') ||
                    str_contains($item->Description, '8c CPU 8c GPU')
                ) {
                    $product['options']['system-on-a-chip-processor'] .= '8-core GPU)';
                } elseif (
                    str_contains($item->Description, '8-core CPU and 10-core') ||
                    str_contains($item->Description, '8core CPU and 10core GPU') ||
                    str_contains($item->Description, '8core CPU 10core GPU') ||
                    str_contains($item->Description, '8C CPU/10C GPU') ||
                    str_contains($item->Description, '8c CPU 10c GPU')

                ) {
                    $product['options']['system-on-a-chip-processor'] .= '10-core GPU)';
                } elseif (
                    str_contains($item->Description, '10c CPU 16C GPU') ||
                    str_contains($item->Description, '10-core CPU and 16-core') ||
                    str_contains($item->Description, '10c CPU 16c GPU')
                ) {
                    $product['options']['system-on-a-chip-processor'] .= '16-core GPU)';
                } elseif (
                    str_contains($item->Description, '12c CPU 19c GPU') ||
                    str_contains($item->Description, '12-core CPU and 19-core GPU')
                ) {
                    $product['options']['system-on-a-chip-processor'] .= '19-core GPU)';
                } elseif (
                    str_contains($item->Description, '12-core CPU and 30-core GPU') ||
                    str_contains($item->Description, '12-core CPU 30-core GPU') ||
                    str_contains($item->Description, '12C CPU/30C GPU') ||
                    str_contains($item->Description, '12c CPU 30c GPU')
                ) {
                    $product['options']['system-on-a-chip-processor'] .= '30-core GPU)';
                } elseif (
                    str_contains($item->Description, '12c CPU 38c GPU') ||
                    str_contains($item->Description, '12C CPU/38C GPU') ||
                    str_contains($item->Description, '12-core CPU and 38-core GPU')
                ) {
                    $product['options']['system-on-a-chip-processor'] .= '38-core GPU)';
                } elseif (
                    str_contains($item->Description, '24C CPU/60C GPU') ||
                    str_contains($item->Description, '24-core CPU 60-core GPU')
                ) {
                    $product['options']['system-on-a-chip-processor'] .= '60-core GPU)';
                } elseif (
                    str_contains($item->Description, '24C CPU/76C GPU')
                ) {
                    $product['options']['system-on-a-chip-processor'] .= '76-core GPU)';
                }
            }

            if (
                str_contains($item->Description, ' 8GB ') ||
                str_contains($item->Description, 'APPLE 13inch MacBook Air: Apple M1 chip with 8core CPU and 7core GPU 256GB') ||
                str_contains($item->Description, 'APPLE 13inch MacBook Air: Apple M2 chip with 8core CPU and 8core GPU') ||
                str_contains($item->Description, 'APPLE MacBook Air 15inch Apple M2 chip with 8core CPU and 10core GPU') ||
                str_contains($item->Description, 'APPLE Mac mini: Apple M2 chip with 8-core CPU and 10-core') ||
                str_contains($item->Description, 'APPLE 24inch iMac with Retina 4.5K display: Apple M1 chip with 8-core CPU') ||
                str_contains($item->Description, ' 8G ')
            ) {
                $product['options']['werkgeheugen'] = '8 GB';
            } elseif (
                str_contains($item->Description, ' 16GB ') ||
                str_contains($item->Description, 'APPLE 16inch MacBook Pro: Apple M2 Pro chip with 12-core CPU and 19-core GPU') ||
                str_contains($item->Description, 'APPLE 14inch MacBook Pro: Apple M2 Pro chip with 12-core CPU and 19-core GPU') ||
                str_contains($item->Description, 'APPLE Mac mini: Apple M2 Pro chip with 10-core CPU and 16-core GPU') ||
                str_contains($item->Description, ' 16G ')
            ) {
                $product['options']['werkgeheugen'] = '16 GB';
            } elseif (str_contains($item->Description, ' 24GB ')) {
                $product['options']['werkgeheugen'] = '24 GB';
            } elseif (
                str_contains($item->Description, ' 32GB ') ||
                str_contains($item->Description, 'APPLE 16inch MacBook Pro: Apple M2 Max chip with 12-core CPU and 38-core GPU') ||
                str_contains($item->Description, 'APPLE 14inch MacBook Pro: Apple M2 Max chip with 12-core CPU and 30-core GPU') ||
                str_contains($item->Description, 'APPLE Mac Studio Apple M2 Max chip with 12-core CPU 30-core GPU')
            ) {
                $product['options']['werkgeheugen'] = '32 GB';
            } elseif (
                str_contains($item->Description, ' 64GB ') ||
                str_contains($item->Description, 'APPLE Mac Studio Apple M2 Ultra chip with 24-core CPU 60-core GPU')
            ) {
                $product['options']['werkgeheugen'] = '64 GB';
            } elseif (str_contains($item->Description, ' 96GB ')) {
                $product['options']['werkgeheugen'] = '96 GB';
            } elseif (str_contains($item->Description, ' 128GB ')) {
                $product['options']['werkgeheugen'] = '128 GB';
            } elseif (str_contains($item->Description, ' 192GB ')) {
                $product['options']['werkgeheugen'] = '192 GB';
            }

            if (
                str_contains($item->Description, ' 256GB ') ||
                str_contains($item->Description, ' 256GS ')
            ) {
                $product['options']['opslagcapaciteit'] = '256 GB';
            } elseif (
                str_contains($item->Description, ' 512GB ') ||
                str_contains($item->Description, ' 512GS ')
            ) {
                $product['options']['opslagcapaciteit'] = '512 GB';
            } elseif (
                str_contains($item->Description, ' 1TB ')
            ) {
                $product['options']['opslagcapaciteit'] = '1 TB';
            } elseif (str_contains($item->Description, ' 2TB ')) {
                $product['options']['opslagcapaciteit'] = '2 TB';
            } elseif (str_contains($item->Description, ' 4TB ')) {
                $product['options']['opslagcapaciteit'] = '4 TB';
            } elseif (str_contains($item->Description, ' 8TB ')) {
                $product['options']['opslagcapaciteit'] = '8 TB';
            }

            if (
                str_contains($item->Description, 'Space Gray') ||
                str_contains($item->Description, 'Space Grey') ||
                str_contains($item->Description, 'Gray')
            ) {
                $product['options']['kleur'] = 'Spacegrijs';
            } elseif (str_contains($item->Description, 'Silver')) {
                $product['options']['kleur'] = 'Zilver';
            } elseif (str_contains($item->Description, 'Gold')) {
                $product['options']['kleur'] = 'Goud';
            } elseif (str_contains($item->Description, 'Midnight')) {
                $product['options']['kleur'] = 'Middernacht';
            } elseif (str_contains($item->Description, 'Starlight')) {
                $product['options']['kleur'] = 'Sterrenlicht';
            } elseif (str_contains($item->Description, 'Green')) {
                $product['options']['kleur'] = 'Groen';
            } elseif (str_contains($item->Description, 'Blue')) {
                $product['options']['kleur'] = 'Blauw';
            } elseif (str_contains($item->Description, 'Pink')) {
                $product['options']['kleur'] = 'Roze';
            } elseif (
                str_contains($item->Description, 'Yellow') ||
                str_contains($item->ShortDescription, 'YE')
            ) {
                $product['options']['kleur'] = 'Geel';
            } elseif (
                str_contains($item->Description, 'Purple') ||
                str_contains($item->ShortDescription, 'PUR')
            ) {
                $product['options']['kleur'] = 'Paars';
            } elseif (
                str_contains($item->Description, 'Orange') ||
                str_contains($item->ShortDescription, 'OG')
            ) {
                $product['options']['kleur'] = 'Oranje';
            }

            if (
                str_contains($item->Description, ' 30W ') ||
                str_contains($item->Description, 'APPLE 13inch MacBook Air: Apple M2 chip with 8core CPU and 8core GPU')
            ) {
                $product['options']['oplader'] = '30 W';
            } elseif (
                str_contains($item->Description, ' 35W Dual ') ||
                str_contains($item->Description, 'APPLE MacBook Air 15inch Apple M2 chip with 8core CPU and 10core GPU')
            ) {
                $product['options']['oplader'] = '35 W (2 USB-C poorten)';
            } elseif (str_contains($item->Description, ' 67W ')) {
                $product['options']['oplader'] = '67 W';
            } elseif (str_contains($item->Description, ' 70W ')) {
                $product['options']['oplader'] = '70 W';
            } elseif (str_contains($item->Description, ' 96W ')) {
                $product['options']['oplader'] = '96 W';
            }

            if (str_contains($item->Description, '10 Gigabit Ethernet')) {
                $product['options']['ethernet'] = '10 Gigabit Ethernet';
            } elseif (
                str_contains($item->Description, ' Gigabit Ethernet ') ||
                str_contains($item->Description, 'APPLE Mac mini: Apple M2 chip with 8-core CPU and 10-core GPU') ||
                str_contains($item->Description, 'APPLE Mac mini: Apple M2 Pro chip with 10-core CPU and 16-core GPU') ||
                str_contains($item->Description, 'GB Eth.') ||
                str_contains($item->Description, ' GB ') ||
                str_contains($item->Description, 'Gigabit') ||
                str_contains($item->Description, 'APPLE 24inch iMac with Retina 4.5K display: Apple M1 chip with 8-core CPU and 8-core GPU')
            ) {
                $product['options']['ethernet'] = 'Gigabit Ethernet';
            } else {
                $product['options']['ethernet'] = 'Geen';
            }

            if (
                str_contains($item->Description, 'Magic Keyboard Touch IDNumeric Dutch') ||
                str_contains($item->Description, 'NUM/NL') ||
                str_contains($item->Description, 'MagicKB Touch IDNumeric Dutch')
            ) {
                $product['options']['toetsenbord'] = 'Magic Keyboard met Touch ID en numeriek toetsenblok';
            } elseif (
                str_contains($item->Description, ' Magic Keyboard Touch ID Dutch ') ||
                str_contains($item->Description, 'NL/Qwerty') && str_contains($item->Description, 'M1 chip with 8c CPU and 8c GPU') ||
                str_contains($item->Description, ' MagicKB Touch ID Dutch ') ||
                str_contains($item->Description, 'APPLE 24inch iMac with Retina 4.5K display: Apple M1 chip with 8-core CPU and 8-core GPU')
            ) {
                $product['options']['toetsenbord'] = 'Magic Keyboard met Touch ID';
            } elseif (
                str_contains($item->Description, ' Magic Keyboard Dutch ') ||
                str_contains($item->Description, 'NL/Qwerty') && str_contains($item->Description, 'M1 chip with 8c CPU and 7c GPU') ||
                str_contains($item->ShortDescription, 'NL/Qwerty') && str_contains($item->Description, 'APPLE 24inch iMac with Retina 4.5K display: Apple M1 chip with 8-core CPU and 7-core GPU')
            ) {
                $product['options']['toetsenbord'] = 'Magic Keyboard';
            }

            if (
                preg_match('/\b'.preg_quote('Magic Mouse', '/').'\b/', $item->Description) ||
                preg_match('/\b'.preg_quote('MagMouse', '/').'\b/', $item->Description) ||
                preg_match('/\b'.preg_quote('MagicMouse', '/').'\b/', $item->Description) ||
                str_contains($item->Description, 'APPLE 24inch iMac with Retina 4.5K display: Apple M1 chip with 8-core CPU and 8-core GPU') ||
                str_contains($item->Description, 'APPLE 24inch iMac with Retina 4.5K display: Apple M1 chip with 8-core CPU and 7-core GPU')
            ) {
                $product['options']['muis'] = 'Magic Mouse';
            } elseif (
                preg_match('/\b'.preg_quote('MagTrackpad', '/').'\b/', $item->Description) ||
                preg_match('/\b'.preg_quote('Magic Trackpad', '/').'\b/', $item->Description)
            ) {
                $product['options']['muis'] = 'Magic Trackpad';
            }

            $product['stock'] = $item->AvailableQuantity;

            $product['also_product_id'] = $item->ProductID;

            $product['price'] = preg_replace('/[^0-9]/', '', number_format((float) $item->NetRetailPrice, 2, '.', '')) / 100;

            $products[] = $product;
        }

        return $products;
    }
}
