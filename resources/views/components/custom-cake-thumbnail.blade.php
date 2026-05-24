@props([
    'payload' => [],
    'width' => 96,
    'height' => 96,
    'class' => '',
])

@php
    $p = is_array($payload) ? $payload : [];
    $shape = strtolower((string) ($p['shape'] ?? 'round'));
    $layers = max(1, min(4, (int) ($p['layers'] ?? 1)));

    $frostingKey = strtolower((string) ($p['frosting'] ?? 'ivory'));
    $frostingMap = [
        'white' => ['#f2f2f2', '#d9d9d9'],
        'ivory' => ['#e9e2cf', '#d2c7ad'],
        'blush' => ['#edd3d6', '#d8b5bb'],
        'sage' => ['#d2e1d8', '#b4c8bd'],
        'powder_blue' => ['#d6e1ea', '#b7c6d3'],
        'chocolate' => ['#4a2f1f', '#311f14'],
        'mocha' => ['#6a4638', '#4b3329'],
        'lavender' => ['#d7b2ef', '#bb8fdd'],
    ];
    $customFrosting = (string) ($p['frosting_custom'] ?? '');
    $customValid = preg_match('/^#[0-9A-Fa-f]{6}$/', $customFrosting) === 1;
    $topColor = $customValid ? $customFrosting : ($frostingMap[$frostingKey][0] ?? '#e9e2cf');
    $sideColor = $customValid ? '#b89c8c' : ($frostingMap[$frostingKey][1] ?? '#d2c7ad');

    $drip = strtolower((string) ($p['drip'] ?? 'none'));
    $dripMap = [
        'chocolate' => '#3f2219',
        'white_chocolate' => '#fff6ea',
        'pink' => '#f26ca1',
        'caramel' => '#b66a3d',
    ];
    $dripColor = $dripMap[$drip] ?? null;

    $baseW = 96;
    $stepW = 14;
    $tierH = 18;
    $tierStepY = 17;
    $baseY = 86;
@endphp

<svg class="{{ $class }}" width="{{ $width }}" height="{{ $height }}" viewBox="0 0 160 120" aria-label="Custom cake preview">
    <ellipse cx="80" cy="100" rx="52" ry="14" fill="#e7d2dc"/>

    @for ($i = 0; $i < $layers; $i++)
        @php
            $w = $baseW - ($i * $stepW);
            $x = 80 - ($w / 2);
            $y = $baseY - ($i * $tierStepY);
        @endphp
        <g>
            @if ($shape === 'square')
                <rect x="{{ $x }}" y="{{ $y - $tierH }}" width="{{ $w }}" height="{{ $tierH }}" rx="4" fill="{{ $sideColor }}"/>
                <rect x="{{ $x }}" y="{{ $y - $tierH - 8 }}" width="{{ $w }}" height="10" rx="5" fill="{{ $topColor }}" stroke="#bf8b99" stroke-width="1"/>
            @elseif ($shape === 'heart')
                <ellipse cx="{{ 80 }}" cy="{{ $y - $tierH/2 }}" rx="{{ $w/2 }}" ry="{{ $tierH/2 }}" fill="{{ $sideColor }}"/>
                <path d="M {{ 80 }} {{ $y - $tierH - 2 }}
                         c -10 -9 -24 -10 -24 2
                         c 0 10 11 15 24 24
                         c 13 -9 24 -14 24 -24
                         c 0 -12 -14 -11 -24 -2 z"
                      fill="{{ $topColor }}" stroke="#bf8b99" stroke-width="1"/>
            @else
                <ellipse cx="{{ 80 }}" cy="{{ $y - $tierH/2 }}" rx="{{ $w/2 }}" ry="{{ $tierH/2 }}" fill="{{ $sideColor }}"/>
                <ellipse cx="{{ 80 }}" cy="{{ $y - $tierH }}" rx="{{ $w/2 }}" ry="8" fill="{{ $topColor }}" stroke="#bf8b99" stroke-width="1"/>
            @endif

            @if ($dripColor)
                <path d="M {{ $x + 8 }} {{ $y - $tierH + 1 }}
                         c 8 0 8 8 12 8
                         c 4 0 4 -10 8 -10
                         c 5 0 5 9 9 9
                         c 4 0 5 -7 9 -7
                         c 5 0 6 8 11 8
                         c 4 0 5 -8 9 -8
                         c 4 0 4 6 8 6"
                      fill="none" stroke="{{ $dripColor }}" stroke-width="3.2" stroke-linecap="round"/>
            @endif
        </g>
    @endfor
</svg>

