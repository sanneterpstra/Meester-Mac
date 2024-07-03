<?php

namespace App\Http\Livewire;

use App\Enums\Sort;
use App\Models\ProductVariant;
use App\Traits\FetchesUrls;
use Http\Client\Exception\HttpException;
use Livewire\Component;
use Livewire\ComponentConcerns\PerformsRedirects;
use Livewire\WithPagination;
use Lunar\Models\Attribute;
use Lunar\Models\Collection;
use Meilisearch\Endpoints\Indexes;

class CollectionPage extends Component
{
    use FetchesUrls,
        PerformsRedirects,
        WithPagination;

    public $collection;

    public $sorteren = Sort::STOCK->value;

    public Sort $sort = Sort::STOCK;

    public $filters = [];

    public $queryString = [
        'sorteren',
        'filters',
    ];

    public $attributes;

    /**
     * {@inheritDoc}
     *
     * @param  string  $slug
     * @return void
     *
     * @throws HttpException
     */
    public function mount($slug)
    {
        $this->url = $this->fetchUrl(
            $slug,
            Collection::class,
        );

        if (! $this->url) {
            abort(404);
        }

        $this->collection = $this->url->element;

        $this->attributes = Attribute::where('filterable', 1)->get();

        $search = ProductVariant::search('', function ($meilisearch, string $query, array $options) {
            $options['facets'] = ['*'];

            return $meilisearch->search($query, $options);
        })->raw();

        $this->filters = collect($search['facetDistribution'])->mapWithKeys(function ($facet, $key) {
            if ($this->attributes->where('handle', '=', $key)->first()) {
                if (array_key_exists($key, $this->filters)) {
                    return [$key => $this->filters[$key]];
                }

                return [$key => null];
            } else {
                return [];
            }
        });

    }

    public function updatedFilters()
    {
        $this->filters = collect($this->filters)->mapWithKeys(function ($value, $key) {
            return (! empty($value)) ? [$key => $value] : [$key => null];
        })->toArray();
    }

    public function updatingFilters()
    {
        $this->resetPage();
    }

    public function updatingSorteren($value)
    {
        $this->sort = Sort::from($value);
        $this->sorteren = $this->sort->value();
    }

    public function paginationView()
    {
        return 'vendor.pagination.tailwind';
    }

    /**
     * Computed property to return the collection.
     *
     * @return Collection
     */
    public function getCollectionProperty()
    {
        return $this->url->element;
    }

    public function getAttributeTitle($handle)
    {
        return $this->attributes->where('handle', $handle)->first()->translate('name');
    }

    public function getFilterValue($valueId)
    {
        return $this->values->firstWhere('id', $valueId)->translate('name');
    }

    public function render()
    {
        $results = $this->getResultsProperty()->paginate(12);
        $rawSearch = $this->getResultsProperty()->raw();

        $attributes = Attribute::all();

        uksort($rawSearch['facetDistribution'], function ($a, $b) use ($attributes) {
            return ($attributes->where('handle', $a)->first()->attributeGroup->position.$attributes->where('handle', $a)->first()->position < $attributes->where('handle', $b)->first()->attributeGroup->position.$attributes->where('handle', $b)->first()->position) ? -1 : 1;
        });

        foreach ($rawSearch['facetDistribution'] as $handle => $values) {
            //            if(count($values) <= 1) {
            //                unset($rawSearch['facetDistribution'][$handle]);
            //                continue;
            //            }

            $isBytesArray = false;
            foreach ($values as $value => $hits) {
                if (strpos($value, 'GB') !== false || strpos($value, 'TB') !== false) {
                    $isBytesArray = true;
                }
            }
            if ($isBytesArray) {
                uksort($values, function ($a, $b) {
                    return $this->convertToBytes($a) <=> $this->convertToBytes($b);
                });
                $rawSearch['facetDistribution'][$handle] = $values;
            }
        }

        return view('livewire.shop.collection-page', [
            'results' => $results,
            'facets' => $rawSearch['facetDistribution'],
        ])->layout('layouts.storefront');
    }

    public function getResultsProperty()
    {

        $result = ProductVariant::search('', function (Indexes $meilisearch, string $query, array $options) {
            $options['sort'] = $this->sort->value();

            $filters = collect($this->filters)->filter(fn ($filter) => ! empty($filter))
                ->map(function ($value, $key) {
                    return '"'.$key.'"'.'="'.$value.'"';
                })
                ->flatten()
                ->join(' AND ');

            $options['filter'] = null;
            if ($filters) {
                $options['filter'] = $filters;
            }

            $options['filter'] .= (isset($options['filter']) ? ' AND ' : '').'"collections" = "'.$this->collection->id.'"';
            $options['filter'] .= ' AND (("stock" >= 1 AND "purchasable" = in_stock) OR "purchasable" = always)';
            $options['facets'] = collect($this->filters)->keys();
            $options['limit'] = 500;

            return $meilisearch->search($query, $options);
        });

        return $result;
    }

    protected function convertToBytes($size)
    {
        preg_match('/(\d+)\s*(TB|GB)/', $size, $matches);
        $value = (int) $matches[1];
        $unit = strtoupper($matches[2]);

        if ($unit === 'TB') {
            return $value * 1024 * 1024 * 1024 * 1024;
        } else { // Assuming GB
            return $value * 1024 * 1024 * 1024;
        }
    }

    protected function getAttributeHandles($model)
    {
        return $model->map(function ($variant) {
            return $variant->attribute_data->filter(function ($value, $handle) {
                if ($this->attributes->whereIn('handle', $handle)->first()) {
                    return $value;
                }
            });
        })
            ->unique()
            ->collapse()
            ->keys()
            ->mapWithKeys(fn ($key) => [$key => []])->toArray();
    }

    protected function getVariants()
    {
        return $this->collection->products->map(function ($product) {
            return $product->variants;
        })->flatten();
    }
}
