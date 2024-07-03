@props([
    'error' => false,
    ])
    <input {{ $attributes->merge([
        'type' => 'text',
        'class' => 'w-full p-2 border border-gray-300 sm:text-base',
    ])->class([
        'border-red-400' => !!$error,
    ]) }} maxlength="255">