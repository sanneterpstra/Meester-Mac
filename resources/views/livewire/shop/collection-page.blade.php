<section x-data="{ filterMenuOpen: false }">
    <div class="fixed bottom-0 z-10 w-full p-4 lg:hidden" x-show="!filterMenuOpen">
        <x-input.button class="mx-auto block text-center" @click="filterMenuOpen = !filterMenuOpen">Filter
        </x-input.button>
    </div>
    <x-breadcrumbs>
        {{ Breadcrumbs::render('collection', $this->collection) }}
    </x-breadcrumbs>

    <div x-show="filterMenuOpen" x-cloak>
        <!--
  Mobile filter dialog

  Off-canvas menu for mobile, show/hide based on off-canvas menu state.
  -->
        <div class="relative z-40 lg:hidden" role="dialog" aria-modal="true">
            <!--
   Off-canvas menu backdrop, show/hide based on off-canvas menu state.

   Entering: "transition-opacity ease-linear duration-300"
 From: "opacity-0"
 To: "opacity-100"
   Leaving: "transition-opacity ease-linear duration-300"
 From: "opacity-100"
 To: "opacity-0"
   -->
            <div class="fixed inset-0 bg-black bg-opacity-25"></div>

            <div class="fixed inset-0 z-40 flex">
                <!--
 Off-canvas menu, show/hide based on off-canvas menu state.

 Entering: "transition ease-in-out duration-300 transform"
 From: "translate-x-full"
 To: "translate-x-0"
 Leaving: "transition ease-in-out duration-300 transform"
 From: "translate-x-0"
 To: "translate-x-full"
 -->
                <div class="relative mr-auto flex h-full w-full max-w-xs flex-col overflow-y-auto bg-white py-4 pb-6 shadow-xl"
                    @click.outside="filterMenuOpen = !filterMenuOpen">
                    <div class="fixed bottom-0 z-20 w-full max-w-xs bg-white p-4">
                        <x-input.button class="w-full text-center" @click="filterMenuOpen = !filterMenuOpen"
                            wire:loading.attr="disabled">
                            <span wire:loading.delay.remove wire:target="filters">Toon {{ $results->count() }}
                                producten</span>
                            <span class="min-w-20" wire:loading.delay wire:target="filters">
                                <svg class="inline h-4 w-4 animate-spin text-white" role="status" aria-hidden="true"
                                    viewBox="0 0 100 101" fill="none" xmlns="http://www.w3.org/2000/svg">
                                    <path
                                        d="M100 50.5908C100 78.2051 77.6142 100.591 50 100.591C22.3858 100.591 0 78.2051 0 50.5908C0 22.9766 22.3858 0.59082 50 0.59082C77.6142 0.59082 100 22.9766 100 50.5908ZM9.08144 50.5908C9.08144 73.1895 27.4013 91.5094 50 91.5094C72.5987 91.5094 90.9186 73.1895 90.9186 50.5908C90.9186 27.9921 72.5987 9.67226 50 9.67226C27.4013 9.67226 9.08144 27.9921 9.08144 50.5908Z"
                                        fill="#E5E7EB"></path>
                                    <path
                                        d="M93.9676 39.0409C96.393 38.4038 97.8624 35.9116 97.0079 33.5539C95.2932 28.8227 92.871 24.3692 89.8167 20.348C85.8452 15.1192 80.8826 10.7238 75.2124 7.41289C69.5422 4.10194 63.2754 1.94025 56.7698 1.05124C51.7666 0.367541 46.6976 0.446843 41.7345 1.27873C39.2613 1.69328 37.813 4.19778 38.4501 6.62326C39.0873 9.04874 41.5694 10.4717 44.0505 10.1071C47.8511 9.54855 51.7191 9.52689 55.5402 10.0491C60.8642 10.7766 65.9928 12.5457 70.6331 15.2552C75.2735 17.9648 79.3347 21.5619 82.5849 25.841C84.9175 28.9121 86.7997 32.2913 88.1811 35.8758C89.083 38.2158 91.5421 39.6781 93.9676 39.0409Z"
                                        fill="currentColor"></path>
                                </svg>
                                Laden..</span>
                        </x-input.button>
                    </div>
                    <div class="pb- flex items-center justify-between px-4">
                        <h2 class="text-lg font-medium text-gray-900">Filters</h2>
                        <button
                            class="relative -mr-2 flex h-10 w-10 items-center justify-center p-2 text-gray-400 hover:text-gray-500"
                            type="button" @click="filterMenuOpen = !filterMenuOpen">
                            <span class="absolute -inset-0.5"></span>
                            <span class="sr-only">Close menu</span>
                            <svg class="h-6 w-6" aria-hidden="true" fill="none" viewBox="0 0 24 24"
                                stroke-width="1.5" stroke="currentColor">
                                <path stroke-linecap="round" stroke-linejoin="round" d="M6 18L18 6M6 6l12 12" />
                            </svg>
                        </button>
                    </div>

                    <!-- Filters -->
                    <form class="mt-4 pb-14">
                        @if ($this->collection->descendants->count() !== 0)
                            <div class="border-t border-gray-200 pb-4 pt-4">
                                <fieldset x-data="{ open: false }">
                                    <legend class="w-full px-2">
                                        <!-- Expand/collapse section button -->
                                        <button
                                            class="flex w-full items-center justify-between p-2 text-gray-400 hover:text-gray-500"
                                            type="button" aria-controls="filter-section-1" aria-expanded="false"
                                            @click="open = ! open">
                                            <span class="text-sm font-medium text-gray-900">Categorieën</span>
                                            <span class="ml-6 flex h-7 items-center">
                                                <!--
                        Expand/collapse icon, toggle classes based on section open state.

                        Open: "-rotate-180", Closed: "rotate-0"
                      -->
                                                <svg class="h-5 w-5 transform" aria-hidden="true"
                                                    :class="{ 'rotate-180': open, 'rotate-0': !open }"
                                                    viewBox="0 0 20 20" fill="currentColor">
                                                    <path fill-rule="evenodd"
                                                        d="M5.23 7.21a.75.75 0 011.06.02L10 11.168l3.71-3.938a.75.75 0 111.08 1.04l-4.25 4.5a.75.75 0 01-1.08 0l-4.25-4.5a.75.75 0 01.02-1.06z"
                                                        clip-rule="evenodd" />
                                                </svg>
                                            </span>
                                        </button>
                                    </legend>
                                    <div class="px-4 pb-2 pt-4" id="filter-section-1" x-show="open">
                                        <div class="space-y-6">
                                            @foreach ($this->collection->descendants as $sub_collection)
                                                <div class="flex items-center">
                                                    <a class="text-base text-gray-600"
                                                        href="{{ route('collection.view', $sub_collection->urls->first()->slug) }}">{{ $sub_collection->translateAttribute('name') }}</a>
                                                </div>
                                            @endforeach
                                        </div>
                                    </div>
                                </fieldset>
                            </div>
                        @endif
                        @if ($results->count())
                            @foreach ($facets as $title => $facet)
                                @if ($facet)
                                    <div class="border-t border-gray-200 pb-4 pt-4">
                                        <fieldset x-data="{ open: false }">
                                            <legend class="w-full px-2">
                                                <!-- Expand/collapse section button -->
                                                <button
                                                    class="flex w-full items-center justify-between p-2 text-gray-400 hover:text-gray-500"
                                                    type="button" aria-controls="filter-section-2"
                                                    aria-expanded="false" @click="open = ! open">
                                                    <span
                                                        class="text-sm font-medium text-gray-900">{{ $this->getAttributeTitle($title) }}</span>
                                                    <span class="ml-6 flex h-7 items-center">
                                                        <!--
                        Expand/collapse icon, toggle classes based on section open state.

                        Open: "-rotate-180", Closed: "rotate-0"
                      -->
                                                        <svg class="h-5 w-5 transform" aria-hidden="true"
                                                            :class="{ 'rotate-180': open, 'rotate-0': !open }"
                                                            viewBox="0 0 20 20" fill="currentColor">
                                                            <path fill-rule="evenodd"
                                                                d="M5.23 7.21a.75.75 0 011.06.02L10 11.168l3.71-3.938a.75.75 0 111.08 1.04l-4.25 4.5a.75.75 0 01-1.08 0l-4.25-4.5a.75.75 0 01.02-1.06z"
                                                                clip-rule="evenodd" />
                                                        </svg>
                                                    </span>
                                                </button>
                                            </legend>
                                            <div class="px-4 pb-2 pt-4" id="filter-section-2" x-show="open">
                                                <div class="space-y-6">
                                                    @foreach ($facet as $value => $count)
                                                        <div class="flex items-center">
                                                            @php
                                                                $id =
                                                                    strtolower($title) .
                                                                    '_' .
                                                                    strtolower($value) .
                                                                    '_' .
                                                                    rand();
                                                            @endphp
                                                            <input
                                                                class="h-4 w-4 rounded border-gray-300 text-blue-600 focus:ring-blue-500"
                                                                id="{{ $id }}" type="checkbox"
                                                                value="{{ $value }}"
                                                                wire:model="filters.{{ $title }}">
                                                            <label
                                                                class="ml-3 text-sm text-gray-600 hover:cursor-pointer"
                                                                for="{{ $id }}">{{ $value == 'true' ? 'Ja' : $value }}
                                                                ({{ $count }})
                                                            </label>
                                                        </div>
                                                    @endforeach
                                                </div>
                                            </div>
                                        </fieldset>
                                    </div>
                                @endif
                            @endforeach
                        @endif
                    </form>
                </div>
            </div>
        </div>
    </div>

    <div class="mx-auto mb-10 mt-12 max-w-7xl px-4 sm:px-6 lg:px-8">
        <h1 class="text-3xl font-bold tracking-tight text-gray-900 sm:text-4xl" id="collectionTitle">
            {{ $collection->translateAttribute('name') }}
        </h1>
        <p class="mt-4">{!! $collection->translateAttribute('description') !!}</p>
    </div>

    @if ($results->count())
        <div class="mx-auto max-w-7xl px-4 sm:px-6 lg:px-8">
            <div class="flex w-full items-center justify-between border-b border-gray-200 pb-2 lg:justify-end">
                <button class="flex items-center gap-2 lg:hidden" type="button"
                    @click="filterMenuOpen = !filterMenuOpen">
                    <span>Filters</span>
                    <svg class="h-5 w-5 text-gray-400" aria-hidden="true" viewBox="0 0 20 20" fill="currentColor">
                        <path fill-rule="evenodd"
                            d="M2.628 1.601C5.028 1.206 7.49 1 10 1s4.973.206 7.372.601a.75.75 0 01.628.74v2.288a2.25 2.25 0 01-.659 1.59l-4.682 4.683a2.25 2.25 0 00-.659 1.59v3.037c0 .684-.31 1.33-.844 1.757l-1.937 1.55A.75.75 0 018 18.25v-5.757a2.25 2.25 0 00-.659-1.591L2.659 6.22A2.25 2.25 0 012 4.629V2.34a.75.75 0 01.628-.74z"
                            clip-rule="evenodd" />
                    </svg>
                </button>

                <div class="relative inline-block text-left" x-data="{ open: false }" x-cloak>
                    <div>
                        <button class="group inline-flex items-center justify-center text-gray-700 hover:text-gray-900"
                            id="menu-button" type="button" aria-expanded="false" aria-haspopup="true"
                            @click="open = ! open">
                            Sorteer op: {{ App\Enums\Sort::from($this->sorteren)->friendlyName() }}
                            <svg class="-mr-1 ml-1 h-5 w-5 flex-shrink-0 text-gray-400 group-hover:text-gray-500"
                                aria-hidden="true" viewBox="0 0 20 20" fill="currentColor">
                                <path fill-rule="evenodd"
                                    d="M5.23 7.21a.75.75 0 011.06.02L10 11.168l3.71-3.938a.75.75 0 111.08 1.04l-4.25 4.5a.75.75 0 01-1.08 0l-4.25-4.5a.75.75 0 01.02-1.06z"
                                    clip-rule="evenodd" />
                            </svg>
                        </button>
                    </div>

                    <!--
                        Dropdown menu, show/hide based on menu state.

                        Entering: "transition ease-out duration-100"
                        From: "transform opacity-0 scale-95"
                        To: "transform opacity-100 scale-100"
                        Leaving: "transition ease-in duration-75"
                        From: "transform opacity-100 scale-100"
                        To: "transform opacity-0 scale-95"
                    -->
                    <div class="absolute right-0 z-10 mt-2 w-40 origin-top-right rounded-md bg-white shadow-2xl ring-1 ring-black ring-opacity-5 focus:outline-none"
                        role="menu" aria-orientation="vertical" aria-labelledby="menu-button" tabindex="-1"
                        x-show="open" x-transition:enter="transition ease-out duration-100"
                        x-transition:enter-start="transform opacity-0 scale-95"
                        x-transition:enter-end="transform opacity-100 scale-100"
                        x-transition:leave="transition ease-out duration-100"
                        x-transition:leave-start="transform opacity-100 scale-100"
                        x-transition:leave-end="transform opacity-10 scale-95">
                        <div class="py-1" role="none">
                            <!--
                                Active: "bg-gray-100", Not Active: ""

                                Selected: "font-medium text-gray-900", Not Selected: "text-gray-500"
                            -->
                            @foreach (App\Enums\Sort::cases() as $case)
                                <div class="block flex items-center px-4 py-2">
                                    <input class="text-indigo-600 focus:ring-indigo-600 h-4 w-4 border-gray-300"
                                        id="{{ $case->value }}" type="radio" value="{{ $case }}"
                                        wire:model="sorteren" @click="open = ! open" />
                                    <label class="ml-3 block font-medium leading-6 text-gray-900"
                                        for="{{ $case->value }}">{{ $case->friendlyName() }}</label>
                                </div>
                            @endforeach
                        </div>
                    </div>
                </div>
            </div>

            <section class="pb-24 pt-6" aria-labelledby="products-heading">
                <h2 class="sr-only" id="products-heading">Products</h2>

                <div class="grid grid-cols-1 gap-x-8 lg:grid-cols-4">
                    <!-- Filters -->
                    <div class="hidden lg:col-span-1 lg:block">
                        @if ($this->collection->descendants->count() !== 0)
                            <div class="border-b border-gray-200 py-6">
                                <h3 class="font-medium text-gray-900">Categorieën</h3>
                                <div class="pt-4" id="filter-section-0">
                                    <div class="space-y-2">
                                        @foreach ($this->collection->children->sortBy('_lft') as $sub_collection)
                                            <div class="flex items-center">
                                                <a class="text-base text-sm text-gray-600 hover:cursor-pointer"
                                                    href="{{ route('collection.view', $sub_collection->urls->first()->slug) }}">{{ $sub_collection->translateAttribute('name') }}</a>
                                            </div>
                                        @endforeach
                                    </div>
                                </div>
                            </div>
                        @endif

                        @if ($results->count())
                            @foreach ($facets as $title => $facet)
                                @if ($facet)
                                    <div class="border-b border-gray-200 py-6">
                                        <h3 class="font-medium text-gray-900">
                                            {{ $this->getAttributeTitle($title) }}
                                        </h3>

                                        <div class="pt-4" id="filter-section-0">
                                            <div class="space-y-2">
                                                @foreach ($facet as $value => $count)
                                                    <div class="flex items-center">
                                                        @php
                                                            $id =
                                                                strtolower($title) .
                                                                '_' .
                                                                strtolower($value) .
                                                                '_' .
                                                                rand();
                                                        @endphp
                                                        <input
                                                            class="h-4 w-4 rounded border-gray-300 text-blue-600 focus:ring-blue-500"
                                                            id="{{ $id }}" type="checkbox"
                                                            value="{{ $value }}"
                                                            wire:model="filters.{{ $title }}">
                                                        <label class="ml-3 text-sm text-gray-600 hover:cursor-pointer"
                                                            for="{{ $id }}">{{ $value == 'true' ? 'Ja' : $value }}
                                                            ({{ $count }})
                                                        </label>
                                                    </div>
                                                @endforeach
                                            </div>
                                        </div>
                                    </div>
                                @endif
                            @endforeach
                        @endif
                    </div>

                    <!-- Product grid -->
                    <div
                        class="col-span-1 grid grid-cols-1 gap-y-4 sm:grid-cols-2 sm:gap-x-6 sm:gap-y-10 lg:col-span-3 lg:grid-cols-3 lg:gap-x-8">
                        @foreach ($results as $variant)
                            @if ($loop->iteration == 4)
                                <div
                                    class="col-span-1 flex flex-col bg-blue-50 px-4 sm:col-span-2 sm:col-span-2 sm:flex-row sm:px-14 lg:col-span-3 lg:col-span-3">
                                    <div class="py-4 sm:py-14">
                                        <h2 class="text-2xl font-bold">Hulp nodig bij je aankoop?</h2>
                                        <p>Laat je adviseren. Dan ben je zeker van je zaak.</p>
                                        <x-input.link-button class="mt-4" href="#adviesafspraak">Laat je adviseren
                                        </x-input.link-button>
                                    </div>
                                    <div>
                                        <img class="h-full w-full" src="/images/mm_aankoopadvies_small.svg"
                                            alt="Aankoopadvies">
                                    </div>
                                </div>
                            @endif
                            <x-product-variant-card :productVariant="$variant" />
                        @endforeach
                        <div class="mt-8 sm:col-span-2 lg:col-span-3">
                            {{ $results->links() }}
                        </div>
                    </div>
                </div>
            </section>
        </div>
    @else
        <div class="mx-auto max-w-7xl px-4 pb-24 sm:px-6 lg:px-8">
            <div class="mb-4 bg-blue-50 p-4">
                <div class="flex">
                    <div class="flex-shrink-0">
                        <svg class="h-5 w-5 text-blue-400" aria-hidden="true" viewBox="0 0 20 20"
                            fill="currentColor">
                            <path fill-rule="evenodd"
                                d="M18 10a8 8 0 11-16 0 8 8 0 0116 0zm-7-4a1 1 0 11-2 0 1 1 0 012 0zM9 9a.75.75 0 000 1.5h.253a.25.25 0 01.244.304l-.459 2.066A1.75 1.75 0 0010.747 15H11a.75.75 0 000-1.5h-.253a.25.25 0 01-.244-.304l.459-2.066A1.75 1.75 0 009.253 9H9z"
                                clip-rule="evenodd" />
                        </svg>
                    </div>
                    <div class="ml-3">
                        <h3 class="text-base font-medium text-blue-800">
                            Er zijn helaas geen resultaten gevonden.</h3>
                    </div>
                </div>
            </div>
        </div>
    @endif
</section>
