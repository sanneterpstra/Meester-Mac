<!DOCTYPE html>
<html lang="{{ str_replace('_', '-', app()->getLocale()) }}">

<head>
	<meta charset="utf-8">
	<meta
			name="viewport"
			content="width=device-width, initial-scale=1"
	>
	<title>Demo Storefront</title>
	<meta
			name="description"
			content="Example of an ecommerce storefront built with Lunar."
	>
	@vite(['resources/css/app.css', 'resources/js/app.js'])

	<link
			rel="icon"
			href="{{ asset('favicon.svg') }}"
	>
	@livewireStyles
</head>

<body class="antialiased">
<livewire:components.navigation />

<main class="pt-[60px] sm:pt-[80px]">
	{{ $slot }}
</main>

<x-footer />

<x-dialogs.rate />

@livewireScripts

</body>

</html>
