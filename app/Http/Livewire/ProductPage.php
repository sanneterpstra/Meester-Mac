<?php

namespace App\Http\Livewire;

use App\Data\ProductData;
use App\Data\ProductOptionData;
use App\Traits\FetchesUrls;
use Illuminate\Support\Collection;
use Livewire\Component;
use Livewire\ComponentConcerns\PerformsRedirects;
use Lunar\FieldTypes\ListField;
use Lunar\FieldTypes\Number;
use Lunar\FieldTypes\Toggle;
use Lunar\Models\Product;
use Lunar\Models\ProductOption;
use Lunar\Models\ProductOptionValue;
use Lunar\Models\ProductVariant;

class ProductPage extends Component
{
    use FetchesUrls, PerformsRedirects;

    /**
     * The selected option values.
     *
     * @var array
     */
    public $selectedOptionValues = [];

    public $availableOptionValues = [];

    public $sku;

    public $variantImages = [];

    public $selectedImage = 0;

    public $slug;

    public function mount($slug)
    {
        $this->slug = $slug;
        $this->url = $this->fetchUrl(
            $slug,
            Product::class,
            [
                'element.variants.basePrices.currency',
                'element.variants.basePrices.priceable',
                'element.productType.mappedAttributes.attributeGroup',
                'element.productType.variantAttributes.attributeGroup',
            ]
        );

        if ($this->sku) {
            $this->selectedOptionValues = $this->product->variants->where('sku', $this->sku)->first()->values->map(function ($value) {
                return [$value->product_option_id => $value->id];
            })
                ->mapWithKeys(fn ($a) => $a)
                ->toArray();
        } else {
            $this->selectedOptionValues = $this->ProductOptions->mapWithKeys(function ($data) {
                return [$data['option']->id => $data['values']->first()->id];
            })->toArray();
        }

        $this->availableOptionValues = $this->getOptions($this->slug, $this->selectedOptionValues);

        $this->variantImages = $this->variant->images->map(function ($image) {
            return $image->getUrl('large');
        });

        if (! count($this->variantImages)) {
            $this->variantImages = $this->images->map(function ($image) {
                return $image->getUrl('large');
            });
        }

        if (! $this->variant) {
            abort(404);
        }
    }

    public function getOptions($product, $selected = [])
    {
        return ProductOptionData::collection(
            ProductOption::withWhereHas(
                'values',
                fn ($q) => $q
                    ->whereHas(
                        'variants',
                        fn ($q) => $q
                            ->whereHas(
                                'product',
                                fn ($q) => $q->whereHas('urls', fn ($q) => $q->where('slug', $product))
                            )
                    )
                    ->with(
                        'variants',
                        fn ($q) => $q
                            ->when(
                                $notNull = array_filter($selected),
                                fn ($q) => $q
                                    ->whereHas('values', fn ($q) => $q
                                        ->whereIn(
                                            app(ProductOptionValue::class)->getTable().'.id',
                                            $notNull
                                        ),
                                        '>=',
                                        count($notNull) === count($selected) ? count($notNull) - 1 : count($notNull)
                                    )
                            )
                            ->whereHas(
                                'product',
                                fn ($q) => $q
                                    ->whereHas('urls', fn ($q) => $q->where('slug', $product))
                            )
                    )
            )->get()
                ->sortBy('position')
                ->values()
                ->all()
        )->toArray();
    }

    public function getBreadcrumbProperty()
    {
        $collection = $this->product->collections->first();

        return collect([
            'name' => $collection->translateAttribute('name'),
            'breadcrumb' => $collection->getBreadcrumbAttribute(),
        ]);
    }

    public function updatedSelectedOptionValues()
    {
        $this->availableOptionValues = $this->getOptions($this->slug, $this->selectedOptionValues);
        $this->sku = $this->variant->sku;
        $this->variantImages = $this->variant->images->map(function ($image) {
            return $image->getUrl('large');
        });

        if (! count($this->variantImages)) {
            $this->variantImages = $this->images->map(function ($image) {
                return $image->getUrl('large');
            });
        }

        $this->selectedImage = 0;
    }

    public function ProductValue($value)
    {
        return ProductData::from($value)->toArray();
    }

