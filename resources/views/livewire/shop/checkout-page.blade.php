<div>
    <div class="mx-auto max-w-screen-xl px-4 py-12 sm:px-6 lg:px-8">
        <h1 class="mb-2 border-b border-gray-200 pb-2 text-3xl font-bold tracking-tight text-gray-900 sm:text-4xl">
            Afrekenen</h1>
        @if (session()->has('message'))
            <div class="{{ session('message')['type'] == 'error' ? 'bg-red-50' : 'bg-blue-50' }} mb-4 p-4">
                <div class="flex">
                    <div class="flex-shrink-0">
                        <svg class="{{ session('message')['type'] == 'error' ? 'text-red-400' : 'text-blue-400' }} h-5 w-5"
                            aria-hidden="true" viewBox="0 0 20 20" fill="currentColor">
                            <path fill-rule="evenodd"
                                d="M18 10a8 8 0 11-16 0 8 8 0 0116 0zm-7-4a1 1 0 11-2 0 1 1 0 012 0zM9 9a.75.75 0 000 1.5h.253a.25.25 0 01.244.304l-.459 2.066A1.75 1.75 0 0010.747 15H11a.75.75 0 000-1.5h-.253a.25.25 0 01-.244-.304l.459-2.066A1.75 1.75 0 009.253 9H9z"
                                clip-rule="evenodd" />
                        </svg>
                    </div>
                    <div class="ml-3">
                        <h3
                            class="{{ session('message')['type'] == 'error' ? 'text-red-800' : 'text-blue-800' }} text-base font-medium">
                            {{ session('message')['message'] }}</h3>
                    </div>
                </div>
            </div>
        @endif
        <div class="grid grid-cols-1 gap-8 py-8 lg:grid-cols-3 lg:items-start">
            <div
                class="space-y-4 rounded-xl border border-gray-100 bg-white px-6 py-8 lg:sticky lg:top-8 lg:order-last">
                <h3 class="font-medium">
                    Winkelmand
                </h3>

                <div class="flow-root">
                    <div class="-my-4 divide-y divide-gray-100">
                        @foreach ($cart->lines as $line)
                            <div class="flex items-center py-4" wire:key="cart_line_{{ $line->id }}">
                                <img class="h-16 w-16 rounded object-contain"
                                    src="{{ $line->purchasable->getThumbnail()->getUrl() }}" />

                                <div class="ml-4 flex-1">
                                    <p class="max-w-[35ch] text-sm font-medium">
                                        {{ $line->quantity }}x {{ $line->purchasable->getDescription() }}
                                    </p>
                                    <p class="mt-1 block text-xs text-gray-500">
                                        {{ $line->purchasable->getOptions()->implode(' / ') }}</p>
                                </div>

                            </div>
                        @endforeach
                    </div>
                </div>

                <div class="flow-root">
                    <dl class="-my-4 divide-y divide-gray-100 text-sm">
                        {{-- <div class="flex flex-wrap py-4">
							<dt class="w-1/2 font-medium">
								Subtotaal
							</dt>

							<dd class="w-1/2 text-right">
								{{$cart->subTotal->formatted()}}
						</dd>
				</div> --}}

                        @if ($this->shippingOption)
                            <div class="flex flex-wrap py-4">
                                <dt class="w-1/2 font-medium">
                                    {{ $this->shippingOption->getDescription() }}
                                </dt>

                                <dd class="w-1/2 text-right">
                                    {{ $this->shippingOption->getPrice()->formatted() }}
                                </dd>
                            </div>
                        @endif

                        @foreach ($cart->taxBreakdown->amounts as $tax)
                            <div class="flex flex-wrap py-4">
                                <dt class="w-1/2 font-medium">
                                    {{ $tax->description }}
                                </dt>

                                <dd class="w-1/2 text-right">
                                    {{ $tax->price->formatted() }}
                                </dd>
                            </div>
                        @endforeach

                        <div class="flex flex-wrap py-4">
                            <dt class="w-1/2 font-bold">
                                Totaalbedrag
                            </dt>

                            <dd class="w-1/2 text-right font-bold">
                                {{ $cart->total->formatted() }}
                            </dd>
                        </div>
                    </dl>
                </div>
            </div>

            <div class="space-y-6 lg:col-span-2">
                @include('partials.checkout.address', [
                    'type' => 'shipping',
                    'step' => $steps['shipping_address'],
                ])

                @include('partials.checkout.shipping_option', [
                    'step' => $steps['shipping_option'],
                ])

                @include('partials.checkout.address', [
                    'type' => 'billing',
                    'step' => $steps['billing_address'],
                ])

                @include('partials.checkout.payment', [
                    'step' => $steps['payment'],
                ])
            </div>
        </div>
    </div>
</div>
