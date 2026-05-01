@props(['size' => 'md', 'text' => ''])

@php
    $sizes = [
        'sm' => ['wrapper' => 'w-16 h-16', 'cake' => 'scale-75', 'textClass' => 'text-xs'],
        'md' => ['wrapper' => 'w-24 h-24', 'cake' => 'scale-100', 'textClass' => 'text-sm'],
        'lg' => ['wrapper' => 'w-32 h-32', 'cake' => 'scale-125', 'textClass' => 'text-base'],
    ];
    $s = $sizes[$size] ?? $sizes['md'];
@endphp

<div class="cake-loader-wrapper flex flex-col items-center justify-center gap-3">
    <div class="{{ $s['wrapper'] }} relative flex items-center justify-center">

        {{-- Orbiting dots --}}
        <div class="absolute inset-0 cake-orbit">
            <span class="cake-dot" style="--i:0"></span>
            <span class="cake-dot" style="--i:1"></span>
            <span class="cake-dot" style="--i:2"></span>
            <span class="cake-dot" style="--i:3"></span>
            <span class="cake-dot" style="--i:4"></span>
            <span class="cake-dot" style="--i:5"></span>
        </div>

        {{-- Cake SVG --}}
        <svg class="cake-bounce {{ $s['cake'] }}" viewBox="0 0 64 72" fill="none" xmlns="http://www.w3.org/2000/svg" width="48" height="54">
            {{-- Steam wisps --}}
            <path class="cake-steam cake-steam-1" d="M28 8 C28 4, 32 2, 32 0" stroke="#E6B7BE" stroke-width="1.5" stroke-linecap="round" fill="none" opacity="0.6"/>
            <path class="cake-steam cake-steam-2" d="M36 10 C36 6, 40 4, 40 2" stroke="#E6B7BE" stroke-width="1.5" stroke-linecap="round" fill="none" opacity="0.4"/>

            {{-- Candle --}}
            <rect x="29" y="12" width="4" height="14" rx="2" fill="#F8E2E7"/>
            <ellipse cx="31" cy="11" rx="3" ry="4" fill="#FFD97D"/>
            <ellipse cx="31" cy="12" rx="2" ry="2.5" fill="#FFB347"/>

            {{-- Top layer frosting (wavy) --}}
            <path d="M12 30 C12 30, 16 24, 22 26 C28 28, 30 22, 32 22 C34 22, 36 28, 42 26 C48 24, 52 30, 52 30 L52 38 L12 38 Z" fill="#C88A92"/>
            
            {{-- Top layer cake --}}
            <rect x="12" y="34" width="40" height="12" rx="3" fill="#F5E6E8"/>
            <rect x="12" y="34" width="40" height="4" rx="2" fill="#E6B7BE" opacity="0.5"/>

            {{-- Bottom layer frosting drips --}}
            <path d="M8 46 C8 44, 12 42, 18 44 C24 46, 26 40, 32 40 C38 40, 40 46, 46 44 C52 42, 56 44, 56 46 L56 48 L8 48 Z" fill="#C88A92"/>

            {{-- Bottom layer cake --}}
            <rect x="8" y="46" width="48" height="14" rx="4" fill="#F5E6E8"/>
            <rect x="8" y="46" width="48" height="4" rx="2" fill="#E6B7BE" opacity="0.4"/>

            {{-- Cake board --}}
            <ellipse cx="32" cy="62" rx="28" ry="4" fill="#EED9DE"/>

            {{-- Sprinkles on top layer --}}
            <circle cx="20" cy="37" r="1.2" fill="#FFB6C1"/>
            <circle cx="28" cy="36" r="1" fill="#FFD97D"/>
            <circle cx="36" cy="37" r="1.2" fill="#FFB6C1"/>
            <circle cx="44" cy="36" r="1" fill="#FFD97D"/>

            {{-- Sprinkles on bottom layer --}}
            <circle cx="16" cy="52" r="1.2" fill="#FFD97D"/>
            <circle cx="24" cy="53" r="1" fill="#FFB6C1"/>
            <circle cx="32" cy="51" r="1.3" fill="#B5EAD7"/>
            <circle cx="40" cy="53" r="1" fill="#FFB6C1"/>
            <circle cx="48" cy="52" r="1.2" fill="#FFD97D"/>

            {{-- Cherry on top --}}
            <circle cx="31" cy="22" r="4" fill="#E74C6F"/>
            <circle cx="29.5" cy="20.5" r="1.2" fill="#FF7E9D" opacity="0.7"/>
            <path d="M31 18 C33 14, 35 16, 34 18" stroke="#5A3A3A" stroke-width="1" fill="none" stroke-linecap="round"/>
        </svg>
    </div>

    @if($text)
        <p class="{{ $s['textClass'] }} font-bold text-[#C88A92] tracking-wider animate-pulse">{{ $text }}</p>
    @endif
</div>

<style>
    /* Cake bounce animation */
    .cake-bounce {
        animation: cakeBounce 1.4s ease-in-out infinite;
    }
    @keyframes cakeBounce {
        0%, 100% { transform: translateY(0); }
        50% { transform: translateY(-8px); }
    }

    /* Steam animations */
    .cake-steam {
        animation: steamRise 2s ease-in-out infinite;
    }
    .cake-steam-1 { animation-delay: 0s; }
    .cake-steam-2 { animation-delay: 0.7s; }
    @keyframes steamRise {
        0% { opacity: 0; transform: translateY(4px); }
        30% { opacity: 0.6; }
        100% { opacity: 0; transform: translateY(-8px); }
    }

    /* Orbiting dots */
    .cake-orbit {
        animation: orbitSpin 3s linear infinite;
    }
    @keyframes orbitSpin {
        from { transform: rotate(0deg); }
        to { transform: rotate(360deg); }
    }
    .cake-dot {
        position: absolute;
        width: 6px;
        height: 6px;
        border-radius: 50%;
        background: #E6B7BE;
        top: 50%;
        left: 50%;
        margin-top: -3px;
        margin-left: -3px;
    }
    .cake-dot:nth-child(1) { background: #C88A92; width: 8px; height: 8px; margin-top:-4px; margin-left:-4px; transform: rotate(0deg) translateX(44px); }
    .cake-dot:nth-child(2) { background: #E6B7BE; width: 7px; height: 7px; margin-top:-3.5px; margin-left:-3.5px; transform: rotate(60deg) translateX(44px); }
    .cake-dot:nth-child(3) { background: #F5E6E8; transform: rotate(120deg) translateX(44px); }
    .cake-dot:nth-child(4) { background: #C88A92; opacity: 0.5; transform: rotate(180deg) translateX(44px); }
    .cake-dot:nth-child(5) { background: #E6B7BE; opacity: 0.4; width: 5px; height: 5px; margin-top:-2.5px; margin-left:-2.5px; transform: rotate(240deg) translateX(44px); }
    .cake-dot:nth-child(6) { background: #F5E6E8; opacity: 0.3; width: 4px; height: 4px; margin-top:-2px; margin-left:-2px; transform: rotate(300deg) translateX(44px); }
</style>
