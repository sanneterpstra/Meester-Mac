<footer class="bg-gray-50 p-4 pt-8 md:px-8 md:pt-18">
    <div class="mx-auto max-w-7xl">
        <div class="grid grid-cols-1 gap-x-8 gap-y-16 lg:grid-cols-2">
            <nav class="order-2 md:order-1">
                <ul class="grid grid-cols-2 gap-8 sm:grid-cols-3">
                    <li>
                        <div class="font-display text-base font-semibold text-black">Shop</div>
                        <ul class="text-neutral-700 mt-4 text-base">
                            <li class="mt-4">
                                <a class="hover:text-neutral-950 transition"
                                    href="{{ route('collection.view', 'desktop') }}">Desktops</a>
                            </li>
                            <li class="mt-4">
                                <a class="hover:text-neutral-950 transition"
                                    href="{{ route('collection.view', 'laptop') }}">Laptops</a>
                            </li>
                            <li class="mt-4">
                                <a class="hover:text-neutral-950 transition"
                                    href="{{ route('collection.view', 'iphone') }}">iPhones</a>
                            </li>
                        </ul>
                    </li>

                    <li>
                        <div class="font-display text-base font-semibold text-black">Meester Mac</div>
                        <ul class="text-neutral-700 mt-4 text-base">
                            <li class="mt-4">
                                <a class="hover:text-neutral-950 transition" href="#tarieven">Tarieven</a>
                            </li>
                            <li class="mt-4">
                                <a class="hover:text-neutral-950 transition"
                                    href="{{ route('home.view') }}#maak-hulp-afspraak">Afspraak maken</a>
                            </li>
                            <li class="mt-4">
                                <a class="hover:text-neutral-950 transition" href="{{ route('about.view') }}">Over
                                    mij</a>
                            </li>
                            <li class="mt-4">
                                <a class="hover:text-neutral-950 transition"
                                    href="{{ route('contact.view') }}">Contact</a>
                            </li>
                        </ul>
                    </li>

                    <li>
                        <div class="font-display text-base font-semibold text-black">Klantenservice</div>
                        <ul class="text-neutral-700 mt-4 text-base">

                            <li class="mt-4">
                                <a class="hover:text-neutral-950 transition" href="{{ route('terms.view') }}">Algemene
                                    voorwaarden</a>
                            </li>

                            <li class="mt-4">
                                <a class="hover:text-neutral-950 transition"
                                    href="{{ route('payment_methods.view') }}">Betaalmethoden</a>
                            </li>
                            <li class="mt-4">
                                <a class="hover:text-neutral-950 transition"
                                    href="{{ route('shipments-and-returns.view') }}">Verzending
                                    en retour</a>
                            </li>
                        </ul>
                    </li>
                </ul>
            </nav>
            <div class="order-1 flex justify-stretch md:order-2 lg:justify-end">
                <div class="w-full sm:max-w-md">
                    <h2 class="font-display text-base font-semibold text-black">Nieuwsbrief</h2>
                    <p class="text-neutral-700 mt-4 text-base">Meld je aan om af en toe nieuws en tips te ontvangen.</p>
                    <livewire:components.newsletter-form />
                </div>
            </div>
        </div>

        <p class="mt-4 border-t border-gray-100 pt-4 text-sm text-gray-500">
            &copy; {{ now()->year }} Meester Mac. Alle rechten voorbehouden | KVK 59317566 | Meester Mac is een
            onafhankelijk bedrijf en geen
            onderdeel
            van Apple
        </p>
    </div>
</footer>
