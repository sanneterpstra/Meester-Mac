<!DOCTYPE html>
<html lang="{{ str_replace('_', '-', app()->getLocale()) }}">

    <head>
        <meta charset="utf-8">
        <meta name="viewport" content="width=device-width, initial-scale=1, maximum-scale=1">
        <title>Demo Storefront</title>
        <meta name="description" content="Example of an ecommerce storefront built with Lunar.">
        @vite(['resources/css/app.css', 'resources/js/app.js'])
        <link href="{{ asset('favicon.svg') }}" rel="icon">
        @livewireStyles
    </head>

    <body class="text-gray-900 antialiased">
        <livewire:components.shop-navigation />
        <x-dialogs.rate />

        <x-dialogs.adviceappointment />

        <main>
            {{ $slot }}
        </main>

        <x-footer />

        @livewireScripts
    </body>

</html>
