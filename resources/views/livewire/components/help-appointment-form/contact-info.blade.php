<div class="overflow-hidden">
	@include('livewire.components.help-appointment-form.navigation')
	<div class="bg-white px-4 pb-4 pt-5 sm:p-6 sm:pb-4">
		<div class=" grid grid-cols-1 gap-x-6 gap-y-8 sm:grid-cols-6">
			<div class="col-span-6 sm:col-span-3">
				<label for="first-name" class="block text-base font-medium text-gray-700">Voornaam</label>
				<x-input.text id="name" type="text" name="name" autocomplete="given-name"
							  wire:model.lazy="fields.first_name" :error="$errors->has('fields.first_name')" />
				@error('fields.first_name')
				<p class="mt-2 text-sm text-red-600">{{$message}}</p>
				@enderror
			</div>

			<div class="col-span-6 sm:col-span-3">
				<label for="last-name" class="block text-base font-medium text-gray-700">Achternaam</label>
				<x-input.text id="last-name" type="text" name="lastname" autocomplete="given-lastname"
							  wire:model.lazy="fields.last_name" :error="$errors->has('fields.last_name')" />
				@error('fields.last_name')
				<p class="mt-2 text-sm text-red-600">{{$message}}</p>
				@enderror
			</div>

			<div class="col-span-6 sm:col-span-3">
				<label for="email-address" class="block text-base font-medium text-gray-700">E-mailadres</label>
				<x-input.text id="email-address" type="email" name="email" autocomplete="email"
							  wire:model.lazy="fields.email" :error="$errors->has('fields.email')" />
				@error('fields.email')
				<p class="mt-2 text-sm text-red-600">{{$message}}</p>
				@enderror
			</div>

			<div class="col-span-6 sm:col-span-3">
				<label for="phone" class="block text-base font-medium text-gray-700">Telefoonnummer</label>
				<x-input.text id="phone" type="tel" name="phone" autocomplete="phone" wire:model.lazy="fields.phone"
							  :error="$errors->has('fields.phone')" />
				@error('fields.phone')
				<p class="mt-2 text-sm text-red-600">{{$message}}</p>
				@enderror
			</div>

			<div class="col-span-3">
				<label for="postal-code" class="block text-base font-medium text-gray-700">Postcode</label>
				<x-input.text id="postal-code" type="text" name="postalcode" autocomplete="postalcode"
							  wire:model.lazy="fields.postalcode" :error="$errors->has('fields.postalcode')" />
				@error('fields.postalcode')
				<p class="mt-2 text-sm text-red-600">{{$message}}</p>
				@enderror
			</div>

			<div class="col-span-3">
				<label for="house-number" class="block text-base font-medium text-gray-700">Huisnummer</label>
				<x-input.text id="house-number" type="text" name="housenumber" wire:model.lazy="fields.housenumber"
							  :error="$errors->has('fields.housenumber')" />
				@error('fields.housenumber')
				<p class="mt-2 text-sm text-red-600">{{$message}}</p>
				@enderror
			</div>

			<div class="col-span-6 sm:col-span-3">
				<label for="address" class="block text-base font-medium text-gray-700">Straatnaam</label>
				<x-input.text id="address" type="text" name="address" autocomplete="address-line1"
							  wire:model.lazy="fields.address" :error="$errors->has('fields.address')" />
				@error('fields.address')
				<p class="mt-2 text-sm text-red-600">{{$message}}</p>
				@enderror
			</div>

			<div class="col-span-6 sm:col-span-3">
				<label for="city" class="block text-base font-medium text-gray-700">Plaats</label>
				<x-input.text id="city" type="text" name="city" autocomplete="city" wire:model.lazy="fields.city"
							  :error="$errors->has('fields.city')" />
				@error('fields.city')
				<p class="mt-2 text-sm text-red-600">{{$message}}</p>
				@enderror
			</div>
		</div>
	</div>
	<div class="bg-gray-50 px-4 py-3 sm:flex sm:flex-row-reverse sm:px-6">
		<x-input.button wire:click="submit">Maak hulpafspraak</x-input.button>
	</div>
</div>