<div class="space-y-4">
	<x-hub::input.group :label="'Tracking url'" :error="$errors->first($bind.'.tracking_url')" for="tracking_url" required>
		<x-hub::input.text wire:model.defer="{{ $bind }}.tracking_url" id="tracking_url" required/>
	</x-hub::input.group>
</div>
