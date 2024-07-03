@props([
    'error' => false,
    ])
    <textarea {{ $attributes->merge([
        'class' => 'w-full p-2 border border-gray-300 block sm:text-base'
    ])->class([
        'border-red-400' => !!$error,
    ]) }}></textarea>