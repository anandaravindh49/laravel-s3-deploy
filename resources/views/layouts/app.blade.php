<!doctype html>
<html lang="{{ str_replace('_', '-', app()->getLocale()) }}">
<head>
    <meta charset="utf-8" />
    <meta name="viewport" content="width=device-width, initial-scale=1" />
    <title>{{ config('app.name', 'MyApp') }} - @yield('title', 'Dashboard')</title>

    {{-- Use Vite-built assets when available; otherwise fallback minimal CSS so layout looks OK --}}
    @if (file_exists(public_path('build/manifest.json')) || file_exists(public_path('hot')))
        @vite(['resources/css/app.css', 'resources/js/app.js'])
    @else
        <style>
            /* Minimal fallback styles (keeps things readable without a built Tailwind) */
            :root{--sidebar-bg:#0f172a;--muted:#6b7280;--card:#ffffff}
            *{box-sizing:border-box}
            body{font-family:ui-sans-serif,system-ui,-apple-system,'Segoe UI',Roboto,'Helvetica Neue',Arial; margin:0; background:#f8fafc; color:#0f172a}
            a{text-decoration:none}
            .flex{display:flex}.items-center{align-items:center}.justify-between{justify-content:space-between}.justify-center{justify-content:center}
            .min-h-screen{min-height:100vh}.overflow-auto{overflow:auto}
            .p-4{padding:1rem}.p-6{padding:1.5rem}.gap-4{gap:1rem}.gap-6{gap:1.5rem}
            .rounded{border-radius:.5rem}.rounded-md{border-radius:.375rem}
            .shadow{box-shadow:0 6px 18px rgba(15,23,42,0.06)}
            .text-sm{font-size:.875rem}.text-xs{font-size:.75rem}.text-lg{font-size:1.125rem}.text-xl{font-size:1.25rem}
            .w-64{width:16rem}.h-12{height:3rem}
            .bg-white{background:#fff}.bg-indigo-600{background:#4f46e5}.text-white{color:#fff}
            .border{border:1px solid rgba(15,23,42,0.06)}.rounded-full{border-radius:9999px}
            .table{display:table;width:100%}
            .text-muted{color:var(--muted)}
            .hover-light:hover{background:#f1f5f9}
            .cursor-pointer{cursor:pointer}
        </style>
    @endif

    @stack('head')
</head>
<body class="min-h-screen">
    <div class="flex">
        {{-- Sidebar --}}
        <aside class="w-64 bg-[var(--sidebar-bg,#0f172a)] text-white min-h-screen hidden md:block">
            <div class="p-6 border-b border-white/5">
                <a href="{{ url('/') }}" class="flex items-center gap-3">
                    <img src="https://via.placeholder.com/40x40.png?text=LOGO" alt="logo" class="w-10 h-10 rounded-md">
                    <div>
                        <div class="font-bold text-lg">{{ config('app.name', 'MyApp') }}</div>
                        <div class="text-xs text-white/70">Admin Panel</div>
                    </div>
                </a>
            </div>

            <nav class="p-4">
                @php
                    // Define menu items as [label => route/path]
                    $menu = [
                        'Dashboard' => '/dashboard',
                        'Players'   => '/players',
                        'Reports'   => '/reports',
                        'Settings'  => '/settings',
                        'Logout'    => '/logout',
                    ];

                    // Current path for active detection
                    $current = request()->path(); // e.g., 'dashboard'
                @endphp

                <ul class="space-y-1">
                    @foreach ($menu as $label => $path)
                        @php
                            // active if current matches path (supports root and nested)
                            $isActive = $path === '/dashboard'
                                ? request()->is('dashboard') || request()->is('/')
                                : request()->is(ltrim($path, '/')) || request()->is(ltrim($path, '/').'*');
                        @endphp

                        <li>
                            <a href="{{ $path }}"
                               class="flex items-center gap-3 p-3 rounded-md transition-colors duration-150 {{ $isActive ? 'bg-white/10 text-white' : 'text-white/80 hover:bg-white/5 hover:text-white' }}">
                                {{-- simple icons (emoji) for demo; replace with SVG icons as needed --}}
                                <span class="w-9 h-9 flex items-center justify-center rounded-md {{ $isActive ? 'bg-white/20' : 'bg-white/5' }}">
                                    @switch($label)
                                        @case('Dashboard') 🏠 @break
                                        @case('Players') 🎮 @break
                                        @case('Reports') 📊 @break
                                        @case('Settings') ⚙️ @break
                                        @case('Logout') 🔒 @break
                                        @default •
                                    @endswitch
                                </span>

                                <span class="font-medium">{{ $label }}</span>

                                {{-- a small badge example for Players --}}
                                @if ($label === 'Players')
                                    <span class="ml-auto text-xs bg-white/10 px-2 py-0.5 rounded">{{ 1250 }}</span>
                                @endif
                            </a>
                        </li>
                    @endforeach
                </ul>
            </nav>

            <div class="p-4 mt-auto border-t border-white/5 text-sm">
                <div class="text-xs text-white/70">Signed in as</div>
                <div class="font-medium mt-1">{{ auth()->check() ? auth()->user()->name : 'Demo Admin' }}</div>
                <div class="text-xs text-white/60">{{ auth()->check() ? auth()->user()->email : 'admin@example.com' }}</div>
            </div>
        </aside>

        {{-- Main content --}}
        <div class="flex-1 min-h-screen overflow-auto bg-slate-50">
            {{-- Top navbar --}}
            <header class="h-12 bg-white border-b flex items-center justify-between px-4 md:px-8">
                <div class="flex items-center gap-4">
                    <button class="md:hidden p-2 rounded-md bg-white/10 text-gray-700">☰</button>
                    <h2 class="text-lg font-semibold text-gray-800">@yield('title', 'Dashboard')</h2>
                </div>

                <div class="flex items-center gap-4">
                    <div class="text-sm text-gray-700">Hello, <span class="font-medium">{{ auth()->check() ? auth()->user()->name : 'Demo Admin' }}</span></div>
                    <img src="https://via.placeholder.com/36" alt="avatar" class="w-9 h-9 rounded-full border">
                </div>
            </header>

            <main class="p-6 md:p-8">
                @yield('content')
            </main>
        </div>
    </div>

    @stack('scripts')
</body>
</html>
