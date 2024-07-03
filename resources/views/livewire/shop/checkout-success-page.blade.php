<section class="bg-white">
    <div class="mx-auto max-w-3xl px-4 py-16 sm:py-24 md:px-8 lg:px-8">
        <div class="max-w-2xl">
            <p class="mt-2 text-3xl font-bold text-blue-500 sm:text-5xl">Bedankt voor je bestelling!</p>
            <p class="mt-2 text-base">Je bestelling is ontvangen en wordt zo snel mogelijk verwerkt. Onderstaande
                overzicht is ook naar je e-mailadres gestuurd.</p>

            <x-input.link-button class="mt-8" href="{{ route('shop.view') }}">Ga naar winkel</x-input.link-button>

            <dl class="mt-12 text-sm font-medium">
                <dt class="text-gray-900">Bestelnummer:</dt>
                <dd class="text-indigo-600 mt-2">
                    {{ $order->reference }}
                </dd>
            </dl>
        </div>

        <div class="mt-10 border-t border-gray-200">
            <h2 class="sr-only">Je bestelling</h2>

            <h3 class="sr-only">Producten</h3>
            @foreach ($this->order->lines as $line)
                @if ($line->type !== 'shipping')
                    <div class="flex space-x-6 border-b border-gray-200 py-10">
                        <img class="h-20 w-20 flex-none rounded-lg bg-gray-100 object-cover object-center sm:h-40 sm:w-40"
                            src="{{ $line->purchasable->getThumbnail()->getUrl('medium') }}" />

                        <div class="flex flex-auto flex-col">
                            <div>
                                <h4 class="text-lg font-medium text-gray-900">
                                    <a
                                        href="{{ route('product.view', $line->purchasable->product->defaultUrl->slug . '?sku=' . $line->purchasable->sku) }}">{{ $line->description }}</a>
                                </h4>
                                <p class="mt-2 text-sm text-gray-600">{{ $line->option }}</p>
                            </div>
                            <div class="mt-6 flex flex-1 items-end">
                                <dl class="flex space-x-4 divide-x divide-gray-200 text-sm sm:space-x-6">
                                    <div class="flex">
                                        <dt class="font-medium text-gray-900">Aantal:</dt>
                                        <dd class="ml-2 text-gray-700">{{ $line->unit_quantity }}</dd>
                                    </div>
                                    <div class="flex pl-4 sm:pl-6">
                                        <dt class="font-medium text-gray-900">Prijs:</dt>
                                        <dd class="ml-2 text-gray-700">{{ $line->total->formatted() }}</dd>
                                    </div>
                                </dl>
                            </div>
                        </div>
                    </div>
                @endif
            @endforeach

            <div class="sm:ml-40 sm:pl-6">
                <h3 class="sr-only">Je gegevens</h3>

                <h4 class="sr-only">Adressen</h4>

                <dl class="grid grid-cols-2 gap-x-6 py-10 text-base">
                    @foreach ($this->order->addresses as $address)
                        <div>
                            <dt class="font-medium text-gray-900">
                                {{ $address->type == 'shipping' ? 'Verzendadres' : 'Factuuradres' }}</dt>
                            <dd class="mt-2 text-gray-700">
                                <address class="not-italic">
                                    <span class="block">{{ $address->first_name . ' ' . $address->last_name }}</span>
                                    <span class="block">{{ $address->line_one . ' ' . $address->line_two }}</span>
                                    <span class="block">{{ $address->postcode . ' ' . $address->city }}</span>
                                </address>
                            </dd>
                        </div>
                    @endforeach
                </dl>

                <h4 class="sr-only">Payment</h4>
                <dl class="grid grid-cols-2 gap-x-6 border-t border-gray-200 py-10 text-base">
                    <div>
                        <dt class="font-medium text-gray-900">Betaling</dt>
                        @foreach ($this->order->transactions as $transaction)
                            @if ($transaction->card_type == 'ideal')
                                <dd class="mt-2 text-gray-700">
                                    <p>iDeal</p>
                                    <p>{{ $transaction->meta->details['consumerAccount'] }}</p>
                                </dd>
                            @endif
                        @endforeach
                    </div>
                    <div>
                        <dt class="font-medium text-gray-900">Verzendmethode(s)</dt>
                        @foreach ($this->order->shippingLines as $shippingLine)
                            <dd class="mt-2 text-gray-700">
                                <p>{{ $shippingLine->description }}</p>
                                <p>{{ $shippingLine->total->formatted() }}</p>
                            </dd>
                        @endforeach

                    </div>
                </dl>

                <h3 class="sr-only">Totaaloverzicht</h3>

                <dl class="space-y-6 border-t border-gray-200 pt-10 text-base">
                    <div class="flex justify-between">
                        <dt class="font-medium text-gray-900">Subtotaal</dt>
                        <dd class="text-gray-700">{{ $this->order->sub_total->formatted() }}</dd>
                    </div>

                    <div class="flex justify-between">
                        <dt class="font-medium text-gray-900">Verzending</dt>
                        <dd class="text-gray-700">{{ $this->order->shipping_total->formatted() }}</dd>
                    </div>
                    <div class="flex justify-between">
                        <dt class="font-medium text-gray-900">Totaal</dt>
                        <dd class="text-gray-900">{{ $this->order->total->formatted() }}</dd>
                    </div>
                </dl>
            </div>
        </div>
    </div>
</section>
