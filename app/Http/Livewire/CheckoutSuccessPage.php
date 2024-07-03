<?php

namespace App\Http\Livewire;

use App\Mail\OrderPlaced;
use Illuminate\Support\Facades\Mail;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Str;
use Livewire\Component;
use Lunar\Facades\CartSession;
use Lunar\Models\Cart;
use Lunar\Models\Order;

class CheckoutSuccessPage extends Component
{
    public ?Cart $cart;

    public Order $order;

    public function mount()
    {
        $this->cart = CartSession::current();

        if (! $this->cart || ! $this->cart->completedOrder) {
            $this->redirect(route('shop.view'));

            return;
        }

        $this->order = $this->cart->completedOrder;

        if ($this->order->isPlaced()) {
            $mailable = new OrderPlaced($this->order);

            Mail::to($this->order->billingAddress->contact_email)->queue($mailable);
            $storedPath = 'orders/activity/'.Str::random().'.html';

            $storedMailer = Storage::put(
                $storedPath,
                $mailable->render()
            );

            activity()
                ->performedOn($this->order)
                ->event('email-notification')
                ->withProperties([
                    'template' => $storedPath,
                    'email' => $this->order->billingAddress->contact_email,
                    'mailer' => 'Order Placed',
                ])->log('email-notification');
        }

        CartSession::forget();
    }

    public function render()
    {
        return view('livewire.shop.checkout-success-page')
            ->layout('layouts.storefront');
    }
}
