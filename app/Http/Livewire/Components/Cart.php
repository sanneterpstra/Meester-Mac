<?php

namespace App\Http\Livewire\Components;

use Illuminate\Support\Collection;
use Livewire\Component;
use Lunar\Facades\CartSession;
use Lunar\Managers\CartManager;

class Cart extends Component
{
    /**
     * The editable cart lines.
     */
    public array $lines;

    public bool $linesVisible = false;

    protected $listeners = [
        'add-to-cart' => 'handleAddToCart',
    ];

    /**
     * {@inheritDoc}
     */
    public function rules()
    {
        return [
            'lines.*.quantity' => 'required|numeric|min:1|max:10',
        ];
    }

    public function messages()
    {
        return [
            'lines.*.quantity.required' => 'Vul in hoeveel je wilt bestellen',
            'lines.*.quantity.min' => 'Je kunt minimaal 1 bestellen',
            'lines.*.quantity.numeric' => 'Vul een getal in',
            'lines.*.quantity.max' => 'Je kunt maximaal 10 bestellen',
        ];
    }

    public function updated($propertyName)
    {
        $this->updateLines();
    }

    /**
     * Update the cart lines.
     *
     * @return void
     */
    public function updateLines()
    {
        $this->validate();

        CartSession::updateLines(
            collect($this->lines)
        );
        $this->mapLines();
        $this->emit('cartUpdated');
    }

    /**
     * Map the cart lines.
     *
     * We want to map out our cart lines like this so we can
     * add some validation rules and make them editable.
     *
     * @return void
     */
    public function mapLines()
    {
        $this->lines = $this->cartLines->map(function ($line) {
            return [
                'id' => $line->id,
                'identifier' => $line->purchasable->getIdentifier(),
                'quantity' => $line->quantity,
                'description' => $line->purchasable->getDescription(),
                'thumbnail' => $line->purchasable->getThumbnail()->getUrl(),
                'option' => $line->purchasable->getOption(),
                'options' => $line->purchasable->getOptions()->implode(' / '),
                'sub_total' => $line->subTotal->formatted(),
                'unit_price' => $line->unitPrice->formatted(),
            ];
        })->toArray();
    }

    /**
     * {@inheritDoc}
     */
    public function mount()
    {
        $this->mapLines();
    }

    /**
     * Get the current cart instance.
     *
     * @return CartManager
     */
    public function getCartProperty()
    {
        return CartSession::current();
    }

    /**
     * Return the cart lines from the cart.
     *
     * @return Collection
     */
    public function getCartLinesProperty()
    {
        return $this->cart->lines ?? collect();
    }

    public function removeLine($id)
    {
        CartSession::remove($id);
        $this->mapLines();
    }

    public function handleAddToCart()
    {
        $this->mapLines();
        $this->linesVisible = true;
    }

    public function render()
    {
        return view('livewire.components.cart');
    }
}
