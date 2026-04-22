@extends('layouts.app')
@include('layouts.navigation')
@section('header')
    <h2 class="font-semibold text-xl text-gray-800 dark:text-gray-200 leading-tight">
        Dashboard Parkir
    </h2>
@endsection

@section('content')
<div class="py-12">
    <div class="max-w-7xl mx-auto sm:px-6 lg:px-8">
        <div class="bg-white dark:bg-gray-800 overflow-hidden shadow-sm sm:rounded-lg">
            <div class="p-6 text-gray-900 dark:text-gray-100">
                <!-- Stats Grid -->
                <div class="grid grid-cols-1 md:grid-cols-3 gap-6 mb-8">
                    <div class="bg-gradient-to-r from-green-400 to-green-500 p-6 rounded-xl shadow-lg text-white">
                        <div class="text-3xl font-bold mb-1">{{ $availableLots ?? 0 }}</div>
                        <div class="text-sm opacity-90">Lot Tersedia</div>
                        <div class="mt-3">
                            <div class="w-full bg-white bg-opacity-20 rounded-full h-2">
                                <div class="bg-white h-2 rounded-full" style="width: {{ (($totalLots ?? 1) > 0 ? (($availableLots ?? 0) / ($totalLots ?? 1)) * 100 : 0) }}%"></div>
                            </div>
                        </div>
                    </div>
                    <div class="bg-gradient-to-r from-orange-400 to-orange-500 p-6 rounded-xl shadow-lg text-white">
                        <div class="text-3xl font-bold mb-1">{{ $activeTickets ?? 0 }}</div>
                        <div class="text-sm opacity-90">Kendaraan Aktif</div>
                    </div>
                    <div class="bg-gradient-to-r from-blue-400 to-blue-500 p-6 rounded-xl shadow-lg text-white">
                        <div class="text-3xl font-bold mb-1">Rp {{ number_format($todayRevenue ?? 0) }}</div>
                        <div class="text-sm opacity-90">Pendapatan Hari Ini</div>
                    </div>
                </div>


                {{-- Filter Form --}}
                <div class="bg-gradient-to-r from-indigo-50 to-purple-50 dark:from-indigo-900/20 dark:to-purple-900/20 p-6 rounded-xl shadow-lg mb-8">
                    <h3 class="text-xl font-bold mb-6 flex items-center text-gray-800 dark:text-gray-100">
                        <svg class="w-6 h-6 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M3 4a1 1 0 011-1h16a1 1 0 011 1v2.586a1 1 0 01-.293.707l-6.414 6.414a1 1 0 00-.293.707V17l-4 4v-6.586a1 1 0 00-.293-.707L3.293 7.293A1 1 0 013 6.586V4z"></path>
                        </svg>
                        Filter Data
                    </h3>
                    <form method="GET" action="{{ route('parking.dashboard') }}" class="grid grid-cols-1 md:grid-cols-3 gap-4">
                        <input type="text" name="search" value="{{ $filters['search'] ?? '' }}" placeholder="Cari plat atau ticket..." class="px-4 py-3 border border-gray-300 rounded-xl focus:ring-2 focus:ring-indigo-500 focus:border-indigo-500 dark:bg-white dark:border-gray-600">
                        <select name="status" class="px-4 py-3 border border-gray-300 rounded-xl focus:ring-2 focus:ring-indigo-500 focus:border-indigo-500 dark:bg-white dark:border-gray-600">
                            <option value="all">Semua Status</option>
                            <option value="entry" {{ ($filters['status'] ?? '') == 'entry' ? 'selected' : '' }}>Entry</option>
                            <option value="paid" {{ ($filters['status'] ?? '') == 'paid' ? 'selected' : '' }}>Paid</option>
                        </select>
                        <div class="flex gap-2">
                            <button type="submit" class="flex-1 bg-indigo-600 hover:bg-indigo-700 text-white py-3 px-4 rounded-xl font-semibold transition-all duration-200 shadow-md hover:shadow-lg">
                                Filter
                            </button>
                            <a href="{{ route('parking.dashboard') }}" class="bg-gray-500 hover:bg-gray-600 text-white py-3 px-4 rounded-xl font-semibold transition-all duration-200 shadow-md hover:shadow-lg text-center whitespace-nowrap">
                                Reset
                            </a>
                        </div>
                    </form>
                </div>

                {{-- Filtered Tickets Table --}}
                <div class="bg-gradient-to-r from-blue-50 to-indigo-50 dark:from-blue-900/20 dark:to-indigo-900/20 p-6 rounded-xl shadow-lg mb-8">
                    <h3 class="text-xl font-bold mb-6 flex items-center text-gray-800 dark:text-gray-100">
                        <svg class="w-6 h-6 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M3 4a1 1 0 011-1h16a1 1 0 011 1v2.586a1 1 0 01-.293.707l-6.414 6.414a1 1 0 00-.293.707V17l-4 4v-6.586a1 1 0 00-.293-.707L3.293 7.293A1 1 0 013 6.586V4z"></path>
                        </svg>
                        Filtered Tickets ({{ $tickets->total() }} of {{ $tickets->total() }})
                    </h3>
                    @if($tickets->isEmpty())
                        <div class="text-center py-12 text-gray-500 dark:text-gray-400">
                            <svg class="w-16 h-16 mx-auto mb-4 opacity-50" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 5H7a2 2 0 00-2 2v10a2 2 0 002 2h10a2 2 0 002 -2V7a2 2 0 00-2 -2h-2M9 5a2 2 0 0 0 2 2h2a2 2 0 0 0 2 -2M9 5a2 2 0 0 1 2 -2h2a2 2 0 0 1 2 2m-3 7h3m-3 4h3"></path>
                            </svg>
                            <p>Tidak ada data yang sesuai filter</p>
                            <p class="text-sm mt-1">Coba ubah kata kunci pencarian atau pilih status lain</p>
                        </div>
                    @else
                        <div class="overflow-x-auto">
                            <table class="w-full bg-white dark:bg-gray-800 rounded-xl shadow-sm">
                                <thead class="bg-gradient-to-r from-blue-500 to-indigo-600 text-white">
                                    <tr>
                                        <th class="px-6 py-4 text-left rounded-tl-xl font-semibold">Ticket</th>
                                        <th class="px-6 py-4 text-left font-semibold">Plat</th>
                                        <th class="px-6 py-4 text-left font-semibold">Lot</th>
                                        <th class="px-6 py-4 text-left font-semibold">Entry</th>
                                        @if(($filters['status'] ?? 'all') == 'paid' || ($filters['status'] ?? '') == '')
                                        <th class="px-6 py-4 text-left font-semibold">Exit</th>
                                        <th class="px-6 py-4 text-left font-semibold">Fee</th>
                                        @endif
                                        <th class="px-6 py-4 text-left font-semibold rounded-tr-xl">Status</th>
                                    </tr>
                                </thead>
                                <tbody class="divide-y divide-gray-200 dark:divide-gray-700">
                                    @foreach($tickets as $ticket)
                                    <tr class="hover:bg-gray-50 dark:hover:bg-gray-700/50 transition-colors">
                                        <td class="px-6 py-4 font-mono text-sm bg-blue-50 dark:bg-blue-900/30">{{ $ticket->ticket_code }}</td>
                                        <td class="px-6 py-4 font-semibold text-gray-900 dark:text-gray-100 uppercase tracking-wide">{{ $ticket->plate_number }}</td>
                                        <td class="px-6 py-4">
                                            <span class="inline-flex px-3 py-1 rounded-full text-sm font-semibold bg-orange-100 text-orange-800 dark:bg-orange-900/50 dark:text-orange-200">
                                                {{ $ticket->parkingLot->lot_number ?? 'N/A' }}
                                            </span>
                                        </td>
                                        <td class="px-6 py-4 text-sm text-gray-900 dark:text-gray-100">
                                            {{ $ticket->entry_time->format('d/m H:i') }}<br>
                                            <span class="text-xs text-gray-500 dark:text-gray-400">{{ $ticket->entry_time->diffForHumans() }}</span>
                                        </td>
                                        @if($ticket->status === 'paid')
                                        <td class="px-6 py-4 text-sm text-gray-900 dark:text-gray-100 font-semibold">
                                            {{ $ticket->exit_time->format('d/m H:i') }}<br>
                                            <span class="text-xs text-gray-500 dark:text-gray-400">{{ $ticket->exit_time->diffForHumans($ticket->entry_time) }}</span>
                                        </td>
                                        <td class="px-6 py-4 font-bold text-lg text-green-600 dark:text-green-400">
                                            Rp {{ number_format($ticket->fee ?? 0) }}
                                        </td>
                                        @endif
                                        <td class="px-6 py-4">
                                            @if($ticket->status === 'entry')
                                            <span class="inline-flex px-3 py-1 rounded-full text-sm font-semibold bg-emerald-100 text-emerald-800 dark:bg-emerald-900/50 dark:text-emerald-200">
                                                Entry
                                            </span>
                                            @else
                                            <span class="inline-flex px-3 py-1 rounded-full text-sm font-semibold bg-amber-100 text-amber-800 dark:bg-amber-900/50 dark:text-amber-200">
                                                Paid
                                            </span>
                                            @endif
                                        </td>
                                    </tr>
                                    @endforeach
                                </tbody>
                            </table>
                            <div class="mt-6">
                                {{ $tickets->appends($filters)->links() }}
                            </div>
                        </div>
                    @endif
                </div>
            </div>
        </div>
    </div>
</div>
@endsection




