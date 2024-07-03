<header x-data="{ open: false }" x-cloak>
    <div class="fixed z-20 w-full border-b border-gray-200 bg-white">
        <nav class="mx-auto flex max-w-7xl items-center justify-between gap-x-6 p-4 md:px-8" aria-label="Global">
            <div class="flex lg:flex-1">
                <a class="text-xl font-semibold" href="{{ route('home.view') }}">
                    <span>Meester Mac</span>
                </a>
            </div>
            <div class="hidden lg:flex lg:gap-x-8">
                <a class="text-base text-gray-900" href="{{ route('home.view') }}">Home</a>
                <a class="text-base text-gray-900" href="{{ route('about.view') }}">Over</a>
                <a class="text-base text-gray-900" href="#tarieven">Tarieven</a>
                <a class="text-base text-gray-900" href="{{ route('contact.view') }}">Contact</a>
            </div>
            <div class="hidden flex-1 items-center justify-end gap-x-6 text-sm sm:flex md:text-base">
                <x-input.link-button-border href="{{ route('shop.view') }}">Bekijk de webshop
                </x-input.link-button-border>
            </div>
            <div class="flex lg:hidden">
                <button class="-m-2.5 inline-flex items-center justify-center rounded-md p-2.5 text-gray-700"
                    type="button" @click="open = ! open">
                    <span class="sr-only">Open het menu</span>
                    <!--
      Icon when menu is closed.
      Menu open: "hidden", Menu closed: "block"
     -->
                    <svg class="h-6 w-6" aria-hidden="true" x-show="!open" fill="none" viewBox="0 0 24 24"
                        stroke-width="1.5" stroke="currentColor">
                        <path stroke-linecap="round" stroke-linejoin="round"
                            d="M3.75 6.75h16.5M3.75 12h16.5m-16.5 5.25h16.5" />
                    </svg>
                    <!--
      Icon when menu is open.
      Menu open: "block", Menu closed: "hidden"
     -->
                    <svg class="h-6 w-6" aria-hidden="true" x-show="open" fill="none" viewBox="0 0 24 24"
                        stroke-width="1.5" stroke="currentColor">
                        <path stroke-linecap="round" stroke-linejoin="round" d="M6 18L18 6M6 6l12 12" />
                    </svg>
                </button>
            </div>
        </nav>
    </div>
    <!-- Mobile menu, show/hide based on menu open state. -->
    <div role="dialog" aria-modal="true" x-show="open">
        <!-- Background backdrop, show/hide based on slide-over state. -->
        <div class="fixed inset-0 z-0 z-10 bg-black opacity-15 backdrop-blur-xl"></div>
        <div class="fixed inset-y-0 right-0 top-[60px] z-10 w-full overflow-y-auto bg-white sm:top-[76px] sm:max-w-sm md:top-[81px]"
            x-on:click.outside="open = false">
            <div class="divide-y divide-gray-500/10">
                <a class="block p-4" href="{{ route('home.view') }}">Home</a>
                <a class="block p-4" href="{{ route('about.view') }}">Over</a>
                <a class="block p-4" href="#tarieven">Tarieven</a>
                <a class="block p-4" href="{{ route('contact.view') }}">Contact</a>
                <x-input.link-button-border class="mx-4 mt-4 block text-center md:hidden"
                    href="{{ route('shop.view') }}">Bekijk
                    de webshop
                </x-input.link-button-border>
            </div>
        </div>
    </div>
    </div>
</header>
