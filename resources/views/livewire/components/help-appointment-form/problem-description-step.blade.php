<div class="h-full flex flex-col items-stretch">
	@include('livewire.components.help-appointment-form.navigation')
	<div class="bg-white px-4 pb-4 pt-5 sm:p-6 sm:pb-4">
		<div class=" grid grid-cols-1 gap-x-6 gap-y-8 sm:grid-cols-6">
			<div class="col-span-full">
				<label for="date" class="block text-base font-medium leading-6 text-gray-900"> Wat is het
					probleem? </label>
				<x-input.textarea id="problem" name="problem" rows="16" wire:model.debounce.500ms="fields.problem"
								  placeholder="Probeer hier kort en bondig het probleem te beschrijven."
								  :error="$errors->has('fields.problem')" />
				@error('fields.problem')
				<p class="mt-2 text-sm text-red-600" id="email-error">{{$message}}</p>
				@enderror
			</div>
		</div>
	</div>
	<div class="bg-gray-50 px-4 py-3 sm:flex sm:flex-row-reverse sm:px-6">
		<x-input.button wire:click="submit">Volgende stap</x-input.button>
	</div>
</div>