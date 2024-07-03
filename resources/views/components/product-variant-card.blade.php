@props(['productVariant'])

<div class="group relative flex flex-col overflow-hidden rounded-lg border border-gray-200 bg-white">
    <div class="aspect-h-4 aspect-w-3 sm:aspect-none bg-white p-6 group-hover:opacity-75">
        <img class="object-contain object-center sm:h-full sm:w-full"
            src="{{ $productVariant->getThumbnail('medium')->getUrl('medium') }}"
            alt="Eight shirts arranged on table in black, olive, grey, blue, white, red, mustard, and green.">
    </div>
    <div class="grow">
        <div class="flex flex-1 flex-col space-y-4 p-6">
            <h3 class="text-xl font-bold text-gray-900">
                <a href="{{ route('product.view', [$productVariant->product->defaultUrl->slug, $productVariant->sku]) }}">
                    <span class="absolute inset-0" aria-hidden="true"></span>
                    {{ $productVariant->product->translateAttribute('name') }}
                </a>
            </h3>

            @foreach ($productVariant->values->sortBy('product_option_id') as $value)
                <div class="flex flex-1 flex-col justify-end">
                    <span
                        class="text-xs font-bold uppercase text-gray-400">{{ $value->option->translate('name') }}</span>
                    <p class="text-sm text-black">{{ $value->translate('name') }}</p>
                </div>
            @endforeach
        </div>
    </div>
    <div class="bg-slate-50 flex flex-row p-6">
        <div class="grow">
            <x-product-price class="font-bold text-black" :variant="$productVariant" />

            <div class="flex items-center justify-items-center space-x-2">
                {!! $productVariant->stock > 0
                    ? '<svg xmlns="http://www.w3.org/2000/svg" viewBox="0 0 24 24" fill="currentColor" class="w-4 h-4 text-green-700 inline">
                                        <path d="M3.375 4.5C2.339 4.5 1.5 5.34 1.5 6.375V13.5h12V6.375c0-1.036-.84-1.875-1.875-1.875h-8.25zM13.5 15h-12v2.625c0 1.035.84 1.875 1.875 1.875h.375a3 3 0 116 0h3a.75.75 0 00.75-.75V15z" />
                                        <path d="M8.25 19.5a1.5 1.5 0 10-3 0 1.5 1.5 0 003 0zM15.75 6.75a.75.75 0 00-.75.75v11.25c0 .087.015.17.042.248a3 3 0 015.958.464c.853-.175 1.522-.935 1.464-1.883a18.659 18.659 0 00-3.732-10.104 1.837 1.837 0 00-1.47-.725H15.75z" />
                                        <path d="M19.5 19.5a1.5 1.5 0 10-3 0 1.5 1.5 0 003 0z" />
                                    </svg>
                                    <p class="text-green-700 text-sm ">Op voorraad</p>'
                    : '<svg xmlns="http://www.w3.org/2000/svg" viewBox="0 0 24 24" fill="currentColor" class="w-4 h-4 text-orange-500 inline">
                                        <path d="M3.375 4.5C2.339 4.5 1.5 5.34 1.5 6.375V13.5h12V6.375c0-1.036-.84-1.875-1.875-1.875h-8.25zM13.5 15h-12v2.625c0 1.035.84 1.875 1.875 1.875h.375a3 3 0 116 0h3a.75.75 0 00.75-.75V15z" />
                                        <path d="M8.25 19.5a1.5 1.5 0 10-3 0 1.5 1.5 0 003 0zM15.75 6.75a.75.75 0 00-.75.75v11.25c0 .087.015.17.042.248a3 3 0 015.958.464c.853-.175 1.522-.935 1.464-1.883a18.659 18.659 0 00-3.732-10.104 1.837 1.837 0 00-1.47-.725H15.75z" />
                                        <path d="M19.5 19.5a1.5 1.5 0 10-3 0 1.5 1.5 0 003 0z" />
                                    </svg>
                                    <p class="text-orange-500 text-sm">2 tot 3 weken</p>' !!}
            </div>
        </div>
        <a class="rounded-md bg-blue-600 px-6 py-3 font-semibold text-white hover:bg-blue-500 focus-visible:outline focus-visible:outline-2 focus-visible:outline-offset-2 focus-visible:outline-blue-600"
            href="#cart">
            +
        </a>
    </div>
</div>