    /**
     * Computed property to get variant.
     *
     * @return ProductVariant
     */
    public function getVariantProperty()
    {

        return ProductVariant::search('', function ($meilisearch, string $query, array $options) {
            $options['filter'] = 'product_id = '.$this->product->id;

            foreach ($this->selectedOptionValues as $optionId => $valueId) {
                $options['filter'] .= ' AND options.'.$optionId.' = '.$valueId;
            }

            return $meilisearch->search($query, $options);
        })->first();

        //        return $this->product->variants->first(function ($variant) {
        //            return ! $variant->values->pluck('id')
        //                ->diff(
        //                    collect($this->selectedOptionValues)->values()
        //                )->count();
        //        });
    }

    public function getAttributesListProperty()
    {
        $attributeGroups = collect($this->productAttributes());

        $productAttributeData = $this->product->attribute_data;
        $variantAttributeData = $this->variant->attribute_data;

        $attributes = $attributeGroups->map(function ($attributeValues) use ($productAttributeData, $variantAttributeData) {

            return $attributeValues->map(function ($attribute) use ($productAttributeData, $variantAttributeData) {

                if ($productAttributeData->get($attribute->handle)) {
                    $attribute->value = $productAttributeData->get($attribute->handle);
                    if (! is_null($attribute->value->getValue())) {
                        return $attribute;
                    }
                }

                if ($variantAttributeData->get($attribute->handle)) {
                    $attribute->value = $variantAttributeData->get($attribute->handle);
                    if (! is_null($attribute->value->getValue())) {
                        return $attribute;
                    }
                }
            })->filter(function ($value, $key) {
                // Filter out attributeValues that are null
                return $value != null;
            });
        })->filter(function ($value, $key) {
            // Filter out AttributeGroups that are empty
            return $value->count() != 0;
        });

        return $attributes;
    }

    public function productAttributes()
    {
        $variantAttributes = $this->product->productType->variantAttributes->map(function ($attribute) {
            $attribute->attribute_group_name = $attribute->attributeGroup->translate('name');
            $attribute->attribute_group_position = $attribute->attributeGroup->position;

            return $attribute;
        })->unique('name')->sortBy('attribute_group_position')->groupBy('attribute_group_name');

        $productAttributes = $this->product->mappedAttributes()->map(function ($attribute) {
            $attribute->attribute_group_name = $attribute->attributeGroup->translate('name');
            $attribute->attribute_group_position = $attribute->attributeGroup->position;

            return $attribute;
        })->unique('name')->sortBy('attribute_group_position')->groupBy('attribute_group_name');

        return $productAttributes->union($variantAttributes)->all();
    }

    public function formattedAttributeValue($attributeValue)
    {
        if ($attributeValue instanceof ListField) {
            return implode(', ', $attributeValue->getValue());
        } elseif ($attributeValue instanceof Number) {
            return strval($attributeValue->getValue());
        } elseif ($attributeValue instanceof Toggle) {
            if ($attributeValue->getValue()) {
                return 'Ja';
            } else {
                return 'Nee';
            }
        } else {
            return $attributeValue->getValue();
        }
    }

    /**
     * Computed property to return all available option values.
     *
     * @return Collection
     */
    public function getProductOptionValuesProperty()
    {
        return $this->product->variants->pluck('values')->flatten();
    }

    /**
     * Computed propert to get available product options with values.
     *
     * @return Collection
     */
    public function getProductOptionsProperty()
    {
        return $this->productOptionValues->unique('id')->groupBy('product_option_id')
            ->map(function ($values) {
                return [
                    'option' => $values->first()->option,
                    'values' => $values,
                ];
            })->values()->sortBy('option.position');
    }

    /**
     * Computed property to return product.
     *
     * @return Product
     */
    public function getProductProperty()
    {
        return $this->url->element;
    }

    /**
     * Return all images for the product.
     *
     * @return Collection
     */
    public function getImagesProperty()
    {
        return $this->product->media;
    }

    /**
     * Computed property to return current image.
     *
     * @return string
     */
    public function getImageProperty()
    {
        // if (count($this->variant->images)) {
        //     return $this->variant->images->first();
        // }

        if ($primary = $this->images->first(fn ($media) => $media->getCustomProperty('primary'))) {
            return $primary;
        }

        return $this->images->first();
    }

    public function render()
    {
        return view('livewire.shop.product-page')
            ->layout('layouts.storefront');
    }
}
