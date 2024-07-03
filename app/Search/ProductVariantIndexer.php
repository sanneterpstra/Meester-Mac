<?php

namespace App\Search;

use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Model;
use Lunar\Facades\Pricing;
use Lunar\FieldTypes\TranslatedText;
use Lunar\Models\Attribute;
use Lunar\Search\ScoutIndexer;

class ProductVariantIndexer extends ScoutIndexer
{
    public function searchableAs(Model $model): string
    {
        return 'product_variants';
    }

    public function shouldBeSearchable(Model $model): bool
    {
        return true;
    }

    public function makeAllSearchableUsing(Builder $query): Builder
    {
        return $query->with([
            'product',
        ]);
    }

    public function getScoutKey(Model $model): mixed
    {
        return $model->getKey();
    }

    public function getScoutKeyName(Model $model): mixed
    {
        return $model->getKeyName();
    }

    // Simple array of any sortable fields.
    public function getSortableFields(): array
    {
        return [
            'price',
            'created_at',
            'updated_at',
        ];
    }

    // Simple array of any filterable fields.
    public function getFilterableFields(): array
    {
        return [
            '__soft_deleted',
        ];
    }

    // Return an array representing what should be sent to the search service i.e. Algolia
    public function toSearchableArray(Model $model): array
    {
        $data['purchasable'] = $model->purchasable;
        $data['stock'] = $model->stock;
        $data['sku'] = $model->sku;
        $data['price'] = Pricing::for($model)->get()->matched->price->value;
        $data['product_id'] = $model->product_id;

        if ($model->getThumbnail()) {
            $data['thumbnail'] = $model->getThumbnail()->getUrl('small');
        }

        $data['options'] = $model->values->mapWithKeys(function ($value) {
            return [$value->option->id => $value->id];
        });

        foreach ($model->product->attribute_data ?? [] as $field => $value) {
            $attribute = Attribute::where('handle', $field)->where('attribute_type', 'Lunar\Models\Product')->first();

            if ($attribute) {
                if ($attribute->filterable) {
                    if ($value instanceof TranslatedText) {
                        foreach ($value->getValue() as $locale => $text) {
                            $data[$field.'_'.$locale] = $text?->getValue();
                        }
                    } else {
                        $data[$field] = $model->product->translateAttribute($field);
                    }
                }
            }
        }

        foreach ($model->attribute_data ?? [] as $field => $value) {
            $attribute = Attribute::where('handle', $field)->where('attribute_type', 'Lunar\Models\ProductVariant')->first();
            if ($attribute) {
                if ($attribute->filterable) {
                    if ($value instanceof TranslatedText) {
                        foreach ($value->getValue() as $locale => $text) {
                            $data[$field.'_'.$locale] = $text?->getValue();
                        }
                    } else {
                        $data[$field] = $model->translateAttribute($field);
                    }
                }
            }
        }

        if ($model->product->collections()) {
            $data['collections'] = $model->product->collections->pluck('id');
        }

        return array_merge($data, $this->mapSearchableAttributes($model));
    }
}
