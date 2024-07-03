<?php

namespace App\Console\Commands;

use Illuminate\Console\Command;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Process;
use Lunar\FieldTypes\Text;
use Lunar\Hub\Actions\Pricing\UpdatePrices;
use Lunar\Models\Product;

class SyncRefurbished extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'app:sync-refurbished';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Command description';

    protected $forzaRefurbishedMarginPercentage = 5.26299941585;

    protected $renewdMarginPercentage = 6;

    protected $refurbishedDirectMarginPercentage = 7;

    /**
     * Execute the console command.
     */
    public function handle()
    {
        // Forza Refurbished
        $this->retrieveStock('ForzaRefurbished', 'iPhones', 'python_scripts/createForzaRefurbishedIphonesList.py');
        $this->retrieveStock('ForzaRefurbished', 'MacBooks', 'python_scripts/createForzaRefurbishedMacBooksList.py');

        // Refurbished Direct
        $this->retrieveStock('RefurbishedDirect', 'iMacs', 'python_scripts/createRefurbishedDirectImacsList.py');
        // $this->retrieveStock('RefurbishedDirect', 'iPhones', 'python_scripts/createRefurbishedDirectIphonesList.py');
        $this->retrieveStock('RefurbishedDirect', 'MacBooks', 'python_scripts/createRefurbishedDirectMacBooksList.py');

        // Renewd
        $this->retrieveStock('Renewd', 'iPhones', 'python_scripts/createRenewdIphonesList.py');

        $this->resetStock();

        $this->syncStock();

        $this->setPrices();

        $this->call('lunar:search:index');

        Log::channel('syncstock')->info('Search reindexed');
    }

    protected function retrieveStock($distributor, $devices, $script)
    {
        Log::info($distributor.' ['.$devices.']: Retrieving current stock and prices');

        $pricelistFilePath = storage_path('app/pricelists/refurbished/'.$distributor.'_'.$devices.'.json');
        $result = Process::forever()->run('python3 '.$script.' '.$pricelistFilePath);

        if (! $result->successful()) {
            file_put_contents($pricelistFilePath, json_encode([]));
            Log::critical($distributor.' ['.$devices."]: Retrieving current stock and prices failed. Cleared pricelist. Error: \n".$result->errorOutput());
        } else {
            Log::info($distributor.' ['.$devices.']: Retrieving current stock and prices success.');
        }
    }

    protected function resetStock()
    {
        $products = Product::where('attribute_data->refurbished->value', 'true')->get();

        foreach ($products as $product) {
            foreach ($product->variants as $variant) {

                $variant->attribute_data['renewd_product_id'] = new Text('');
                $variant->attribute_data['renewd_stock'] = new Text('');
                $variant->attribute_data['renewd_price'] = new Text('');
                $variant->attribute_data['forza_product_id'] = new Text('');
                $variant->attribute_data['forza_stock'] = new Text('');
                $variant->attribute_data['forza_price'] = new Text('');
                $variant->attribute_data['refurbisheddirect_product_id'] = new Text('');
                $variant->attribute_data['refurbisheddirect_stock'] = new Text('');
                $variant->attribute_data['refurbisheddirect_price'] = new Text('');
                $variant->stock = 0;
                $variant->save();

                // Sync price
                $pricing['EUR'] = [
                    'id' => $variant->getPrices()->first()->id,
                    'price' => 1,
                    'currency_id' => 1,
                    'tier' => 1,
                    'compare_price' => 0,
                ];
                app(UpdatePrices::class)->execute($variant, collect($pricing));
            }
        }
    }

    protected function syncStock()
    {
        $this->call('app:sync-forza-refurbished-mac-books');
        $this->call('app:sync-forza-refurbished-iphones');
        $this->call('app:sync-refurbished-direct-imacs');
        $this->call('app:sync-refurbished-direct-mac-books');
        // $this->call('app:sync-refurbished-direct-iphones');
        $this->call('app:sync-renewd-iphones');
    }

    protected function setPrices()
    {
        $products = Product::where('attribute_data->refurbished->value', 'true')->get();

        foreach ($products as $product) {
            foreach ($product->variants as $variant) {
                if (
                    $variant->translateAttribute('renewd_stock') ||
                    $variant->translateAttribute('forza_stock') ||
                    $variant->translateAttribute('refurbisheddirect_stock')
                ) {
                    $prices = [
                        (float) $variant->translateAttribute('renewd_price'),
                        (float) $variant->translateAttribute('forza_price'),
                        (float) $variant->translateAttribute('refurbisheddirect_price'),
                    ];
                    $price = max($prices);

                    if ($price === (float) $variant->translateAttribute('refurbisheddirect_price')) {
                        $price = (($price / 1.21) / (1 - ($this->refurbishedDirectMarginPercentage / 100))) * 1.21;
                    } elseif ($price === (float) $variant->translateAttribute('forza_price')) {
                        $price = ((($price / 1.21) * (1 + ($this->forzaRefurbishedMarginPercentage / 100))) * 1.21);
                    } elseif ($price === (float) $variant->translateAttribute('renewd_price')) {
                        $price = ((($price / 1.21) * (1 + ($this->renewdMarginPercentage / 100))) * 1.21);
                    }

                    // Sync price
                    $pricing['EUR'] = [
                        'id' => $variant->getPrices()->first()->id,
                        'price' => (0.05 * round(($price) * 20)),
                        'currency_id' => 1,
                        'tier' => 1,
                        'compare_price' => 0,
                    ];
                    app(UpdatePrices::class)->execute($variant, collect($pricing));

                    $variant->stock = (int) $variant->translateAttribute('renewd_stock') + (int) $variant->translateAttribute('forza_stock') + (int) $variant->translateAttribute('refurbisheddirect_stock');
                    $variant->save();
                }
            }
        }
    }
}
