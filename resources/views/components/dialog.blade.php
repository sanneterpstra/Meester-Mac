@props(['name'])
<div
    x-data="{
        show: false,
        init() {
            this.show = (location.hash === '#{{ $name }}');
        },
        close() {
            this.show = false;
            window.history.pushState('', document.title, window.location.pathname);
        }
    }"
    @keydown.escape.window="close()"
    @hashchange.window="show = (location.hash === '#{{ $name }}')"
>
    <div
        class="relative z-30"
        aria-labelledby="modal-title"
        role="dialog"
        aria-modal="true"
        x-show.transition="show"
        x-cloak
    >
        <!--
    Background backdrop, show/hide based on modal state.

    Entering: "ease-out duration-300"
      From: "opacity-0"
      To: "opacity-100"
    Leaving: "ease-in duration-200"
      From: "opacity-100"
      To: "opacity-0"
  -->
        <div
            class="fixed inset-0 bg-gray-500 bg-opacity-75"
            x-show="show"
            x-transition:enter="transition ease-out duration-300"
            x-transition:enter-start="opacity-0"
            x-transition:enter-end="opacity-100 "
            x-transition:leave="transition ease-in duration-200"
            x-transition:leave-start="opacity-100"
            x-transition:leave-end="opacity-0"
        ></div>

        <div
            class="fixed inset-0 z-10 w-screen overflow-y-auto"
            x-show="show"
        >
            <div
                class="flex min-h-full items-end justify-center p-4 text-center lg:items-center lg:p-0"
                x-show="show"
            >
                <!--
            Modal panel, show/hide based on modal state.
    
            Entering: "ease-out duration-300"
              From: "opacity-0 translate-y-4 sm:translate-y-0 sm:scale-95"
              To: "opacity-100 translate-y-0 sm:scale-100"
            Leaving: "ease-in duration-200"
              From: "opacity-100 translate-y-0 sm:scale-100"
              To: "opacity-0 translate-y-4 sm:translate-y-0 sm:scale-95"
            -->
                <div
                    class="relative transform overflow-hidden rounded-lg bg-white px-10 pb-10 pt-10 text-left shadow-xl transition-all sm:my-8 lg:w-1/2"
                    @click.away="close()"
                    x-show="show"
                    x-transition:enter="transition ease-out duration-300"
                    x-transition:enter-start="opacity-0 translate-y-4 sm:translate-y-0 sm:scale-95"
                    x-transition:enter-end="opacity-100 translate-y-0 sm:scale-100"
                    x-transition:leave="transition ease-in duration-200"
                    x-transition:leave-start="opacity-100 translate-y-0 sm:scale-100"
                    x-transition:leave-end="opacity-0 translate-y-4 sm:translate-y-0 sm:scale-95"
                >
                    <div class="absolute right-0 top-0 hidden pr-4 pt-4 sm:block">
                        <a
                            href="#"
                            @click.prevent="close()"
                            type="button"
                            class="rounded-md bg-white text-gray-400 hover:text-gray-500 focus:outline-none focus:ring-2 focus:ring-indigo-500 focus:ring-offset-2"
                        >
                            <span class="sr-only">Close</span>
                            <svg
                                class="h-6 w-6"
                                fill="none"
                                viewBox="0 0 24 24"
                                stroke-width="1.5"
                                stroke="currentColor"
                                aria-hidden="true"
                            >
                                <path
                                    stroke-linecap="round"
                                    stroke-linejoin="round"
                                    d="M6 18L18 6M6 6l12 12"
                                />
                            </svg>
                        </a>
                    </div>
                    <h2 class="text-2xl font-bold mb-10">{{ $title }}</h2>
                    <main>
                        {{ $body }}
                    </main>
                </div>
            </div>
        </div>
    </div>
</div>
