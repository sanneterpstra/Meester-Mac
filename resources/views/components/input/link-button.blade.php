@props([
    'href' => ''
    ])

    <a href="{{$href}}" {{$attributes->merge(['class' => 'inline-block rounded-md bg-blue-600 px-6 py-3 font-semibold text-white hover:bg-blue-500 focus-visible:outline focus-visible:outline-2 focus-visible:outline-offset-2 focus-visible:outline-blue-600'])}}>
        {{$slot}}
    </a>