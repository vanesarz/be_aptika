<!DOCTYPE html>
<html lang="{{ str_replace('_', '-', app()->getLocale()) }}">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <meta name="csrf-token" content="{{ csrf_token() }}">

    <title>@yield('title', config('app.name', 'SmartJabar'))</title>

    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
    <link href="https://fonts.googleapis.com/css2?family=Plus+Jakarta+Sans:wght@400;500;600;700&family=DM+Mono:wght@400;500&display=swap" rel="stylesheet">

    @vite(['resources/css/app.css', 'resources/js/app.js'])

    <style>
        /* CSS Custom Kamu */
        *, *::before, *::after { box-sizing: border-box; margin: 0; padding: 0; }

        :root {
    --bg: #f6f8fc;
    --surface: #ffffff;
    --border: #e2e8f0;
    --accent: #1d4ed8;
    --accent-2: #0ea5e9;
    --danger: #dc2626;
    --text: #0f172a;
    --muted: #64748b;
    --mono: 'DM Mono', monospace;
    --sans: 'Plus Jakarta Sans', sans-serif;
}

        body {
            font-family: var(--sans);
            background: var(--bg);
            color: var(--text);
            min-height: 100vh;
        }

        /* Navigasi disesuaikan agar support layout bawaan */
        nav.custom-nav {
            background: var(--surface);
            border-bottom: 1px solid var(--border);
            padding: 0 2rem;
            display: flex;
            align-items: center;
            justify-content: space-between;
            height: 64px;
        }
        .nav-brand {
            font-family: var(--mono);
            font-weight: 500;
            font-size: .95rem;
            color: var(--accent);
            letter-spacing: .05em;
            text-decoration: none;
        }
        .nav-brand span { color: var(--muted); }

   

        main {
        margin-left: 320px;
        padding: 2.5rem 1.5rem;
        Hapus atau kurangi max-width agar lebih fleksibel
        max-width: 960px; 
    }
         /* main {
            max-width: 1080px; 
            margin: 0 auto;
            padding: 2.5rem 1.5rem;
        } */

        /* --- Sisa CSS dari kode baru kamu masukkan di sini --- */
        .page-header { display: flex; align-items: flex-end; justify-content: space-between; margin-bottom: 2rem; padding-bottom: 1.25rem; border-bottom: 1px solid var(--border); }
        .btn { display: inline-flex; align-items: center; gap: .4rem; padding: .45rem 1rem; border-radius: 6px; font-size: .85rem; font-weight: 600; cursor: pointer; border: 1px solid transparent; text-decoration: none; transition: all .15s; }
        .btn-primary { background: var(--accent); color: #fff; }
        .table-wrap { border: 1px solid var(--border); border-radius: 8px; overflow: hidden; background: var(--surface); }
        table { width: 100%; border-collapse: collapse; }
        th { padding: .75rem 1rem; text-align: left; font-size: .75rem; color: var(--muted); text-transform: uppercase; }
        td { padding: .8rem 1rem; border-top: 1px solid var(--border); font-size: .875rem; }
    </style>
</head>
<body class="antialiased">
    <div class="min-h-screen">
        @include('layouts.navigation')

        @isset($header)
            <header class="page-header" style="max-width: 1080px; margin: 2rem auto 0; padding: 0 1.5rem;">
                <div class="header-content">
                    {{ $header }}
                </div>
            </header>
        @endisset

        <main>
            {{-- Mendukung pola {{ $slot }} (Breeze) dan @yield('content') (Lama) --}}
            @if(isset($slot))
                {{ $slot }}
            @else
                @yield('content')
            @endif
        </main>
    </div>
</body>
</html>