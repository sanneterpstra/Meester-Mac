<!DOCTYPE html>
<html lang="{{ str_replace('_', '-', app()->getLocale()) }}">

    <head>
        <meta charset="utf-8">
        <meta name="viewport" content="width=device-width, initial-scale=1">
        <title>Demo Storefront</title>
        <meta name="description" content="Example of an ecommerce storefront built with Lunar.">
        @vite('resources/css/app.css')
        <script defer src="https://unpkg.com/alpinejs@3.x.x/dist/cdn.min.js"></script>
        <link href="{{ asset('favicon.svg') }}" rel="icon">
        @livewireStyles
    </head>

    <body class="text-gray-900 antialiased">
        <header class="sticky top-0 z-20 w-full bg-white" x-data="{ open: false }" x-cloak>
            <div class="border-b border-gray-200">
                <nav class="mx-auto flex max-w-7xl items-center justify-between gap-x-6 p-4 md:px-8 lg:py-7"
                    aria-label="Global">
                    <div class="lg:flex lg:flex-1 lg:items-center">
                        <a class="text-xl font-semibold" href="{{ route('shop.view') }}">
                            <span>Meester Mac / Shop</span>
                        </a>
                    </div>
                    <div class="flex flex-1 items-center justify-end gap-x-6">
                        <livewire:components.cart />
                    </div>
                </nav>
            </div>
        </header>

        <main>
            {{ $slot }}
        </main>

        <x-footer />

        @livewireScripts
    </body>

</html>
