@extends('layouts.app')
@include('layouts.navigation')

@section('header')
    <h2 class="font-semibold text-xl text-gray-800 dark:text-gray-200 leading-tight">
        📊 Laporan Parkir
    </h2>
@endsection

@section('content')
<div class="py-12">
    <div class="max-w-7xl mx-auto sm:px-6 lg:px-8">
        <div class="bg-white dark:bg-gray-800 overflow-hidden shadow-sm sm:rounded-lg">
            <div class="p-6 text-gray-900 dark:text-gray-100">
                {{-- Revenue Stats --}}
                <div class="grid grid-cols-1 md:grid-cols-3 gap-6 mb-8">
                    <div class="bg-gradient-to-r from-emerald-400 to-emerald-500 p-6 rounded-xl shadow-lg text-white text-center">
                        <div class="text-3xl font-bold mb-1">Rp {{ number_format($todayRevenue ?? 0) }}</div>
                        <div class="text-sm opacity-90">Pendapatan Hari Ini</div>
                    </div>
                    <div class="bg-gradient-to-r from-blue-400 to-blue-500 p-6 rounded-xl shadow-lg text-white text-center">
                        <div class="text-3xl font-bold mb-1">Rp {{ number_format($weeklyRevenue ?? 0) }}</div>
                        <div class="text-sm opacity-90">Minggu Ini</div>
                    </div>
                    <div class="bg-gradient-to-r from-purple-400 to-purple-500 p-6 rounded-xl shadow-lg text-white text-center">
                        <div class="text-3xl font-bold mb-1">Rp {{ number_format($monthlyRevenue ?? 0) }}</div>
                        <div class="text-sm opacity-90">Bulan Ini</div>
                    </div>
                </div>

                <div class="grid grid-cols-1 lg:grid-cols-2 gap-8 mb-8">
                    {{-- Daily Revenue Chart --}}
                    <div class="bg-white dark:bg-gray-800 p-6 rounded-xl shadow-lg">
                        <h3 class="text-xl font-bold mb-6 flex items-center text-gray-800 dark:text-gray-100">
                            📈 Pendapatan Harian (7 Hari Terakhir)
                        </h3>
                        <canvas id="dailyRevenueChart" height="100"></canvas>
                    </div>

                    {{-- Vehicle Type Stats --}}
                    <div class="bg-white dark:bg-gray-800 p-6 rounded-xl shadow-lg">
                        <h3 class="text-xl font-bold mb-6 flex items-center text-gray-800 dark:text-gray-100">
                            🚗 Statistik Jenis Kendaraan
                        </h3>
                        <canvas id="vehicleTypeChart" height="100"></canvas>
                    </div>
                </div>

                <div class="text-center">
                    <a href="{{ route('parking.dashboard') }}" class="inline-flex items-center px-6 py-3 bg-indigo-600 hover:bg-indigo-700 text-white font-semibold rounded-xl shadow-lg transition-all duration-200">
                        ← Kembali ke Dashboard
                    </a>
                </div>
            </div>
        </div>
    </div>
</div>

{{-- Chart.js --}}
<script src="https://cdn.jsdelivr.net/npm/chart.js"></script>
<script>
    // Daily Revenue Line Chart
    const ctx1 = document.getElementById('dailyRevenueChart').getContext('2d');
    new Chart(ctx1, {
        type: 'line',
        data: {
            labels: @json($dailyLabels),
            datasets: [{
                label: 'Pendapatan (Rp)',
                data: @json($dailyData),
                borderColor: 'rgb(34, 197, 94)',
                backgroundColor: 'rgba(34, 197, 94, 0.1)',
                tension: 0.4,
                fill: true
            }]
        },
        options: {
            responsive: true,
            scales: {
                y: {
                    beginAtZero: true
                }
            }
        }
    });

    // Vehicle Type Pie Chart (use sample if empty)
    const ctx2 = document.getElementById('vehicleTypeChart').getContext('2d');
    const vehicleData = @json(collect($vehicleTypeStats)->values());
    const vehicleLabels = ['Mobil', 'Motor', 'Truk'];
    new Chart(ctx2, {
        type: 'pie',
        data: {
            labels: vehicleLabels,
            datasets: [{
                data: vehicleData->isEmpty() ? [30, 50, 20] : $vehicleTypeStats->values(),
                backgroundColor: ['#3B82F6', '#10B981', '#F59E0B']
            }]
        },
        options: {
            responsive: true
        }
    });
</script>
@endsection

