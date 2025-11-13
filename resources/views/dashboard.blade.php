@extends('layouts.app')

@section('title', 'Dashboard')

@section('content')
    {{-- Statistic cards --}}
    <div class="grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-4 gap-6 mb-6">
        @foreach ($stats as $s)
            <div class="rounded-lg shadow p-4 bg-white overflow-hidden relative group hover:shadow-lg transition-shadow">
                <div class="flex items-start justify-between">
                    <div>
                        <div class="text-sm text-gray-500">{{ $s['label'] }}</div>
                        <div class="text-2xl font-bold text-gray-800 mt-1">{{ $s['value'] }}</div>
                    </div>

                    <div class="flex items-center justify-center w-12 h-12 rounded-full {{ $s['bg'] }} text-white text-xl shadow-lg">
                        <span>{{ $s['icon'] }}</span>
                    </div>
                </div>

                <div class="mt-3 text-xs text-gray-500">
                    <span class="inline-block px-2 py-1 rounded bg-gray-100 group-hover:bg-gray-200 transition">Last 30 days</span>
                </div>
            </div>
        @endforeach
    </div>

    {{-- Table & side panel layout --}}
    <div class="grid grid-cols-1 lg:grid-cols-3 gap-6">
        <div class="lg:col-span-2 bg-white rounded-lg shadow p-4">
            <div class="flex items-center justify-between mb-4">
                <h3 class="text-lg font-semibold text-gray-800">Players</h3>
                <div class="text-sm text-gray-500">Showing recent players</div>
            </div>

            <div class="overflow-x-auto">
                <table class="w-full text-sm table-auto">
                    <thead>
                        <tr class="text-left text-xs text-gray-500">
                            <th class="py-3 px-3">ID</th>
                            <th class="py-3 px-3">Name</th>
                            <th class="py-3 px-3">Team</th>
                            <th class="py-3 px-3">Status</th>
                            <th class="py-3 px-3">Joined Date</th>
                        </tr>
                    </thead>
                    <tbody>
                        @foreach ($players as $p)
                            <tr class="border-t hover:bg-slate-50 transition-colors">
                                <td class="py-3 px-3 font-medium">{{ $p['id'] }}</td>
                                <td class="py-3 px-3">{{ $p['name'] }}</td>
                                <td class="py-3 px-3">{{ $p['team'] }}</td>
                                <td class="py-3 px-3">
                                    @if ($p['status'] === 'Active')
                                        <span class="inline-flex items-center gap-2 px-2 py-1 rounded-full text-xs bg-emerald-50 text-emerald-700">● Active</span>
                                    @else
                                        <span class="inline-flex items-center gap-2 px-2 py-1 rounded-full text-xs bg-gray-100 text-gray-700">● Inactive</span>
                                    @endif
                                </td>
                                <td class="py-3 px-3 text-sm text-gray-500">{{ \Carbon\Carbon::parse($p['joined'])->format('M d, Y') }}</td>
                            </tr>
                        @endforeach
                    </tbody>
                </table>
            </div>
        </div>

        {{-- Right-side quick stats / upcoming --}}
        <aside class="bg-white rounded-lg shadow p-4">
            <h4 class="font-semibold text-gray-800">Quick Actions</h4>

            <div class="mt-4 space-y-3">
                <button class="w-full text-left px-4 py-2 rounded-md border hover:bg-slate-50 transition">Invite Player</button>
                <button class="w-full text-left px-4 py-2 rounded-md border hover:bg-slate-50 transition">Export CSV</button>
                <button class="w-full text-left px-4 py-2 rounded-md border hover:bg-slate-50 transition">Generate Report</button>
            </div>

            <div class="mt-6">
                <h5 class="text-sm text-gray-500">Upcoming Events</h5>
                <ul class="mt-2 space-y-2 text-sm">
                    <li class="p-2 rounded bg-gray-50">Tryouts - Nov 20, 2025</li>
                    <li class="p-2 rounded bg-gray-50">Charity Match - Dec 05, 2025</li>
                </ul>
            </div>
        </aside>
    </div>
@endsection
