<section>
    <x-breadcrumbs>
        {{ Breadcrumbs::render('product', $this->product) }}
    </x-breadcrumbs>

    <div class="mx-auto max-w-screen-xl px-4 py-12 sm:px-6 lg:px-8">
        <div>
            <h1 class="mb-2 text-3xl font-bold tracking-tight text-gray-900 sm:text-4xl">
                {{ $this->product->translateAttribute('name') }}</h1>
            <div>
                @foreach ($this->variant->getOptions() as $option)
                    <span
                        class="inline-flex items-center rounded-md bg-gray-50 px-1.5 py-0.5 text-xs font-medium text-gray-600 ring-1 ring-inset ring-gray-500/10">{{ $option }}</span>
                @endforeach
            </div>
            <div class="flex w-full items-center justify-between border-b border-gray-200 pb-2 lg:justify-end">
            </div>
        </div>
        <div class="grid grid-cols-1 items-start gap-8 border-b border-gray-200 py-8 md:grid-cols-2">
            <div class="relative" x-data="{
                selected: @entangle('selectedImage').defer,
                images: @entangle('variantImages')
            }">
                <img class="rounded-xl object-cover" alt="mountains" :src="images[selected]" />
                <div class="mt-4 grid grid-cols-5 gap-4 sm:grid-cols-5">
                    <template x-for="(image,index) in images" :key="index">
                        <div class="aspect-w-1 aspect-h-1 overflow-hidden rounded-lg border p-2 hover:cursor-pointer"
                            @click="selected = index"
                            :class="{ 'border-blue-500': selected == index, 'border-gray-200': selected != index }">
                            <img class="object-cover" loading="lazy" :src="image" />
                        </div>
                    </template>
                </div>
            </div>
            <div>

                {{-- <p class="mt-1 text-sm text-gray-500">
							{{$this->variant->sku}}
				</p> --}}

                <div class="mt-6">
                    <form class="bg-slate-50 my-4 rounded-xl">
                        <div class="space-y-4">
                            <h3 class="mb-4 text-2xl font-bold">Configuratie:</h3>
                            {{-- {{  $this->variant->attribute_data['also_productpl_id']}} --}}
                            @foreach ($this->availableOptionValues as $option)
                                <fieldset>
                                    <legend class="font-bold text-black">
                                        {{ $option['name'] }}:
                                    </legend>
                                    <div class="mt-2 flex flex-wrap gap-2 uppercase" x-data="{
                                        selectedOption: @entangle('selectedOptionValues'),
                                        selectedValues: []
                                    }"
                                        x-init="selectedValues = Object.values(selectedOption),
                                            $watch('selectedOption', value =>
                                                selectedValues = Object.values(selectedOption)
                                            )">
                                        @foreach ($option['values'] as $value)
                                            <button
                                                class="{{ $value['available'] == false ? 'hidden' : '' }} w-full rounded-lg border px-4 py-2 focus:outline-none focus:ring sm:w-auto"
                                                type="button"
                                                wire:click="$set('selectedOptionValues.{{ $option['id'] }}', {{ $value['id'] }})
												"
                                                :class="{
                                                    'bg-blue-600 border-blue-600 text-white hover:bg-blue-700': selectedValues
                                                        .includes({{ $value['id'] }}),
                                                    'hover:bg-blue-600 hover:text-white': !selectedValues.includes(
                                                        {{ $value['id'] }}),
                                                
                                                }"
                                                @if (!$value['available']) {{ 'disabled' }} @endif>
                                                {{ $value['name'] }}
                                            </button>
                                        @endforeach
                                    </div>
                                </fieldset>
                            @endforeach
                        </div>
                    </form>
                    <div
                        class="fixed bottom-0 left-0 flex w-full justify-between border-t border-gray-200 bg-white p-4 sm:relative sm:block sm:border-none sm:py-4">
                        <div class="flex items-center justify-between sm:mb-4">
                            <x-product-price class="text-xl font-bold sm:text-2xl" :variant="$this->variant" />
                            <div class="hidden items-center justify-items-center space-x-2 sm:flex">
                                @if ($this->variant->stock >= 5)
                                    <span class="inline-block h-4 w-4 rounded-full bg-green-600"></span>
                                    <span class="text-green-600">Ruim op voorraad</span>
                                @elseif($this->variant->stock > 0 && $this->variant->stock < 5)
                                    <span class="inline-block h-4 w-4 rounded-full bg-green-600"></span>
                                    <span class="text-green-600">Minder dan 5 beschikbaar</span>
                                @else
                                    <span class="text-orange-500">Levertijd 3 tot 5 weken</span>
                                @endif
                            </div>
                        </div>
                        <div class="w-full">
                            <livewire:components.add-to-cart :purchasable="$this->variant" :wire:key="$this->variant->id" />
                        </div>
                    </div>
                </div>
            </div>
        </div>
        <div class="grid grid-cols-1 items-start gap-8 border-b border-gray-200 py-8 pb-8 md:grid-cols-2">
            <div>
                <h3 class="mb-4 text-2xl font-bold">Productbeschrijving</h3>
                <div class="space-y-6 text-base text-gray-700">
                    {!! $this->product->translateAttribute('description') !!}
                </div>
            </div>
            <div class="bg-blue-50 p-8">
                {{--            <div class="min-h-[475px] bg-blue-50 bg-[url('http://192.168.0.145/images/mm_aankoopadvies_small_2.svg')] bg-[length:542px_229px] bg-bottom bg-no-repeat p-8 lg:min-h-[450px] xl:bg-right-bottom"> --}}
                <h3 class="mb-4 text-xl font-bold">Aankoopadvies</h3>
                <p>Zie je door de bomen het bos niet meer? Laat je adviseren door Meester Mac. Dan weet je zeker dat
                    je
                    een goede aankoop doet.</p>
                <x-input.link-button class="mt-4" href="#adviesafspraak">Laat je adviseren</x-input.link-button>
            </div>
        </div>
        <div class="mt-8">
            <h3 class="mb-4 text-2xl font-bold">Specificaties</h3>
            @foreach ($this->AttributesList as $attributeGroupName => $attributeValues)
                @unless ($attributeGroupName === 'Details' || $attributeGroupName === 'Distributie')
                    <h3 class="text-lg font-bold">{{ $attributeGroupName }}</h3>
                    <table class="mb-6 min-w-full table-fixed divide-y divide-gray-300">
                        <tbody class="bg-white">

                            @foreach ($attributeValues as $value)
                                <tr class="even:bg-gray-50">
                                    <td class="w-1/3 py-4 pl-4 pr-3 text-sm font-medium text-gray-900 sm:pl-3">
                                        {{ $value->translate('name') }}</td>
                                    <td class="w-2/3 px-3 py-4 text-sm text-gray-500">
                                        {{ $this->formattedAttributeValue($value->value) }}</td>
                                </tr>
                            @endforeach
                        </tbody>
                    </table>
                @endunless
            @endforeach
        </div>
        <div
            class="col-span-3 mt-20 flex flex-col items-stretch justify-stretch bg-blue-50 px-4 sm:px-10 md:max-h-[360px] md:flex-row xl:px-20">
            <div class="grow basis-1/2 flex-col py-4 sm:py-10 xl:py-20">
                <h2 class="text-2xl font-bold sm:text-3xl">Installatiehulp mogelijk</h2>
                <p class="mt-1 text-lg sm:text-xl">Laat je nieuwe apparaat direct goed instellen. Na het afrekenen is
                    het mogelijk installatiehulp aan te vragen. Meester Mac helpt je met het instellen en overzetten van
                    data.</p>
                {{--                <x-input.link-button class="mt-11" href="">Maak een afspraak</x-input.link-button> --}}
            </div>
            <div class="flex w-full basis-1/2 items-end justify-end">
                <img class="h-full w-auto" src="/images/mm_home_illustration.svg" alt="Installatiehulp" />
            </div>
        </div>
    </div>
</section>
