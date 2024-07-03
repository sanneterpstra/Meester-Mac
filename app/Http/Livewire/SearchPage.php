<?php

namespace App\Http\Livewire;

use Livewire\Component;
use Livewire\WithPagination;
use Lunar\Models\Product;

class SearchPage extends Component
{
    use WithPagination;

    /**
     * {@inheritDoc}
     */
    protected $queryString = [
        'term',
        'filters',
    ];

    /**
     * The search term.
     */
    public ?string $term = null;

    public ?array $filters = [];

    /**
     * Return the search results.
     *
     * @return \Illuminate\Pagination\LengthAwarePaginator
     */
    public function getResultsProperty()
    {
        return Product::search($this->term, function ($meilisearch, string $query, array $options) {
            return $meilisearch->search($query, $options);
        })->query(fn ($q) => $q
            ->with('variants', fn ($q) => $q
                ->when($notNull = array_filter($this->filters), fn ($q) => $q
                    ->whereHas('values', fn ($q) => $q
                        ->whereIn(
                            app(\Lunar\Models\ProductOptionValue::class)->getTable().'.id', $notNull)
                    )
                )
            )
        )->paginate(50);
    }

    public function render()
    {
        return view('livewire.shop.search-page')
            ->layout('layouts.storefront');
    }
}
