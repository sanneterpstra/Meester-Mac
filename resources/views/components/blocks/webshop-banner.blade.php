<div class="mx-auto max-w-7xl py-18 px-4 sm:px-8">
	<div class="flex flex-col-reverse lg:flex-row">
		<div class="basis-1/3">
			<h3 class="text-2xl lg:text-3xl font-bold">Webshop: <br />Nieuw en refurbished</h3>
			<p class="mt-3">Meester Mac biedt sinds kort een webshop aan voor nieuwe en refurbished MacBooks, iMacs, Mac
				Mini's, Mac Studio's of iPhones.</p>
			<x-input.link-button
					href="{{ route('shop.view') }}"
					class="hidden sm:inline-block mb-2 mt-8 "
			>Bekijk de webshop
			</x-input.link-button>
		</div>
		<div class="basis-2/3 flex self-center justify-center mb-8">
			<img
					src="images/Apple-Mac-Family-Apple-Silicon_big.jpg.large.png"
					class="max-h-[200px]"
			/>
		</div>
	</div>
	<x-input.link-button
			href="{{ route('shop.view') }}"
			class="sm:hidden mb-2 mt-8 "
	>Bekijk de webshop
	</x-input.link-button>
</div>
