<section class="p-4 bg-white rounded-lg shadow">
    <header class="flex items-center justify-between">
        <strong class="text-gray-700">
            {{ $this->getSlotTitle() }}
        </strong>

        @if($showTrackingInfoEdit == false)
            <button
                class="px-4 py-2 text-xs font-bold text-gray-700 bg-gray-100 border border-transparent rounded hover:border-gray-100 hover:bg-gray-50"
                type="button"
                wire:click.prevent="$set('showTrackingInfoEdit', true)"
            >
              {{ __('adminhub::global.edit') }}
            </button>
        @endif
    </header>

    @if(!empty($this->meta))
        <a class="text-sky-500 underline" href="{{ $this->meta['tracking_url'] }}">{{ $this->meta['tracking_url'] }}</a>
    @endif

    <x-hub::slideover wire:model="showTrackingInfoEdit" form="saveTrackingInfo">
        @include('hub.partials.forms.trackingnumber', [
            'bind' => 'meta',
        ])

        <x-slot name="footer">
            <x-hub::button wire:click.prevent="$set('showTrackingInfoEdit', false)" theme="gray">
                {{ __('adminhub::global.cancel') }}
            </x-hub::button>

            <x-hub::button type="submit">
                Save tracking URL
            </x-hub::button>
        </x-slot>
    </x-hub::slideover>
</section>
