<div>
    <div class="bg-blue-200 py-18" id="maak-hulp-afspraak">
        <div class="relative mx-auto max-w-5xl sm:px-6">
            <div class="grid lg:grid-cols-3">
                <div class="col-span-1 flex flex-col bg-gray-100 sm:flex">
                    <img src="images/ik.jpg" />
                    <div class="p-18">
                        <h2 class="mb-2 text-xl font-bold">Adres</h2>
                        <p class="mb-8">Willem en Marialaan 36<br />
                            2805AR Gouda</p>

                        <h2 class="mb-2 text-xl font-bold">Contact</h2>
                        <p class="my-2">
                            <object class="w-[97px]" type="image/svg+xml" data="svg-phone.svg"></object>
                            <object class="w-[160px]" type="image/svg+xml" data="svg-email-protection.svg"></object>
                        </p>
                    </div>
                </div>

                <div class="col-span-2 bg-white p-18" x-data="{ open: @entangle('showSuccessMessage') }">
                    <h1 class="text-3xl font-bold">Contact</h1>
                    <p class="mt-3 text-base">Heb je vragen, wil je advies of hulp op afstand? Vul dan het onderstaande
                        formulier in. Ik probeer zo snel mogelijk te reageren.</p>

                    <form wire:submit.prevent="submit" x-show="!open">
                        <x-honeypot livewire-model="extraFields" />

                        <div class="mt-6 grid grid-cols-6 gap-x-6 gap-y-4">
                            <div class="col-span-6 sm:col-span-3">
                                <label class="block text-base text-gray-700" for="first-name">Voornaam:</label>
                                <x-input.text id="last-name" name="lastname" type="text"
                                    autocomplete="given-lastname" wire:model.lazy="fields.first_name"
                                    placeholder="Voornaam" :error="$errors->has('fields.first_name')" />
                                @error('fields.first_name')
                                    <p class="mt-1 text-sm text-red-600">{{ $message }}</p>
                                @enderror
                            </div>

                            <div class="col-span-6 sm:col-span-3">
                                <label class="block text-base text-gray-700" for="last-name">Achternaam:</label>
                                <x-input.text id="last-name" name="lastname" type="text"
                                    autocomplete="given-lastname" wire:model.lazy="fields.last_name"
                                    placeholder="Achternaam" :error="$errors->has('fields.last_name')" />
                                @error('fields.last_name')
                                    <p class="mt-1 text-sm text-red-600">{{ $message }}</p>
                                @enderror
                            </div>

                            <div class="col-span-6 sm:col-span-3">
                                <label class="block text-base text-gray-700" for="email-address">E-mailadres:</label>
                                <x-input.text id="email-address" name="email" type="email" autocomplete="email"
                                    wire:model.lazy="fields.email" placeholder="voorbeeld@voorbeeld.nl"
                                    :error="$errors->has('fields.email')" />
                                @error('fields.email')
                                    <p class="mt-1 text-sm text-red-600">{{ $message }}</p>
                                @enderror
                            </div>

                            <div class="col-span-6 sm:col-span-3">
                                <label class="block text-base text-gray-700" for="phone">Telefoonnummer:</label>
                                <x-input.text id="phone" name="phone" type="tel" autocomplete="phone"
                                    wire:model.lazy="fields.phone" placeholder="06.." :error="$errors->has('fields.phone')" />
                                @error('fields.phone')
                                    <p class="mt-1 text-sm text-red-600">{{ $message }}</p>
                                @enderror
                            </div>

                            <div class="col-span-full">
                                <label class="block text-base text-gray-900" for="date">Bericht:</label>
                                <x-input.textarea id="message" name="message" rows="8"
                                    wire:model.lazy="fields.message" placeholder="Type hier je bericht of vraag"
                                    :error="$errors->has('fields.message')" />
                                @error('fields.message')
                                    <p class="mt-1 text-sm text-red-600">{{ $message }}</p>
                                @enderror
                            </div>
                            <div class="col-span-full flex justify-end">
                                <x-input.button type="submit" wire:loading.attr="disabled">
                                    <span wire:loading.delay.remove wire:target="submit">Verstuur bericht</span>
                                    <span class="min-w-20" wire:loading.delay wire:target="submit">
                                        <svg class="inline h-4 w-4 animate-spin text-white" role="status"
                                            aria-hidden="true" viewBox="0 0 100 101" fill="none"
                                            xmlns="http://www.w3.org/2000/svg">
                                            <path
                                                d="M100 50.5908C100 78.2051 77.6142 100.591 50 100.591C22.3858 100.591 0 78.2051 0 50.5908C0 22.9766 22.3858 0.59082 50 0.59082C77.6142 0.59082 100 22.9766 100 50.5908ZM9.08144 50.5908C9.08144 73.1895 27.4013 91.5094 50 91.5094C72.5987 91.5094 90.9186 73.1895 90.9186 50.5908C90.9186 27.9921 72.5987 9.67226 50 9.67226C27.4013 9.67226 9.08144 27.9921 9.08144 50.5908Z"
                                                fill="#E5E7EB"></path>
                                            <path
                                                d="M93.9676 39.0409C96.393 38.4038 97.8624 35.9116 97.0079 33.5539C95.2932 28.8227 92.871 24.3692 89.8167 20.348C85.8452 15.1192 80.8826 10.7238 75.2124 7.41289C69.5422 4.10194 63.2754 1.94025 56.7698 1.05124C51.7666 0.367541 46.6976 0.446843 41.7345 1.27873C39.2613 1.69328 37.813 4.19778 38.4501 6.62326C39.0873 9.04874 41.5694 10.4717 44.0505 10.1071C47.8511 9.54855 51.7191 9.52689 55.5402 10.0491C60.8642 10.7766 65.9928 12.5457 70.6331 15.2552C75.2735 17.9648 79.3347 21.5619 82.5849 25.841C84.9175 28.9121 86.7997 32.2913 88.1811 35.8758C89.083 38.2158 91.5421 39.6781 93.9676 39.0409Z"
                                                fill="currentColor"></path>
                                        </svg>
                                        Laden..</span></x-input.button>
                            </div>
                        </div>
                    </form>

                    <div x-show="open">
                        <div class="mt-6 w-full bg-green-100 p-6">
                            <div class="flex">

                                <div class="">
                                    <h3 class="text-xl font-bold text-green-800">Bericht succesvol verzonden</h3>
                                    <div class="mt-3 text-green-700">
                                        <p>Bedankt voor je bericht. Je ontvangt zo snel mogelijk een reactie.</p>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>

                </div>
            </div>
        </div>
    </div>
</div>
