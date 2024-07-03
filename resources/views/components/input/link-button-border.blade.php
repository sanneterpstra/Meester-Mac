@props([
    'href' => ''
    ])

<a href="{{$href}}" {{$attributes->merge(['class' => 'rounded-md bg-white px-6 py-3 font-semibold text-gray-900 ring-1 ring-inset ring-gray-300 hover:bg-gray-50'])}}>
	{{$slot}}
</a>