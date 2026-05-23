<!DOCTYPE html>
<html lang="{{ str_replace('_', '-', app()->getLocale()) }}">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <title>404 - Recipe Not Found | {{ config('app.name', 'BonBons PH') }}</title>
    <link rel="preconnect" href="https://fonts.bunny.net">
    <link href="https://fonts.bunny.net/css?family=great-vibes:400|instrument-sans:400,500,600,700,800,900" rel="stylesheet" />
    <style>
        :root {
            --cream: #fff8f2;
            --blush: #ffe8e7;
            --rose: #df6673;
            --rose-dark: #9c5b58;
            --brown: #4a251c;
            --muted: #93635c;
        }
        * { box-sizing: border-box; }
        html, body { min-height: 100%; }
        body {
            margin: 0;
            font-family: 'Instrument Sans', ui-sans-serif, system-ui, sans-serif;
            color: var(--brown);
            background: #ffe5d8;
            overflow-x: hidden;
        }
        .page {
            position: relative;
            min-height: 100svh;
            isolation: isolate;
            overflow: hidden;
            background:
                radial-gradient(circle at 15% 20%, rgba(255,255,255,.9), transparent 18rem),
                radial-gradient(circle at 88% 80%, rgba(103,48,30,.22), transparent 22rem),
                linear-gradient(120deg, #fff8f2 0%, #ffe9e5 45%, #e9a987 100%);
        }
        .page::before {
            content: '';
            position: absolute;
            inset: 0;
            z-index: -3;
            background: linear-gradient(90deg, rgba(255,248,242,.98) 0%, rgba(255,248,242,.9) 34%, rgba(255,248,242,.28) 62%, rgba(255,248,242,0) 100%);
        }
        .art {
            position: absolute;
            inset: 0 0 0 38%;
            z-index: -2;
            overflow: hidden;
        }
        .art img {
            width: 100%;
            height: 100%;
            object-fit: cover;
            object-position: 64% center;
            opacity: .82;
            filter: saturate(.98) contrast(.96) sepia(.04);
        }
        .art::before {
            content: '';
            position: absolute;
            inset: 0;
            background:
                linear-gradient(90deg, rgba(255,248,242,.96) 0%, rgba(255,248,242,.58) 20%, rgba(255,248,242,.05) 56%),
                linear-gradient(180deg, rgba(255,248,242,.16), rgba(99,41,25,.18));
        }
        .art::after {
            content: '';
            position: absolute;
            right: 8%;
            bottom: 13%;
            width: 10rem;
            height: 10rem;
            border-radius: 999px;
            background: rgba(30,19,16,.4);
            filter: blur(38px);
            opacity: .45;
        }
        .shell {
            min-height: 100svh;
            width: min(44rem, 94vw);
            display: flex;
            flex-direction: column;
            justify-content: center;
            padding: 3rem clamp(1.25rem, 4.5vw, 6rem) 8rem;
            text-align: center;
        }
        .brand {
            margin-bottom: 1.6rem;
            color: var(--rose-dark);
        }
        .brand__name {
            position: relative;
            display: inline-block;
            font-family: 'Great Vibes', cursive;
            font-size: clamp(3.2rem, 6vw, 5rem);
            line-height: .82;
        }
        .brand__name::before {
            content: '♡';
            position: absolute;
            top: -.8rem;
            left: 50%;
            color: #df6673;
            font-family: ui-sans-serif, system-ui, sans-serif;
            font-size: 1.05rem;
            transform: translateX(-50%);
        }
        .brand__sub {
            margin-top: .5rem;
            font-size: .86rem;
            font-weight: 900;
            letter-spacing: .5em;
        }
        .code {
            position: relative;
            margin: 0 auto;
            width: max-content;
            font-size: clamp(8rem, 16vw, 14rem);
            line-height: .8;
            font-weight: 950;
            letter-spacing: -.08em;
            color: var(--rose);
            text-shadow: 0 .6rem 1.6rem rgba(185,75,86,.18);
        }
        .code::before {
            content: '';
            position: absolute;
            left: .16em;
            top: -.08em;
            width: .33em;
            height: .28em;
            background: #fff3f5;
            border-radius: 45% 45% 56% 56%;
            clip-path: polygon(0 0, 100% 0, 100% 76%, 78% 76%, 70% 100%, 58% 76%, 0 76%);
            box-shadow: inset 0 -.25rem 0 rgba(223,102,115,.14);
        }
        h1 {
            margin: 1.35rem 0 0;
            font-size: clamp(2rem, 4.6vw, 3.1rem);
            line-height: 1.22;
            letter-spacing: -.045em;
            font-weight: 900;
        }
        h1 span { color: var(--rose); }
        .divider {
            display: flex;
            align-items: center;
            justify-content: center;
            gap: .75rem;
            margin: 1.25rem auto 1.4rem;
            color: #ef9fa8;
            font-size: 1.8rem;
        }
        .divider::before,
        .divider::after {
            content: '';
            width: 5.5rem;
            height: 1px;
            background: linear-gradient(90deg, transparent, #eda1aa, transparent);
        }
        .lead {
            max-width: 30rem;
            margin: 0 auto 2.2rem;
            color: var(--muted);
            font-size: clamp(1.02rem, 2vw, 1.3rem);
            line-height: 1.55;
            font-weight: 700;
        }
        .actions {
            display: grid;
            gap: 1rem;
            width: min(24rem, 100%);
            margin: 0 auto;
        }
        .btn {
            display: inline-flex;
            min-height: 4.2rem;
            align-items: center;
            justify-content: center;
            gap: .75rem;
            border-radius: 999px;
            border: 1px solid rgba(223,102,115,.55);
            padding: .95rem 1.25rem;
            text-decoration: none;
            font-size: 1.08rem;
            font-weight: 900;
            transition: transform .18s ease, box-shadow .18s ease, background .18s ease;
        }
        .btn:hover { transform: translateY(-2px); }
        .btn-primary {
            color: #fff;
            background: linear-gradient(135deg, #df6673, #d95667);
            box-shadow: 0 1rem 2rem rgba(216,86,103,.28);
        }
        .btn-secondary {
            color: var(--rose-dark);
            background: rgba(255,255,255,.56);
            backdrop-filter: blur(8px);
        }
        .note {
            position: absolute;
            left: 50%;
            bottom: 0;
            z-index: 3;
            width: min(52rem, 84vw);
            transform: translateX(-50%);
            display: grid;
            grid-template-columns: 1fr auto 1.1fr;
            align-items: center;
            gap: 1.3rem;
            padding: 1.55rem 2rem 1.35rem;
            border: 1px dashed rgba(223,102,115,.34);
            border-bottom: 0;
            border-radius: 3rem 3rem 0 0;
            background: rgba(255,252,247,.94);
            box-shadow: 0 -1rem 2.6rem rgba(75,38,29,.08);
        }
        .note p {
            margin: 0;
            color: var(--brown);
            font-weight: 800;
            line-height: 1.45;
        }
        .note__line {
            position: relative;
            width: 1px;
            height: 3.7rem;
            background: linear-gradient(transparent, rgba(223,102,115,.46), transparent);
        }
        .note__line::before {
            content: '♥';
            position: absolute;
            top: -.9rem;
            left: 50%;
            transform: translateX(-50%);
            color: var(--rose);
        }
        .tip {
            display: flex;
            align-items: center;
            justify-content: center;
            gap: .85rem;
            color: var(--rose);
        }
        .floating-smoke {
            position: absolute;
            right: 9%;
            top: 33%;
            width: 9rem;
            height: 14rem;
            z-index: -1;
            pointer-events: none;
        }
        .floating-smoke span {
            position: absolute;
            width: 5rem;
            height: 5rem;
            border-radius: 50%;
            background: radial-gradient(circle, rgba(50,38,34,.35), rgba(50,38,34,.04) 68%, transparent 72%);
            animation: smoke 4.2s ease-in-out infinite;
        }
        .floating-smoke span:nth-child(1) { bottom: 0; left: 2rem; }
        .floating-smoke span:nth-child(2) { bottom: 3rem; left: .6rem; animation-delay: .45s; }
        .floating-smoke span:nth-child(3) { bottom: 6.3rem; left: 3rem; animation-delay: .9s; }
        @keyframes smoke {
            0%, 100% { transform: translateY(0) scale(.9); opacity: .55; }
            50% { transform: translateY(-1.2rem) scale(1.08); opacity: .85; }
        }
        @media (max-width: 1050px) {
            .page {
                overflow-y: auto;
                background: linear-gradient(180deg, #fff8f2 0%, #ffe7e4 48%, #e9a987 100%);
            }
            .page::before { background: rgba(255,248,242,.72); }
            .art {
                position: relative;
                inset: auto;
                width: 100%;
                height: min(48rem, 58svh);
                z-index: 0;
                order: 2;
                border-radius: 2.2rem 2.2rem 0 0;
            }
            .art img { object-position: 62% center; opacity: .95; }
            .art::before { background: linear-gradient(180deg, rgba(255,248,242,.15), rgba(255,248,242,.4)); }
            .shell {
                width: 100%;
                min-height: auto;
                padding: 2.2rem 1.25rem 2rem;
            }
            .note {
                position: relative;
                left: auto;
                bottom: auto;
                transform: none;
                width: calc(100% - 2rem);
                grid-template-columns: 1fr;
                margin: 1rem auto 1.2rem;
                text-align: center;
                border-radius: 2rem;
                border-bottom: 1px dashed rgba(223,102,115,.34);
            }
            .note__line { display: none; }
            .floating-smoke { display: none; }
        }
        @media (max-width: 540px) {
            .brand__name { font-size: 3.25rem; }
            .brand__sub { font-size: .68rem; }
            .code { font-size: 7.4rem; }
            h1 { font-size: 1.85rem; }
            .lead { font-size: 1rem; }
            .btn { min-height: 3.8rem; border-radius: 1.45rem; }
            .art { height: 24rem; }
            .art img { object-position: 62% center; }
        }
    </style>
</head>
<body>
    <main class="page">
        <div class="art" aria-hidden="true">
            <img src="{{ asset('images/bonbon_404_page.png') }}" alt="">
        </div>
        <div class="floating-smoke" aria-hidden="true"><span></span><span></span><span></span></div>

        <section class="shell" aria-labelledby="page-title">
            <div class="brand" aria-label="BonBons Bakery">
                <div class="brand__name">BonBons</div>
                <div class="brand__sub">• BAKERY •</div>
            </div>

            <div class="code" aria-hidden="true">404</div>
            <h1 id="page-title">Oops! You’ve found<br>a recipe that <span>doesn’t exist.</span></h1>
            <div class="divider" aria-hidden="true">♥</div>
            <p class="lead">Looks like our chef got a little too excited in the kitchen. Don’t worry — even the best bakers have burnt moments.</p>

            <nav class="actions" aria-label="404 page actions">
                <a class="btn btn-primary" href="{{ url('/') }}">
                    <span aria-hidden="true">⌂</span>
                    Back to Home
                </a>
                <a class="btn btn-secondary" href="{{ url('/test-products') }}">
                    <span aria-hidden="true">🧁</span>
                    Explore Our Menu
                </a>
            </nav>
        </section>

        <aside class="note" aria-label="Helpful tip">
            <p>Don’t worry, even the best bakers have their burnt moments! <span style="color:var(--rose)">♥</span></p>
            <div class="note__line"></div>
            <div class="tip">
                <span style="font-size:2rem" aria-hidden="true">👨‍🍳</span>
                <p><strong>Tip:</strong> Try checking the URL<br>or head back to something sweet!</p>
            </div>
        </aside>
    </main>
</body>
</html>
