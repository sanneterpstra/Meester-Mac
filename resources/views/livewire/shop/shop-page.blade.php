<div>
    <div>
        <div class="relative bg-white">
            <div class="mx-auto grid max-w-7xl px-4 py-8 sm:px-6 md:px-8 lg:grid-cols-2 lg:py-37">
                <div>
                    <div class="mx-auto max-w-lg lg:mx-0">
                        <h1 class="text-3xl font-bold sm:text-4xl">Meester Mac verkoopt Apple</h1>
                        <p class="mt-2 text-xl leading-8 text-gray-600 sm:text-2xl">Nieuw en refurbished.</p>
                        <div class="mt-8 flex items-center gap-x-4 sm:mt-14 sm:gap-x-6">
                            <a class="rounded-md bg-blue-600 px-6 py-3 font-semibold text-white hover:bg-blue-500 focus-visible:outline focus-visible:outline-2 focus-visible:outline-offset-2 focus-visible:outline-blue-600"
                                href="{{ route('collection.view', 'laptop') }}">Bekijk laptop's</a>
                            <a class="rounded-md bg-blue-100 px-6 py-3 text-blue-900 hover:bg-blue-500 focus-visible:outline focus-visible:outline-2 focus-visible:outline-offset-2 focus-visible:outline-blue-600"
                                href="{{ route('collection.view', 'iphone') }}">Bekijk iPhones</a>
                        </div>
                    </div>
                </div>
                <div class="relative mt-16 sm:mt-0">
                    <img class="w-full" src="/images/Apple-Mac-Family-Apple-Silicon_big.jpg.large.png" alt="">
                </div>
            </div>
        </div>
    </div>
    <div class="bg-gray-100">
        <div class="mx-auto max-w-7xl px-4 py-18 lg:px-8">

            <h2 class="mb-10 text-center text-2xl font-bold sm:text-3xl">Bekijk alle producten</h2>
            <div
                class="mx-auto grid max-w-md grid-cols-2 gap-8 md:max-w-3xl md:grid-cols-3 lg:max-w-4xl lg:grid-cols-6 xl:mx-0 xl:max-w-none">
                @foreach ($this->nav_products as $collection)
                    <a class="rounded-3xl bg-white p-8"
                        href="{{ route('collection.view', $collection->defaultUrl->slug) }}">
                        <div class="">
                            <img class="mx-auto h-20 w-auto mix-blend-multiply"
                                src="{{ $collection->thumbnail ? $collection->thumbnail->getUrl() : '' }}">
                        </div>
                        <div class="text-center">
                            <p class="font-semibold text-black">{{ $collection->translateAttribute('name') }}</p>
                        </div>
                    </a>
                @endforeach
            </div>
            <div
                class="mx-w-md mdLjustify-stretch mx-auto mt-8 flex flex-col overflow-hidden rounded-3xl bg-white md:flex-row md:justify-between">
                <div class="p-8 text-center md:p-12 md:text-left lg:p-16 xl:p-20">
                    <h2 class="text-2xl font-bold lg:text-3xl">Of laat je adviseren</h2>
                    <p class="mb-12 text-lg lg:text-xl">Dan heb je zekerheid</p>
                    <x-input.link-button href="#adviesafspraak">Plan adviesgesprek in</x-input.link-button>
                </div>
                <div class="flex grow items-end">
                    <img class="w-full pt-2" src="/images/mm_aankoopadvies_wide.svg" alt="Aankoop advies">
                </div>
            </div>
        </div>
    </div>

    <div class="mx-auto max-w-7xl lg:px-8">
        <div class="mx-auto max-w-7xl px-4 py-18 lg:px-8">

            <h2 class="mb-10 text-center text-2xl font-bold sm:text-3xl">Welke gebruiker ben jij?</h2>
            <div class="grid grid-cols-1 gap-16 lg:grid-cols-3">
                <div>
                    <h2 class="text-lg font-bold sm:text-xl">Internetter</h2>
                    <p class="mt-4 text-gray-700">Bekijk de beste producten om te surfen op het internet, te
                        mailen/whatsappen en videos te kijken. Het hoeft niet groot en zwaar te zijn, als het maar
                        werkt.</p>
                    <x-input.link-button href="">Alles voor de internetter</x-input.link-button>
                </div>
                <div>
                    <h2 class="text-lg font-bold sm:text-xl">Bewaarder</h2>
                    <p class="mt-4 text-gray-700">De cloud is niet voor iedereen een oplossing om alle bestanden in te
                        bewaren. Maar dan moet er natuurlijk wel genoeg ruimte zijn om alle bestanden en foto's te
                        kunnen bewaren.</p>
                    <x-input.link-button href="{{ route('collection.view', 'refurbished') }}">Alles voor de
                        bewaarder
                    </x-input.link-button>
                </div>
                <div>
                    <h2 class="text-lg font-bold sm:text-xl">Maker</h2>
                    <p class="mt-4 text-gray-700">Voor alle grafisch ontwerpers, video-editors en 3D tekenaars onder
                        ons. Een selectie van krachtige apparaten met genoeg capaciteit voor al jouw creativiteit.</p>
                    <x-input.link-button href="{{ route('collection.view', 'refurbished') }}">Alles voor de
                        maker
                    </x-input.link-button>
                </div>
            </div>
        </div>
    </div>

    <x-blocks.appointment-form />
</div>
