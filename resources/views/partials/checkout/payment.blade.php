<div class="rounded-xl border border-gray-100 bg-white">
    <div class="flex h-16 items-center border-b border-gray-100 px-6">
        <h3 class="text-lg font-bold">
            Betaling
        </h3>
    </div>

    {{--    @if ($currentStep >= $step) --}}
    <div class="space-y-4 p-6">
        <div class="flex items-center">
            <input class="text-indigo-600 focus:ring-indigo-500 h-4 w-4 rounded border-gray-300" id="term-and-conditions"
                name="term-and-conditions" type="checkbox" wire:model="termsAndConditionsAccepted">
            <div class="ml-2">
                <label class="text-sm font-medium text-gray-900" for="term-and-conditions">Ik ga akkoord met de algemene
                    voorwaarden en ik heb mijn bestelling gecontroleerd en juist ingevuld.</label>
            </div>
        </div>

        <div class="flex items-center">
            <input class="text-indigo-600 focus:ring-indigo-500 h-4 w-4 rounded border-gray-300" id="newsletter"
                name="newsletter" type="checkbox" checked="" wire:model="subscribeToNewsletter">
            <div class="ml-2">
                <label class="text-sm font-medium text-gray-900" for="newsletter">Meld mij aan voor de
                    nieuwsbrief met tips en uitleg</label>
            </div>
        </div>
        @error('termsAndConditionsAccepted')
            <p class="mt-1 text-sm text-red-600">{{ $message }}</p>
        @enderror

        @if (!config('lunar.mollie.specify_payment_methods'))
            <div class="group mb-2 flex cursor-pointer items-center justify-between rounded-lg border p-3 hover:border-blue-500 hover:bg-blue-100"
                wire:click="handleSubmit">
                <div class="flex items-center space-x-4">
                    <span>{{ trans('lunar::mollie.secure_payment_help_text') }}</span>
                </div>
                <button
                    class="flex items-center rounded-lg bg-blue-600 px-5 py-3 font-medium text-white hover:bg-blue-500 disabled:opacity-50"
                    type="submit">
                    <span>{{ trans('lunar::mollie.pay_button') }}</span>
                </button>
            </div>
        @else
            @foreach ($this->getPaymentMethods() as $paymentMethod)
                <div class="group mb-2 flex cursor-pointer items-center justify-between rounded-lg border p-3 hover:border-blue-500 hover:bg-blue-100"
                    wire:click="handleSubmit('{{ $paymentMethod }}')">
                    <div class="flex items-center space-x-4">
                        <img src="{{ $paymentMethod->getImageSrc('svg') }}" alt="{{ $paymentMethod->value }}"
                            width="70" />
                        <span>{{ $paymentMethod->getName() }}</span>
                    </div>
                    <button
                        class="flex items-center rounded-lg bg-white px-5 py-3 font-medium text-white hover:!bg-blue-500 focus:bg-blue-500 group-hover:bg-blue-600"
                        type="submit">
                        <span>{{ trans('lunar::mollie.pay_with_method_button', ['method' => $paymentMethod->getName()]) }}</span>
                    </button>
                </div>
            @endforeach
        @endif

    </div>
    {{--    @endif --}}
</div>
