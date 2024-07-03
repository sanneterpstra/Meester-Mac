<div class="sm:relative" x-data="{
    linesVisible: @entangle('linesVisible')
}">
    <button class="group -m-2 flex items-center p-2" x-on:click="linesVisible = !linesVisible">
        <svg class="h-6 w-6 flex-shrink-0 text-gray-400 group-hover:text-gray-500" aria-hidden="true" fill="none"
            stroke-width="1.5" stroke="currentColor" viewBox="0 0 24 24" xmlns="http://www.w3.org/2000/svg">
            <path
                d="M15.75 10.5V6a3.75 3.75 0 10-7.5 0v4.5m11.356-1.993l1.263 12c.07.665-.45 1.243-1.119 1.243H4.25a1.125 1.125 0 01-1.12-1.243l1.264-12A1.125 1.125 0 015.513 7.5h12.974c.576 0 1.059.435 1.119 1.007zM8.625 10.5a.375.375 0 11-.75 0 .375.375 0 01.75 0zm7.5 0a.375.375 0 11-.75 0 .375.375 0 01.75 0z"
                stroke-linecap="round" stroke-linejoin="round" />
        </svg>
        <span class="ml-2 text-sm font-medium text-gray-700 group-hover:text-gray-800">
            @if ($this->cart && $lines)
                {{ count($lines) }}
            @endif
        </span>
        <span class="sr-only">Producten in winkelwagen, bekijk winkelwagen</span>
    </button>

    <div class="absolute inset-x-0 top-auto z-50 mx-auto mt-4 w-screen max-w-sm rounded-xl border border-gray-100 bg-white px-6 py-8 shadow-xl sm:left-auto"
        x-cloak x-on:click.away="linesVisible = false" x-show="linesVisible" x-transition>
        <button class="absolute right-3 top-3 text-gray-500 transition-transform hover:scale-110" type="button"
            aria-label="Close" x-on:click="linesVisible = false">
            <svg class="h-4 w-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"
                xmlns="http://www.w3.org/2000/svg">
                <path d="M6 18L18 6M6 6l12 12" stroke-linecap="round" stroke-linejoin="round" stroke-width="2" />
            </svg>
        </button>

        <div>
            @if ($this->cart)
                @if ($lines)
                    <div class="flow-root">
                        <ul class="-my-4 max-h-96 divide-y divide-gray-100 overflow-y-auto">
                            @foreach ($lines as $index => $line)
                                <li>
                                    <div class="flex py-4" wire:key="line_{{ $line['id'] }}">
                                        <img class="h-16 w-16 rounded object-contain" src="{{ $line['thumbnail'] }}">

                                        <div class="ml-4 flex-1">
                                            <p class="max-w-[20ch] font-medium">
                                                {{ $line['description'] }}
                                            </p>

                                            <span class="mt-1 block text-xs text-gray-500">
                                                {{ $line['options'] }}
                                            </span>

                                            <div class="mt-2 flex items-center">
                                                <input
                                                    class="w-16 rounded-lg border border-gray-100 p-2 text-xs transition-colors hover:border-gray-200"
                                                    type="number" wire:model.lazy="lines.{{ $index }}.quantity"
                                                    min="1" />

                                                <p class="ml-2 text-xs">
                                                    {{ $line['unit_price'] }}
                                                </p>

                                                <button
                                                    class="ml-auto rounded-lg p-2 text-gray-600 transition-colors hover:bg-gray-100 hover:text-gray-700"
                                                    type="button" wire:click="removeLine('{{ $line['id'] }}')">
                                                    <svg class="h-4 w-4" fill="none" stroke="currentColor"
                                                        viewBox="0 0 24 24" xmlns="http://www.w3.org/2000/svg">
                                                        <path
                                                            d="M19 7l-.867 12.142A2 2 0 0116.138 21H7.862a2 2 0 01-1.995-1.858L5 7m5 4v6m4-6v6m1-10V4a1 1 0 00-1-1h-4a1 1 0 00-1 1v3M4 7h16"
                                                            stroke-linecap="round" stroke-linejoin="round"
                                                            stroke-width="2" />
                                                    </svg>
                                                </button>
                                            </div>
                                        </div>
                                    </div>

                                    @if ($errors->get('lines.' . $index . '.quantity'))
                                        <div class="mb-4 rounded bg-red-50 p-2 text-center text-xs font-medium text-red-700"
                                            role="alert">
                                            @foreach ($errors->get('lines.' . $index . '.quantity') as $error)
                                                {{ $error }}
                                            @endforeach
                                        </div>
                                    @endif
                                </li>
                            @endforeach
                        </ul>
                    </div>
                @else
                    <p class="py-4 text-center font-medium text-gray-500">
                        Je winkelwagen is leeg
                    </p>
                @endif

                @if ($lines)
                    <dl class="mt-6 flex flex-wrap border-t border-gray-100 pt-4 text-sm">
                        <dt class="w-1/2 font-medium">
                            Subtotaal
                        </dt>

                        <dd class="w-1/2 text-right">
                            {{ $this->cart->subTotal->formatted() }}
                        </dd>
                    </dl>
                @endif
            @else
                <p class="py-4 text-center font-medium text-gray-500">
                    Je winkelwagen is leeg
                </p>
            @endif
        </div>

        @if ($lines)
            <div class="mt-4 space-y-4 text-center">
                {{--                <button --}}
                {{--                    class="block w-full rounded-lg border border-blue-600 p-3 text-sm font-medium text-blue-800 hover:ring-1 hover:ring-blue-600" --}}
                {{--                    type="button" wire:click="updateLines"> --}}
                {{--                    Winkelwagen bijwerken --}}
                {{--                </button> --}}

                <a class="block w-full rounded-lg bg-blue-600 p-3 text-center font-medium text-white hover:bg-blue-500"
                    href="{{ route('checkout.view') }}">
                    Afrekenen
                </a>

                <a class="inline-block text-sm font-medium text-gray-600 underline hover:text-gray-500"
                    href="{{ route('shop.view') }}">
                    Verder winkelen
                </a>
            </div>
        @endif
    </div>
</div>
