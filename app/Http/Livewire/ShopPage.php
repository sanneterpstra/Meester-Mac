<?php

namespace App\Http\Livewire;

use App\Traits\FetchesUrls;
use Illuminate\Support\Facades\DB;
use Livewire\Component;
use Livewire\ComponentConcerns\PerformsRedirects;
use Lunar\Models\CollectionGroup;
use Lunar\Models\Order;
use Lunar\Models\OrderLine;
use Lunar\Models\ProductVariant;

class ShopPage extends Component
{
    use FetchesUrls,
        PerformsRedirects;

    public $popular_macs = null;

    public $nav_products = null;

    public function mount()
    {
        $collectionGroup = CollectionGroup::where('handle', 'main')->first();
        $this->nav_products = collect(
            $collectionGroup->collections
        );
    }

    public function getTopSellingProductsProperty()
    {
        $orderTable = (new Order())->getTable();
        $orderLineTable = (new OrderLine())->getTable();
        $variantsTable = (new ProductVariant())->getTable();

        return OrderLine::select([
            'purchasable_type',
            'purchasable_id',
            DB::RAW('COUNT(*) as count'),
        ])->join(
            $orderTable,
            'order_id',
            '=',
            "{$orderTable}.id"
        )->whereBetween("{$orderTable}.placed_at", [
            now()->parse(now()->subDays(14)->format('Y-m-d')),
            now()->parse(now()->addDays(1)->format('Y-m-d')),
        ])->join($variantsTable, function ($join) use ($variantsTable, $orderLineTable) {
            $join->on("{$variantsTable}.id", '=', "{$orderLineTable}.purchasable_id")
                ->where('purchasable_type', '=', ProductVariant::class);
        })->groupBy('purchasable_type', 'purchasable_id')
            ->orderBy('count', 'desc')
            ->take(4)->get();
    }

    public function ProductOptions($product)
    {
        return $this->productOptionValues($product)->unique('id')->groupBy('product_option_id')
            ->map(function ($values) {
                return [
                    'option' => $values->first()->option,
                    'values' => $values,
                ];
            })->values();
    }

    public function ProductOptionValues($product)
    {
        return $product->variants->pluck('values')->flatten();
    }

    public function render()
    {
        return view('livewire.shop.shop-page')
            ->layout('layouts.storefront');
    }
}
