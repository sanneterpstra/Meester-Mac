<div>
    <ol class="flex w-full items-center space-x-8 space-y-0 bg-gray-50 px-6 py-4">
        @foreach ($steps as $key => $step)
            @if ($step->label)
                <li class="{{ $step->isCurrent() ? 'text-blue-500' : 'text-gray-400' }} flex items-center space-x-2.5"
                    @if ($step->isPrevious()) wire:click="{{ $step->show() }}" @endif>
                    <span
                        class="{{ $step->isCurrent() ? 'border-blue-500' : 'border-gray-400' }} flex h-8 w-8 shrink-0 items-center justify-center rounded-full border font-bold">
                        {{ $key + 1 }}
                    </span>
                    <span class="{{ $step->isCurrent() ?: 'hidden' }} font-bold sm:inline">
                        <h3 class="font-bold">{{ $step->label }}
                    </span>
                </li>
            @endif
        @endforeach
    </ol>
</div>
