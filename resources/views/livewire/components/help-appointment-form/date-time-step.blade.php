<div>
    @include('livewire.components.help-appointment-form.navigation')
    <div class="bg-white px-4 pb-4 pt-5 sm:p-6 sm:pb-4">
        <div class="grid grid-cols-1 gap-x-6 gap-y-8 sm:grid-cols-6">
            <div class="col-span-full" wire:init="loadUnavailabledates">
                @if ($datesAvailable)
                    <label class="block text-base font-medium leading-6 text-gray-900" for="date"> Welke dag komt het
                        beste uit? </label>
                    <x-date-picker class="mt-2" id="date" wire:date="fields.date" :unavailableDates="$unavailableDates"
                        :error="$errors->has('fields.date')" x-on:click="showTime = true" />
                    @error('fields.date')
                        <p class="mt-2 text-sm text-red-600" id="email-error">{{ $message }}</p>
                    @enderror
                @else
                    <svg class="inline h-8 w-8 animate-spin text-white" role="status" aria-hidden="true"
                        viewBox="0 0 100 101" fill="none" xmlns="http://www.w3.org/2000/svg">
                        <path
                            d="M100 50.5908C100 78.2051 77.6142 100.591 50 100.591C22.3858 100.591 0 78.2051 0 50.5908C0 22.9766 22.3858 0.59082 50 0.59082C77.6142 0.59082 100 22.9766 100 50.5908ZM9.08144 50.5908C9.08144 73.1895 27.4013 91.5094 50 91.5094C72.5987 91.5094 90.9186 73.1895 90.9186 50.5908C90.9186 27.9921 72.5987 9.67226 50 9.67226C27.4013 9.67226 9.08144 27.9921 9.08144 50.5908Z"
                            fill="#E5E7EB"></path>
                        <path
                            d="M93.9676 39.0409C96.393 38.4038 97.8624 35.9116 97.0079 33.5539C95.2932 28.8227 92.871 24.3692 89.8167 20.348C85.8452 15.1192 80.8826 10.7238 75.2124 7.41289C69.5422 4.10194 63.2754 1.94025 56.7698 1.05124C51.7666 0.367541 46.6976 0.446843 41.7345 1.27873C39.2613 1.69328 37.813 4.19778 38.4501 6.62326C39.0873 9.04874 41.5694 10.4717 44.0505 10.1071C47.8511 9.54855 51.7191 9.52689 55.5402 10.0491C60.8642 10.7766 65.9928 12.5457 70.6331 15.2552C75.2735 17.9648 79.3347 21.5619 82.5849 25.841C84.9175 28.9121 86.7997 32.2913 88.1811 35.8758C89.083 38.2158 91.5421 39.6781 93.9676 39.0409Z"
                            fill="currentColor"></path>
                    </svg>
                @endif
            </div>

            <div class="col-span-full">
                <p class="block text-base font-medium text-gray-700"> Kies een tijd: </p>
                <fieldset class="@if ($errors->has('fields.time')) {{ 'border-red-500' }} @endif mt-1 border">
                    <div class="relative -space-y-px bg-white">
                        <label
                            class="relative flex cursor-pointer flex-col border-b p-4 focus:outline-none md:pl-4 md:pr-6">
                            <span class="flex items-center">
                                <input class="peer h-4 w-4 border-gray-300 text-blue-800 focus:ring-blue-800"
                                    id="morning" name="time" type="radio" value="10:00"
                                    wire:model="fields.time">
                                <span class="ml-3 font-medium peer-checked:text-blue-900"
                                    id="pricing-plans-0-label">10:00 uur</span>
                            </span>
                        </label>

                        <label class="relative flex cursor-pointer flex-col p-4 focus:outline-none md:pl-4 md:pr-6">
                            <span class="flex items-center">
                                <input class="peer h-4 w-4 border-gray-300 text-blue-800 focus:ring-blue-800"
                                    id="afternoon" name="time" type="radio" value="14:00"
                                    wire:model="fields.time">
                                <span class="ml-3 font-medium peer-checked:text-blue-900"
                                    id="pricing-plans-2-label">14:00 uur</span>
                            </span>
                        </label>
                    </div>
                </fieldset>

                @error('fields.time')
                    <p class="mt-2 text-sm text-red-600">{{ $message }}</p>
                @enderror
            </div>
        </div>
    </div>
    <div class="bg-gray-50 px-4 py-4 sm:flex sm:flex-row-reverse sm:px-6">
        <x-input.button wire:click="submit">Volgende stap</x-input.button>
    </div>
</div>
