@extends('layouts.app')
@include('layouts.navigation')

@section('header')
    <h2 class="font-semibold text-xl text-gray-800 dark:text-gray-200 leading-tight">
        ⚙️ Pengaturan Tarif Parkir
    </h2>
@endsection

@section('content')
<div class="py-12">
    <div class="max-w-4xl mx-auto sm:px-6 lg:px-8">
        <div class="bg-white dark:bg-gray-800 overflow-hidden shadow-sm sm:rounded-lg">
            <div class="p-6 text-gray-900 dark:text-gray-100">
                @if (session('success'))
                    <div class="bg-green-100 border border-green-400 text-green-700 px-4 py-3 rounded-xl mb-6 dark:bg-green-900/30 dark:border-green-500 dark:text-green-200">
                        {{ session('success') }}
                    </div>
                @endif

                <form method="POST" action="{{ route('parking.rates') }}" class="space-y-6">
                    @csrf
                    <div class="grid grid-cols-1 md:grid-cols-3 gap-6">
                        {{-- Motor --}}
                        <div class="bg-gradient-to-br from-orange-50 to-yellow-50 dark:from-orange-900/20 p-6 rounded-2xl border border-orange-200 dark:border-orange-700">
                            <h3 class="text-lg font-bold mb-4 flex items-center text-gray-800 dark:text-gray-100">
                                🏍️ Motor
                            </h3>
                            <div class="space-y-4">
                                <div>
                                    <label class="block text-sm font-semibold mb-2">Jam Pertama</label>
                                    <input type="number" name="motor_first" value="{{ $rates->where('vehicle_type', 'motor')->first()?->first_hour ?? 3000 }}" class="w-full px-4 py-3 border border-gray-300 rounded-xl focus:ring-2 focus:ring-orange-500 focus:border-orange-500" min="0" required>
                                </div>
                                <div>
                                    <label class="block text-sm font-semibold mb-2">Jam Tambahan</label>
                                    <input type="number" name="motor_additional" value="{{ $rates->where('vehicle_type', 'motor')->first()?->additional_hour ?? 2000 }}" class="w-full px-4 py-3 border border-gray-300 rounded-xl focus:ring-2 focus:ring-orange-500 focus:border-orange-500" min="0" required>
                                </div>
                            </div>
                        </div>

                        {{-- Mobil --}}
                        <div class="bg-gradient-to-br from-blue-50 to-indigo-50 dark:from-blue-900/20 p-6 rounded-2xl border border-blue-200 dark:border-blue-700">
                            <h3 class="text-lg font-bold mb-4 flex items-center text-gray-800 dark:text-gray-100">
                                🚗 Mobil
                            </h3>
                            <div class="space-y-4">
                                <div>
                                    <label class="block text-sm font-semibold mb-2">Jam Pertama</label>
                                    <input type="number" name="mobil_first" value="{{ $rates->where('vehicle_type', 'mobil')->first()?->first_hour ?? 5000 }}" class="w-full px-4 py-3 border border-gray-300 rounded-xl focus:ring-2 focus:ring-blue-500 focus:border-blue-500" min="0" required>
                                </div>
                                <div>
                                    <label class="block text-sm font-semibold mb-2">Jam Tambahan</label>
                                    <input type="number" name="mobil_additional" value="{{ $rates->where('vehicle_type', 'mobil')->first()?->additional_hour ?? 3000 }}" class="w-full px-4 py-3 border border-gray-300 rounded-xl focus:ring-2 focus:ring-blue-500 focus:border-blue-500" min="0" required>
                                </div>
                            </div>
                        </div>

                        {{-- Truk --}}
                        <div class="bg-gradient-to-br from-red-50 to-rose-50 dark:from-red-900/20 p-6 rounded-2xl border border-red-200 dark:border-red-700">
                            <h3 class="text-lg font-bold mb-4 flex items-center text-gray-800 dark:text-gray-100">
                                🚚 Truk
                            </h3>
                            <div class="space-y-4">
                                <div>
                                    <label class="block text-sm font-semibold mb-2">Jam Pertama</label>
                                    <input type="number" name="truk_first" value="{{ $rates->where('vehicle_type', 'truk')->first()?->first_hour ?? 8000 }}" class="w-full px-4 py-3 border border-gray-300 rounded-xl focus:ring-2 focus:ring-red-500 focus:border-red-500" min="0" required>
                                </div>
                                <div>
                                    <label class="block text-sm font-semibold mb-2">Jam Tambahan</label>
                                    <input type="number" name="truk_additional" value="{{ $rates->where('vehicle_type', 'truk')->first()?->additional_hour ?? 5000 }}" class="w-full px-4 py-3 border border-gray-300 rounded-xl focus:ring-2 focus:ring-red-500 focus:border-red-500" min="0" required>
                                </div>
                            </div>
                        </div>
                    </div>

                    <div class="text-center">
                        <button type="submit" class="bg-emerald-600 hover:bg-emerald-700 text-white py-4 px-8 rounded-2xl font-bold text-xl shadow-2xl hover:shadow-3xl hover:-translate-y-1 transition-all duration-300">
                            💾 Simpan Perubahan Tarif
                        </button>
                    </div>
                </form>

                <div class="mt-12 p-6 bg-gradient-to-r from-gray-50 to-gray-100 dark:from-gray-900 dark:to-gray-800 rounded-2xl">
                    <h3 class="text-lg font-bold mb-4 text-gray-800 dark:text-gray-200">📋 Catatan</h3>
                    <ul class="space-y-2 text-sm text-gray-600 dark:text-gray-400">
                        <li>• Tarif dihitung per jam (pembulatan ke atas)</li>
                        <li>• Perubahan berlaku untuk ticket baru</li>
                        <li>• Minimum tarif = jam pertama</li>
                    </ul>
                </div>
            </div>
        </div>

        <div class="mt-6 text-center">
            <a href="{{ route('parking.dashboard') }}" class="inline-flex items-center px-6 py-3 bg-indigo-600 hover:bg-indigo-700 text-white font-semibold rounded-xl shadow-lg transition-all duration-200">
                ← Kembali Dashboard
            </a>
        </div>
    </div>
</div>
@endsection

