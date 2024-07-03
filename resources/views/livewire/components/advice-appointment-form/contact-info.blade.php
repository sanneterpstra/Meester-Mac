<div class="overflow-hidden">
    @include('livewire.components.help-appointment-form.navigation')
    <div class="bg-white px-4 pb-4 pt-5 sm:p-6 sm:pb-4">
        <div class="grid grid-cols-1 gap-x-6 gap-y-8 sm:grid-cols-6">

            <div class="col-span-full">
                <label class="block text-base font-medium text-gray-700" for="first-name">Voornaam</label>
                <x-input.text id="name" name="name" type="text" autocomplete="given-name"
                    wire:model.lazy="fields.first_name" :error="$errors->has('fields.first_name')" />
                @error('fields.first_name')
                    <p class="mt-2 text-sm text-red-600">{{ $message }}</p>
                @enderror
            </div>

            <div class="col-span-full">
                <label class="block text-base font-medium text-gray-700" for="last-name">Achternaam</label>
                <x-input.text id="last-name" name="lastname" type="text" autocomplete="given-lastname"
                    wire:model.lazy="fields.last_name" :error="$errors->has('fields.last_name')" />
                @error('fields.last_name')
                    <p class="mt-2 text-sm text-red-600">{{ $message }}</p>
                @enderror
            </div>

            <div class="col-span-full">
                <label class="block text-base font-medium text-gray-700" for="email-address">E-mailadres</label>
                <x-input.text id="email-address" name="email" type="email" autocomplete="email"
                    wire:model.lazy="fields.email" :error="$errors->has('fields.email')" />
                @error('fields.email')
                    <p class="mt-2 text-sm text-red-600">{{ $message }}</p>
                @enderror
            </div>

            <div class="col-span-full">
                <label class="block text-base font-medium text-gray-700" for="phone">Telefoonnummer</label>
                <x-input.text id="phone" name="phone" type="tel" autocomplete="phone"
                    wire:model.lazy="fields.phone" :error="$errors->has('fields.phone')" />
                @error('fields.phone')
                    <p class="mt-2 text-sm text-red-600">{{ $message }}</p>
                @enderror
            </div>
        </div>
    </div>
    <div class="bg-gray-50 px-4 py-3 sm:flex sm:flex-row-reverse sm:px-6">
        <x-input.button wire:click="submit">Maak adviesafspraak</x-input.button>
    </div>
</div>
