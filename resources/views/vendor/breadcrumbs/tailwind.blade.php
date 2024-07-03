@unless ($breadcrumbs->isEmpty())
    <nav class="flex justify-start" aria-label="Breadcrumb">
        <ol class="flex items-center space-x-4 px-4 py-4 sm:px-6 lg:px-8" role="list">
            <li>
                <div>
                    <a class="text-gray-400 hover:text-gray-500" href="{{ route('home.view') }}">
                        <svg class="h-5 w-5 flex-shrink-0" aria-hidden="true" viewBox="0 0 20 20" fill="currentColor">
                            <path fill-rule="evenodd"
                                d="M9.293 2.293a1 1 0 011.414 0l7 7A1 1 0 0117 11h-1v6a1 1 0 01-1 1h-2a1 1 0 01-1-1v-3a1 1 0 00-1-1H9a1 1 0 00-1 1v3a1 1 0 01-1 1H5a1 1 0 01-1-1v-6H3a1 1 0 01-.707-1.707l7-7z"
                                clip-rule="evenodd" />
                        </svg>
                        <span class="sr-only">Home</span>
                    </a>
                </div>
            </li>
            @foreach ($breadcrumbs as $breadcrumb)
                @if ($breadcrumb->url && !$loop->last)
                    <li>
                        <div class="flex items-center">
                            <svg class="h-5 w-5 flex-shrink-0 text-gray-400" aria-hidden="true" viewBox="0 0 20 20"
                                fill="currentColor">
                                <path fill-rule="evenodd"
                                    d="M7.21 14.77a.75.75 0 01.02-1.06L11.168 10 7.23 6.29a.75.75 0 111.04-1.08l4.5 4.25a.75.75 0 010 1.08l-4.5 4.25a.75.75 0 01-1.06-.02z"
                                    clip-rule="evenodd" />
                            </svg>
                            <a class="ml-4 text-nowrap text-sm font-medium text-gray-500 hover:text-gray-700"
                                href="{{ $breadcrumb->url }}">
                                {{ $breadcrumb->title }}
                            </a>
                        </div>
                    </li>
                @else
                    <li>
                        <div class="flex items-center">
                            <svg class="h-5 w-5 flex-shrink-0 text-gray-400" aria-hidden="true" viewBox="0 0 20 20"
                                fill="currentColor">
                                <path fill-rule="evenodd"
                                    d="M7.21 14.77a.75.75 0 01.02-1.06L11.168 10 7.23 6.29a.75.75 0 111.04-1.08l4.5 4.25a.75.75 0 010 1.08l-4.5 4.25a.75.75 0 01-1.06-.02z"
                                    clip-rule="evenodd" />
                            </svg>
                            <span class="ml-4 text-nowrap text-sm font-medium text-gray-900" aria-current="page">
                                {{ $breadcrumb->title }}</span>
                        </div>
                    </li>
                @endif
            @endforeach
        </ol>
    </nav>
@endunless
