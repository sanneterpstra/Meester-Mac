<form class="rounded-xl border border-gray-100 bg-white" wire:submit.prevent="saveShippingOption">
    <div class="flex h-16 items-center justify-between border-b border-gray-100 px-6">
        <h3 class="text-lg font-bold">
            Verzendopties
        </h3>

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
            @if ($currentStep == $step)
                <div class="grid grid-cols-1 gap-4 sm:grid-cols-2">
                    @foreach ($this->shippingOptions as $option)
                        <div>
                            <input class="peer hidden" id="{{ $option->getIdentifier() }}" name="shippingOption"
                                type="radio" value="{{ $option->getIdentifier() }}"
                                wire:model.defer="chosenShipping" />

                            <label
                                class="flex cursor-pointer items-center justify-between rounded-lg border border-gray-100 p-4 text-base font-medium shadow-sm hover:bg-gray-50 peer-checked:border-blue-500 peer-checked:ring-1 peer-checked:ring-blue-500"
                                for="{{ $option->getIdentifier() }}">
                                <p>
                                    {{ $option->getDescription() }}
                                </p>

                                <p>
                                    {{ $option->getPrice()->formatted() }}
                                </p>
                            </label>
                        </div>
                    @endforeach
                </div>

                @if ($errors->has('chosenShipping'))
                    <p class="p-4 text-base text-red-500">
                        {{ $errors->first('chosenShipping') }}
                    </p>
                @endif
            @elseif($currentStep > $step && $this->shippingOption)
                <dl class="flex max-w-xs flex-wrap text-base">
                    <dt class="w-1/2 font-medium">
                        {{ $this->shippingOption->getDescription() }}
                    </dt>

                    <dd class="w-1/2 text-right">
                        {{ $this->shippingOption->getPrice()->formatted() }}
                    </dd>
                </dl>
            @endif

            @if ($step == $currentStep)
                <div class="mt-6 text-right">
                    <button class="rounded-lg bg-blue-600 px-5 py-3 text-base font-medium text-white hover:bg-blue-500"
                        type="submit" wire:key="shipping_submit_btn">
                        <span wire:loading.remove.delay wire:target="saveShippingOption">
                            Volgende stap
                        </span>
                        <span wire:loading.delay wire:target="saveShippingOption">
                            <svg class="h-5 w-5 animate-spin text-white" xmlns="http://www.w3.org/2000/svg"
                                fill="none" viewBox="0 0 24 24">
                                <circle class="opacity-25" cx="12" cy="12" r="10" stroke="currentColor"
                                    stroke-width="4"></circle>
                                <path class="opacity-75" fill="currentColor"
                                    d="M4 12a8 8 0 018-8V0C5.373 0 0 5.373 0 12h4zm2 5.291A7.962 7.962 0 014 12H0c0 3.042 1.135 5.824 3 7.938l3-2.647z">
                                </path>
                            </svg>
                        </span>
                    </button>
                </div>
            @endif
        </div>
    @endif
</form>
