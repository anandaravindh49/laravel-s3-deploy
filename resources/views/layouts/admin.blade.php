<!doctype html>
<html lang="{{ str_replace('_', '-', app()->getLocale()) }}">
    <head>
        <meta charset="utf-8">
        <meta name="viewport" content="width=device-width, initial-scale=1">

        <title>{{ config('app.name', 'Laravel') }} - Admin</title>

        <!-- Fonts -->
        <link rel="preconnect" href="https://fonts.bunny.net">
        <link href="https://fonts.bunny.net/css?family=instrument-sans:400,500,600" rel="stylesheet" />

        <!-- Styles / Scripts (same detection used by welcome.blade.php) -->
        @if (file_exists(public_path('build/manifest.json')) || file_exists(public_path('hot')))
            @vite(['resources/css/app.css', 'resources/js/app.js'])
        @else
            {{-- Fallback inline styles copied from default welcome (keeps Tailwind-like utilities available) --}}
            <style>
                /*! tailwindcss fallback (small subset for layout) */
                :root{--bg:#f8fafc}
                html,body{height:100%}
                body{font-family:Instrument Sans,ui-sans-serif,system-ui,sans-serif;background:#f3f4f6}
                .flex{display:flex}.hidden{display:none}.items-center{align-items:center}.justify-between{justify-content:space-between}
                .min-h-screen{min-height:100vh}.p-6{padding:1.5rem}.p-4{padding:1rem}.text-sm{font-size:.875rem}
                .text-lg{font-size:1.125rem}.text-xl{font-size:1.25rem}.font-bold{font-weight:700}.rounded{border-radius:.375rem}
                .bg-white{background:#fff}.bg-indigo-900{background:#0f172a}.text-white{color:#fff}.text-gray-700{color:#374151}
                .shadow{box-shadow:0 1px 3px rgba(0,0,0,0.1)}
                .w-64{width:16rem}.flex-1{flex:1}.gap-4{gap:1rem}
                .grid{display:grid}.grid-cols-3{grid-template-columns:repeat(3,1fr)}.col-span-2{grid-column:span 2 / span 2}
                .h-12{height:3rem}.rounded-sm{border-radius:.25rem}
                .text-gray-500{color:#6b7280}.mt-6{margin-top:1.5rem}.mt-4{margin-top:1rem}
                .p-3{padding:.75rem}.border{border:1px solid rgba(0,0,0,0.06)}.overflow-hidden{overflow:hidden}
                .w-full{width:100%}.max-w-full{max-width:100%}
            </style>
        @endif
    </head>
    <body class="min-h-screen">
        <div class="flex">
            {{-- Sidebar --}}
            <aside class="w-64 bg-indigo-900 text-white min-h-screen shadow">
                <div class="p-6">
                    <a href="/" class="block font-bold text-xl">AZOTOS</a>
                </div>

                <nav class="p-4 text-sm">
                    <ul class="space-y-2">
                        <li class="p-2 rounded"><a href="/dashboard" class="text-white">Dashboard</a></li>
                        <li class="p-2 rounded text-white/90"><a href="#">Staffs (HR)</a></li>
                        <li class="p-2 rounded text-white/90"><a href="#">Students</a></li>
                        <li class="p-2 rounded text-white/90"><a href="#">Roles</a></li>
                        <li class="p-2 rounded text-white/90"><a href="#">Attendance</a></li>
                        <li class="p-2 rounded text-white/90"><a href="#">School Details</a></li>
                    </ul>
                </nav>
            </aside>

            {{-- Main area --}}
            <div class="flex-1">
                {{-- Topbar --}}
                <header class="flex items-center justify-between p-4 bg-white shadow">
                    <div class="flex items-center gap-4">
                        <h2 class="text-xl font-bold text-gray-700">Home <span class="text-sm text-gray-500">› DASHBOARD</span></h2>
                    </div>

                    <div class="flex items-center gap-4">
                        <input placeholder="Search" class="h-10 p-3 border rounded" />
                        <div class="text-sm text-gray-700">{{ auth()->check() ? auth()->user()->email : 'Guest' }}</div>
                    </div>
                </header>

                {{-- Content area --}}
                <main class="p-6">
                    @yield('content')
                </main>
            </div>
        </div>
    </body>
</html>
