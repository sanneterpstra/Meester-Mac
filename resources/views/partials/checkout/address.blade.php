<form class="rounded-xl border border-gray-100 bg-white" wire:submit.prevent="saveAddress('{{ $type }}')">
    <div class="flex h-16 items-center justify-between border-b border-gray-100 px-6">
        <h3 class="text-lg font-bold">
            {{ $type == 'shipping' ? 'Verzend' : 'Factuur' }}gegevens
        </h3>

        @if ($type == 'shipping' && $step == $currentStep)
            <label class="flex cursor-pointer items-center rounded-lg p-2 hover:bg-gray-50">
                <input class="text-green-600 h-5 w-5 rounded border-gray-100" type="checkbox" value="1"
                    wire:model.defer="shippingIsBilling" />

                <span class="font-base ml-2 text-sm">
                    Gebruik ook voor factuuradres
                </span>
            </label>
        @endif

        @if ($currentStep > $step)
            <button
                class="rounded-lg px-5 py-2 text-base font-medium text-gray-600 hover:bg-gray-100 hover:text-gray-700"
                type="button" wire:click.prevent="$set('currentStep', {{ $step }})">
                Bewerk
            </button>
        @endif
    </div>

    @if ($currentStep >= $step)
        <div class="p-6">
            @if ($step == $currentStep)
                <div class="grid grid-cols-6 gap-4">
                    <x-input.group class="col-span-3" label="Voornaam" :errors="$errors->get($type . '.first_name')" required>
                        <x-input.text wire:model.lazy="{{ $type }}.first_name" />
                    </x-input.group>

                    <x-input.group class="col-span-3" label="Achternaam" :errors="$errors->get($type . '.last_name')" required>
                        <x-input.text wire:model.lazy="{{ $type }}.last_name" />
                    </x-input.group>

                    <x-input.group class="col-span-6" label="Bedrijfsnaam" :errors="$errors->get($type . '.company_name')">
                        <x-input.text wire:model.lazy="{{ $type }}.company_name" />
                    </x-input.group>

                    <x-input.group class="col-span-6 sm:col-span-3" label="Telefoonnummer" :errors="$errors->get($type . '.contact_phone')">
                        <x-input.text wire:model.lazy="{{ $type }}.contact_phone" />
                    </x-input.group>

                    <x-input.group class="col-span-6 sm:col-span-3" label="E-mailadres" :errors="$errors->get($type . '.contact_email')" required>
                        <x-input.text type="email" wire:model.lazy="{{ $type }}.contact_email" />
                    </x-input.group>

                    <div class="col-span-6">
                        <hr class="my-4 h-px border-none bg-gray-100">
                    </div>

                    <x-input.group class="col-span-6 sm:col-span-3" label="Postcode" :errors="$errors->get($type . '.postcode')" required>
                        <x-input.text wire:model.lazy="{{ $type }}.postcode" />
                    </x-input.group>

                    <x-input.group class="col-span-6 sm:col-span-3" label="Huisnummer" :errors="$errors->get($type . '.line_two')" required>
                        <x-input.text wire:model.lazy="{{ $type }}.line_two" />
                    </x-input.group>

                    <x-input.group class="col-span-6 sm:col-span-3" label="Adres" :errors="$errors->get($type . '.line_one')" required>
                        <x-input.text wire:model.lazy="{{ $type }}.line_one" />
                    </x-input.group>

                    <x-input.group class="col-span-6 sm:col-span-3" label="Plaatsnaam" :errors="$errors->get($type . '.city')" required>
                        <x-input.text wire:model.lazy="{{ $type }}.city" />
                    </x-input.group>

                    <x-input.group class="col-span-6 hidden" label="Land" required>
                        <select class="w-full rounded-lg border border-gray-200 p-3 sm:text-base"
                            wire:model.defer="{{ $type }}.country_id">
                            <option value>Selecteer een land</option>
                            @foreach ($this->countries as $country)
                                <option value="{{ $country->id }}" wire:key="country_{{ $country->id }}">
                                    {{ $country->native }}
                                </option>
                            @endforeach
                        </select>
                    </x-input.group>
                </div>
            @elseif($currentStep > $step)
                <dl class="grid grid-cols-1 gap-8 text-base sm:grid-cols-2">
                    <div>
                        <div class="space-y-4">
                            <div>
                                <dt class="font-medium">
                                    Naam
                                </dt>

                                <dd class="mt-0.5">
                                    {{ $this->{$type}->first_name }} {{ $this->{$type}->last_name }}
                                </dd>
                            </div>

                            @if ($this->{$type}->company_name)
                                <div>
                                    <dt class="font-medium">
                                        Bedrijf
                                    </dt>

                                    <dd class="mt-0.5">
                                        {{ $this->{$type}->company_name }}
                                    </dd>
                                </div>
                            @endif

                            @if ($this->{$type}->contact_phone)
                                <div>
                                    <dt class="font-medium">
                                        Telefoonnummer
                                    </dt>

                                    <dd class="mt-0.5">
                                        {{ $this->{$type}->contact_phone }}
                                    </dd>
                                </div>
                            @endif

                            <div>
                                <dt class="font-medium">
                                    E-mailadres
                                </dt>

                                <dd class="mt-0.5">
                                    {{ $this->{$type}->contact_email }}
                                </dd>
                            </div>
                        </div>
                    </div>

                    <div>
                        <dt class="font-medium">
                            Adres
                        </dt>

                        <dd class="mt-0.5">
                            {{ $this->{$type}->line_one }} {{ $this->{$type}->line_two }}<br>
                            {{ $this->{$type}->postcode }} {{ $this->{$type}->city }}<br>
                        </dd>
                    </div>
                </dl>
            @endif

            @if ($step == $currentStep)
                <div class="mt-6 text-right">
                    <button class="rounded-lg bg-blue-600 px-5 py-3 text-base font-medium text-white hover:bg-blue-500"
                        type="submit" wire:key="submit_btn" wire:loading.attr="disabled" wire:target="saveAddress">
                        <span wire:loading.remove wire:target="saveAddress">
                            Volgende stap
                        </span>

                        <span wire:loading wire:target="saveAddress">
                            <span class="inline-flex items-center">
                                <x-icon.loading />
                            </span>
                        </span>
                    </button>
                </div>
            @endif
        </div>

    @endif
</form>
