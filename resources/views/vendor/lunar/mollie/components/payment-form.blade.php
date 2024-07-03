<div>
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
