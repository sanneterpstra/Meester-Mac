<x-dialog name="tarieven">
    <x-slot name="title">Tarieven</x-slot>
    <x-slot name="body">
        <h2 class="font-bold text-lg">Hulp aan huis</h2>
        <p class="mb-4">Een hulpafspraak aan huis bestaat uit twee kostenposten: de <span class="font-bold">voorrijkosten</span> en de kosten voor de <span class="font-bold">werkzaamheden</span>. De voorrijkosten zijn relateerd aan de gereden kilometers. De kosten voor de werkzaamheden worden berekend op basis van het uurtarief. Er wordt per kwartier gerekend en er is geen minimale werktijd. Indien randapparatuur aangeschaft moet worden voor een goede installatie of reparatie zullen de kosten hiervoor worden doorberekend.
        </p>

        <div class="ring-1 ring-gray-300 mb-8">
            <table class="min-w-full divide-y divide-gray-300">
                <tbody class="divide-y divide-gray-300 bg-white">
                    <tr>
                        <td class="relative py-4 pl-4 pr-3 sm:pl-6">
                            <div class="flex items-center">
                                <div class="h-11 w-11 flex-shrink-0">
                                    <img
                                        class="h-11 w-11"
                                        src="https://www.meestermac.nl/img/auto.svg"
                                        alt=""
                                    >
                                </div>
                                <div class="ml-4">
                                    <div class="font-medium text-gray-900">Voorrijkosten</div>
                                </div>
                            </div>
                        </td>
                        <td class="px-3 py-5">
                            <div><span class="font-bold">&euro; 0,80</span> per kilometer</div>
                        </td>
                    </tr>
                    <tr>
                        <td class="relative py-4 pl-4 pr-3 sm:pl-6">
                            <div class="flex items-center">
                                <div class="h-11 w-11 flex-shrink-0">
                                    <img
                                        class="h-11 w-11"
                                        src="https://www.meestermac.nl/img/duidelijk-tarief.svg"
                                        alt=""
                                    >
                                </div>
                                <div class="ml-4">
                                    <div class="font-medium text-gray-900">Werktijd</div>
                                </div>
                            </div>
                        </td>
                        <td class="px-3 py-5">
                            <div><span class="font-bold">&euro; 21,50</span> per kwartier</div>
                        </td>
                    </tr>
                </tbody>
            </table>
        </div>

        <h2 class="font-bold text-lg">Hulp op afstand</h2>
        <p class="mb-4">De kosten voor telefonische hulp op afstand worden berekend op basis van het uurtarief. Het eerste uur wordt altijd in rekening gebracht ongeacht de duur van de hulp. Alle extra tijd wordt per kwartier gerekend.</p>

        <div class="ring-1 ring-gray-300 mb-8">
            <table class="min-w-full divide-y divide-gray-300">
                <tbody class="divide-y divide-gray-300 bg-white">
                    <tr>
                        <td class="relative py-4 pl-4 pr-3 sm:pl-6">
                            <div class="flex items-center">
                                <div class="h-11 w-11 flex-shrink-0">
                                    <img
                                        class="h-11 w-11"
                                        src="https://www.meestermac.nl/img/duidelijk-tarief.svg"
                                        alt=""
                                    >
                                </div>
                                <div class="ml-4">
                                    <div class="font-medium text-gray-900">Starttarief</div>
                                </div>
                            </div>
                        </td>
                        <td class="px-3 py-5">
                            <div><span class="font-bold">&euro; 86</span> eerste uur</div>
                        </td>
                    </tr>
                    <tr>
                        <td class="relative py-4 pl-4 pr-3 sm:pl-6">
                            <div class="flex items-center">
                                <div class="h-11 w-11 flex-shrink-0">
                                    <img
                                        class="h-11 w-11"
                                        src="https://www.meestermac.nl/img/duidelijk-tarief.svg"
                                        alt=""
                                    >
                                </div>
                                <div class="ml-4">
                                    <div class="font-medium text-gray-900">Extra tijd</div>
                                </div>
                            </div>
                        </td>
                        <td class="px-3 py-5">
                            <div><span class="font-bold">&euro; 21,50</span> per kwartier</div>
                        </td>
                    </tr>
                </tbody>
            </table>
        </div>

        <p class="mb-4">Zodra je een hulpaanvraag indient, zal ik een kostenschatting naar je opsturen. Zo weet je altijd waar je aan toe bent.</p>

        <x-input.link-button href="{{ route('home.view') }}#maak-hulp-afspraak">Vraag direct hulp aan</x-input.link-button>

    </x-slot>
</x-dialog>
