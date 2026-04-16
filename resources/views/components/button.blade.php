@props(['type' => 'button', 'variant' => 'primary'])

@php
    $classes = 'inline-flex items-center justify-center rounded-md px-4 py-2 font-medium transition duration-150';
    if ($variant === 'primary') {
        $classes .= ' bg-[#5A3A3A] text-white hover:bg-[#4b3232]';
    } elseif ($variant === 'secondary') {
        $classes .= ' bg-[#E6B7BE] text-[#5A3A3A] hover:bg-[#D9A0A8]';
    }
@endphp

<button type="{{ $type }}" {{ $attributes->merge(['class' => $classes]) }}>
    {{ $slot }}
</button>
