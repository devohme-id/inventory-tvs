@props(['active', 'icon' => ''])

@php
$classes = ($active ?? false)
            ? 'flex items-center px-4 py-3 bg-blue-900 text-white rounded-lg transition-all duration-200 group shadow-md'
            : 'flex items-center px-4 py-3 text-gray-600 dark:text-gray-300 hover:bg-gray-100 dark:hover:bg-gray-700 rounded-lg transition-all duration-200 group';

$iconClasses = ($active ?? false)
            ? 'w-5 h-5 mr-3 text-white'
            : 'w-5 h-5 mr-3 text-gray-400 group-hover:text-blue-600 dark:group-hover:text-white transition-colors';
@endphp

<a {{ $attributes->merge(['class' => $classes]) }}>
    @if($icon)
        <i class="{{ $icon }} {{ $iconClasses }}"></i>
    @endif
    <span class="font-medium">{{ $slot }}</span>
</a>