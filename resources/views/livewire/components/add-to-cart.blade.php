<div class="w-full">
    <div class="flex gap-4">
        <div>
            <label class="sr-only" for="quantity">
                Quantity
            </label>
            <input
                class="no-spinner hidden w-16 rounded-lg border border-gray-100 px-1 py-4 text-center text-sm transition sm:inline md:text-base"
                id="quantity" type="number" value="1" min="1" wire:model="quantity" />
        </div>

        <button
            class="bg-green-500 hover:bg-green-600 w-full rounded-lg px-6 py-4 text-center text-sm font-medium text-white md:text-base"
            type="submit" wire:click.prevent="addToCart">
            In winkelwagen
        </button>
    </div>

    @if ($errors->has('quantity'))
        <div class="mt-4 rounded bg-red-50 p-2 text-center text-xs font-medium text-red-700" role="alert">
            @foreach ($errors->get('quantity') as $error)
                {{ $error }}
            @endforeach
        </div>
    @endif
</div>
