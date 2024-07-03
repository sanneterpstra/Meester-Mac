<?php

namespace App\Console\Commands;

use Illuminate\Console\Command;
use Illuminate\Support\Str;
use Lunar\FieldTypes\Text;
use Lunar\FieldTypes\TranslatedText;
use Lunar\Models\Attribute;
use Lunar\Models\Brand;
use Lunar\Models\Channel;
use Lunar\Models\CustomerGroup;
use Lunar\Models\Product;
use Lunar\Models\ProductOption;
use Lunar\Models\ProductOptionValue;
use Lunar\Models\ProductType;
use Lunar\Models\ProductVariant;

class ImportProducts extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'app:import-products';

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
        $products = collect(json_decode(file_get_contents('')));

        foreach ($products as $product) {
            $productDB = Product::where('attribute_data->name->value->nl', $product->name)->first();

            if (! $productDB) {
                $productDB = $this->createProduct($product);
            }

            foreach ($product->attribute_groups as $attribute_group) {
                foreach ($attribute_group->attributes as $handle => $attributeValue) {
                    print_r($handle.' > ');
                    print_r($attributeValue);
                    print_r("\n");
                    $attributeType = Attribute::where('handle', $handle)->first()->type;
                    $productDB->attribute_data[$handle] = new $attributeType($attributeValue);
                }
            }
            $productDB->save();

            $channel = Channel::where('handle', 'webwinkel')->first();
            $productDB->scheduleChannel($channel);

            $customerGroup = CustomerGroup::where('handle', 'consumenten')->first();
            $productDB->scheduleCustomerGroup($customerGroup);

            $variantImages = [];

            foreach ($product->images as $key => $image) {
                if (filter_var($image, FILTER_VALIDATE_URL)) {
                    $primary = ($key === 0) ? true : '0';

                    $productDB->addMediaFromUrl($image)
                        ->withCustomProperties([
                            'caption' => $product->name,
                            'primary' => $primary,
                            'position' => $key + 1,
                        ])
                        ->toMediaCollection('images');
                } else {
                    foreach ($image->images as $key => $variantImageUrl) {
                        $primary = ($key === 0) ? true : '0';

                        $variantImage = $productDB->addMediaFromUrl($variantImageUrl)
                            ->withCustomProperties([
                                'caption' => $product->name,
                                'primary' => $primary,
                                'position' => $key + 1,
                            ])
                            ->toMediaCollection('images');

                        $variantImages[$image->kleur][] = $variantImage;
                    }
                }
            }

            foreach ($product->variants as $key => $variant) {

                $variantDB = ProductVariant::create([
                    'product_id' => $productDB->id,
                    'tax_class_id' => 1,
                    'sku' => $productDB->sku.'-'.$key,
                    'attribute_data' => [],
                ]);

                foreach ($product->dimensions as $type => $value) {
                    switch ($type) {
                        case 'length_value':
                        case 'height_value':
                        case 'width_value':
                            $variantDB->$type = $value;
                            break;
                        case 'weight_value':
                            $variantDB->$type = $value;
                            $variantDB->weight_unit = 'kg';
                            break;
                    }
                }

                // Add variant attributes
                foreach ($variant->attributes as $handle => $attributeValue) {
                    echo $handle.' => '.$attributeValue."\n";
                    $attributeType = Attribute::where('handle', $handle)->first()->type;
                    $variantDB->attribute_data[$handle] = new $attributeType($attributeValue);
                }
                $variantDB->save();

                // Attach variant options
                foreach ($variant->options as $handle => $optionValue) {
                    $optionDB = ProductOption::where('handle', $handle)->first();
                    $optionValueDB = ProductOptionValue::where('product_option_id', $optionDB->id)
                        ->where('name->nl', $optionValue)
                        ->first();
                    $variantDB->values()->attach($optionValueDB);

                    if ($handle == 'kleur' && array_key_exists($optionValue, $variantImages)) {
                        foreach ($variantImages[$optionValue] as $key => $media) {
                            $primary = ($key === 0) ? true : '0';
                            $variantDB->images()->attach($media, [
                                'primary' => $primary,
                            ]);
                        }
                    }
                }

                $variantDB->sku = Str::slug($product->name.'-'.$variantDB->getOptions()->join('-'));
                $variantDB->purchasable = $variant->purchasability;
                $variantDB->attribute_data['also_product_id'] = new \Lunar\FieldTypes\Text($variant->also);
                $variantDB->attribute_data['techdata_product_id'] = new \Lunar\FieldTypes\Text($variant->tdsynnex);
                $variantDB->prices()->create([
                    'price' => 100,
                    'currency_id' => 1,
                ]);
                $variantDB->save();
            }
        }
    }

    protected function createProduct($product)
    {
        $productType = ProductType::where('name', $product->product_type)->first();
        $brand = Brand::where('name', $product->brand)->first();

        return Product::create([
            'product_type_id' => $productType->id,
            'status' => 'published',
            'brand_id' => $brand->id,
            'sku' => Str::slug($product->name),
            'attribute_data' => [
                'name' => new TranslatedText(collect([
                    'nl' => new Text($product->name),
                ])),
                'description' => new Text($product->description),
            ],
        ]);
    }
}
