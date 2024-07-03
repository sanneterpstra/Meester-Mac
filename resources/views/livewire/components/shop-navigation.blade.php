<div class="bg-blue-500">
    <div class="mx-auto flex max-w-7xl items-center justify-between gap-x-6 px-4 py-1 lg:px-8">
        <a class="text-sm text-white" href="{{ route('home.view') }}">Persoonlijke Apple-hulp nodig?
            Maak een afspraak</a>
    </div>
</div>
<header class="sticky top-0 z-20 w-full bg-white" x-data="{ open: false }" x-cloak>
    <div class="border-b border-gray-200">
        <nav class="mx-auto flex max-w-7xl items-center justify-between gap-x-6 p-4 md:px-8 lg:py-0" aria-label="Global">
            <div class="lg:flex lg:flex-1 lg:items-center">
                <a class="text-xl font-semibold" href="{{ route('shop.view') }}">
                    <span>Meester Mac / Shop</span>
                </a>
            </div>
            <div class="hidden h-full lg:flex">
                <div class="inset-x-0 bottom-0 px-4">
                    <div class="flex h-full justify-center">
                        <div class="flex px-6 py-7">
                            <div class="relative flex">
                                <a class="text-base text-gray-900" href="{{ route('shop.view') }}">Shop</a>
                            </div>
                        </div>
                        @foreach ($this->shop_collections as $collection)
                            <div class="flex px-6 py-7" x-data="{ open: false }" @mouseenter="open = true"
                                @mouseleave="open = false">
                                <div class="relative flex">
                                    <a class="text-base"
                                        href="{{ route('collection.view', $collection->defaultUrl->slug) }}">
                                        {{ $collection->translateAttribute('name') }}</p>
                                    </a>
                                </div>
                                <div class="absolute inset-x-0 top-full bg-white p-6" x-show="open">
                                    <!-- Presentational element used to render the bottom shadow, if we put the shadow on the actual panel it pokes out the top, so we use this shorter element to hide the top of the shadow -->
                                    <div class="absolute inset-0 top-1/2 bg-white shadow" aria-hidden="true"></div>

                                    <div class="relative bg-white">
                                        <div class="mx-auto max-w-7xl px-8">
                                            <div class="flex justify-center space-x-14">
                                                @foreach ($collection->children as $sub_collection)
                                                    <div class="group relative text-center">
                                                        <div class="aspect-h-1 aspect-w-1 group-hover:opacity-75">
                                                            @if ($sub_collection->thumbnail)
                                                                <img class="mx-auto max-h-20 object-cover object-center"
                                                                    src="{{ $sub_collection->thumbnail->getUrl() }}">
                                                            @endif
                                                        </div>
                                                        <a class="mt-4 block"
                                                            href="{{ route('collection.view', $sub_collection->urls->first()->slug) }}">
                                                            <span class="absolute inset-0 z-10"
                                                                aria-hidden="true"></span>
                                                            <span
                                                                class="font-bold">{{ $sub_collection->translateAttribute('name') }}</span>
                                                            <div class="text-gray-500">
                                                                {!! $sub_collection->translateAttribute('description') !!}
                                                            </div>
                                                        </a>
                                                    </div>
                                                @endforeach
                                            </div>
                                        </div>
                                    </div>
                                </div>
                                <!-- <ul class="border-slate-200 absolute left-1/2 top-full z-10 min-w-[240px] origin-top-right -translate-x-1/2 rounded-lg border bg-white p-2 shadow-xl"> -->
                            </div>
                        @endforeach
                    </div>
                </div>
            </div>
            <div class="flex flex-1 items-center justify-end gap-x-6">
                <livewire:components.cart />
            </div>
            <div class="flex lg:hidden">
                <button class="-m-2.5 inline-flex items-center justify-center rounded-md p-2.5 text-gray-700"
                    type="button" @click="open = ! open">
                    <span class="sr-only">Open het menu</span>
                    <svg class="h-6 w-6" aria-hidden="true" fill="none" viewBox="0 0 24 24" stroke-width="1.5"
                        stroke="currentColor">
                        <path stroke-linecap="round" stroke-linejoin="round"
                            d="M3.75 6.75h16.5M3.75 12h16.5m-16.5 5.25h16.5" />
                    </svg>
                </button>
            </div>
        </nav>
    </div>
    <!-- Mobile menu, show/hide based on menu open state. -->
    <div role="dialog" aria-modal="true" x-show="open">
        <!-- Background backdrop, show/hide based on slide-over state. -->
        <div class="fixed inset-0 z-10 bg-black opacity-25"></div>
        <div
            class="fixed inset-y-0 right-0 z-10 w-full overflow-y-auto bg-white sm:max-w-sm sm:ring-1 sm:ring-gray-900/10">

            <div class="flex items-center justify-between gap-x-6 border-b border-gray-200 p-4">
                <a class="text-xl font-semibold" href="{{ route('shop.view') }}">
                    <span>Meester Mac / Shop</span>
                </a>
                <button class="-m-2.5 rounded-md p-2.5 text-gray-700" type="button" @click="open = ! open">
                    <span class="sr-only">Close menu</span>
                    <svg class="h-6 w-6" aria-hidden="true" fill="none" viewBox="0 0 24 24" stroke-width="1.5"
                        stroke="currentColor">
                        <path stroke-linecap="round" stroke-linejoin="round" d="M6 18L18 6M6 6l12 12" />
                    </svg>
                </button>
            </div>
            <div class="mt-2 flow-root px-4">
                <div class="divide-y divide-gray-500/10">
                    <div class="space-y-2">
                        @foreach ($this->shop_collections as $collection)
                            <div x-data="{ open: false }">
                                <button
                                    class="flex w-full items-center justify-between py-2 text-left text-base font-semibold text-gray-900"
                                    href="#" @click="open = ! open">
                                    <span class="grow">{{ $collection->translateAttribute('name') }}</span>
                                    <svg class="-mr-1 h-5 w-5 text-gray-400" aria-hidden="true" viewBox="0 0 20 20"
                                        fill="currentColor">
                                        <path fill-rule="evenodd"
                                            d="M5.23 7.21a.75.75 0 011.06.02L10 11.168l3.71-3.938a.75.75 0 111.08 1.04l-4.25 4.5a.75.75 0 01-1.08 0l-4.25-4.5a.75.75 0 01.02-1.06z"
                                            clip-rule="evenodd" />
                                    </svg>
                                </button>
                                <div x-show="open">
                                    <a class="block grow px-3 py-2 text-base text-gray-900 hover:bg-gray-50"
                                        href="{{ route('collection.view', $collection->defaultUrl->slug) }}">
                                        Alle {{ $collection->translateAttribute('name') }}s</p>
                                    </a>
                                    @foreach ($collection->children as $sub_collection)
                                        <a class="block grow px-3 py-2 text-base text-gray-900 hover:bg-gray-50"
                                            href="{{ route('collection.view', $sub_collection->urls->first()->slug) }}">
                                            {{ $sub_collection->translateAttribute('name') }}
                                        </a>
                                    @endforeach
                                </div>
                            </div>
                        @endforeach
                        <div class="mt-2 border-t border-gray-200">
                            <a class="block py-2 text-base font-semibold leading-7 text-gray-900"
                                href="{{ route('home.view') }}">Persoonlijke hulp
                            </a>
                            <a class="block py-2 text-base font-semibold leading-7 text-gray-900"
                                href="{{ route('about.view') }}">Over mij
                            </a>
                            <a class="block py-2 text-base font-semibold leading-7 text-gray-900"
                                href="{{ route('contact.view') }}">Contact
                            </a>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
</header>
