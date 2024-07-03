<?php

namespace App\Console\Commands;

use Illuminate\Console\Command;
use Lunar\Models\ProductVariant;

class UpdateProducts extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'app:update-products';

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
        $products = collect(json_decode(file_get_contents('macbookairm2update.json')));

        foreach ($products as $product) {
            ProductVariant::where('product_id', '=', 89)->chunk(200, function ($productvariants) use ($product) {
                foreach ($productvariants as $variant) {

                    if ($variant->translateAttribute('techdata_product_id')) {
                        if ($variant->translateAttribute('techdata_product_id') == $product->new_tdsynnex) {
                            $variant->attribute_data['also_product_id'] = new \Lunar\FieldTypes\Text($product->new_also);
                            $variant->attribute_data['also_stock'] = new \Lunar\FieldTypes\Text('');
                            $variant->attribute_data['also_price'] = new \Lunar\FieldTypes\Text('');
                            $variant->attribute_data['techdata_stock'] = new \Lunar\FieldTypes\Text('');
                            $variant->stock = 0;
                            $variant->save();
                            print_r('Updated: '.$variant->id." \n");
                        }
                    }
                }
            });
        }
    }
}
