@extends('layouts.app')
@include('layouts.navigation')

@section('header')
    <h2 class="font-semibold text-xl text-gray-800 dark:text-gray-200 leading-tight">
        ➕ Vehicle Entry
    </h2>
@endsection

@section('content')
<div class="py-12">
    <div class="max-w-2xl mx-auto sm:px-6 lg:px-8">
        <div class="bg-gradient-to-br from-white to-blue-50 dark:from-slate-50 dark:to-gray-100 backdrop-blur-sm border border-blue-100 dark:border-gray-200 overflow-hidden shadow-xl sm:rounded-3xl">
            <div class="p-8">
                <form id="entryForm" method="POST" action="{{ route('parking.entry.store') }}" class="space-y-6">
                    @csrf

                    <!-- Plate Number -->
                    <div>
                        <label for="plate_number" class="block text-sm font-semibold font-color:black mb-2">
                            Nomor Polisi / Plate Number <span class="text-emerald-600">*</span>
                        </label>
                        <input id="plate_number" class="block w-full px-4 py-3 border border-gray-300 dark:border-gray-600 rounded-xl bg-white dark:bg-white/90 backdrop-blur-sm shadow-md focus:ring-2 focus:ring-emerald-500 focus:border-emerald-500 dark:focus:ring-emerald-400 transition-all duration-200 uppercase tracking-wide text-lg font-medium placeholder-gray-400 dark:placeholder-gray-500 @error('plate_number') ring-2 ring-red-500 @enderror" 
                               type="text" name="plate_number" value="{{ old('plate_number') }}" required autofocus autocomplete="plate" placeholder="B 1234 ABC" />
                        @error('plate_number')
                            <p class="mt-2 text-sm text-red-600 dark:text-red-400 flex items-center">
                                <svg class="w-4 h-4 mr-1" fill="currentColor" viewBox="0 0 20 20">
                                    <path fill-rule="evenodd" d="M18 10a8 8 0 11-16 0 8 8 0 0116 0zm-7 4a1 1 0 11-2 0 1 1 0 012 0zm-1-9a1 1 0 00-1 1v4a1 1 0 102 0V6a1 1 0 00-1-1z" clip-rule="evenodd" />
                                </svg>
                                {{ $message }}
                            </p>
                        @enderror
                    </div>

                    <!-- Vehicle Type -->
                    <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
                        <div>
                            <label for="vehicle_type" class="block text-sm font-semibold font-color:black mb-2">
                                Jenis Kendaraan <span class="text-emerald-600">*</span>
                            </label>
                            <select id="vehicle_type" name="vehicle_type" class="block w-full px-4 py-3 pr-10 border border-gray-300 dark:border-gray-600 rounded-xl bg-white dark:bg-white/90 backdrop-blur-sm shadow-md focus:ring-2 focus:ring-emerald-500 focus:border-emerald-500 dark:focus:ring-emerald-400 transition-all duration-200 text-lg font-medium @error('vehicle_type') ring-2 ring-red-500 @enderror" required>
                                <option class="fontcolor-grey-300">Jenis Kendaraan</option>
                                <option value="mobil" {{ old('vehicle_type') == 'mobil' ? 'selected' : '' }}>🚗 Mobil</option>
                                <option value="motor" {{ old('vehicle_type') == 'motor' ? 'selected' : '' }}>🏍️ Motor</option>
                                <option value="truk" {{ old('vehicle_type') == 'truk' ? 'selected' : '' }}>🚚 Truk</option>
                            </select>
                            @error('vehicle_type')
                                <p class="mt-2 text-sm text-red-600 dark:text-red-400">{{ $message }}</p>
                            @enderror
                        </div>

                        <!-- Vehicle Model -->
                        <div>
                            <label for="vehicle_model" class="block text-sm font-semibold font-color:black mb-2">
                                Model Kendaraan (Opsional)
                            </label>
                            <input id="vehicle_model" class="block w-full px-4 py-3 border border-gray-300 dark:border-gray-600 rounded-xl bg-white dark:bg-white/90 backdrop-blur-sm shadow-md focus:ring-2 focus:ring-emerald-500 focus:border-emerald-500 dark:focus:ring-emerald-400 transition-all duration-200 text-lg placeholder-gray-400 dark:placeholder-gray-500 @error('vehicle_model') ring-2 ring-red-500 @enderror" 
                                   type="text" name="vehicle_model" value="{{ old('vehicle_model') }}" autocomplete="vehicle-model" placeholder="Toyota Avanza, Honda Beat, etc" />
                            @error('vehicle_model')
                                <p class="mt-2 text-sm text-red-600 dark:text-red-400">{{ $message }}</p>
                            @enderror
                        </div>
                    </div>

<div class="flex flex-col sm:flex-row gap-4 pt-4">
                        <a href="{{ route('parking.dashboard') }}" class="flex-1 bg-white/60 dark:bg-slate-100/50 border border-gray-300 dark:border-gray-600 font-color:dark-grey py-4 px-8 rounded-xl font-semibold text-lg shadow-lg hover:shadow-xl hover:bg-gray-50 dark:hover:bg-slate-200 transition-all duration-200 flex items-center justify-center">
                            ← Kembali Dashboard
                        </a>
                        <button type="submit" id="generateBtn" class="flex-1 bg-emerald-600 hover:bg-emerald-700 text-white font-bold py-4 px-6 rounded-xl shadow-lg hover:shadow-xl hover:-translate-y-0.5 transition-all duration-200 flex items-center justify-center space-x-2 text-lg disabled:bg-gray-400 disabled:cursor-not-allowed">
                            <span>📄</span>
                            <span>Generate Struk Parkir</span>
                        </button>
                    </div>
                </form>

                <script>
                document.addEventListener('DOMContentLoaded', function() {
                    const form = document.getElementById('entryForm');
                    const btn = document.getElementById('generateBtn');

                    form.addEventListener('submit', function(e) {
                        e.preventDefault();
                        
                        // Validate
                        const plate = document.getElementById('plate_number').value.trim();
                        const type = document.getElementById('vehicle_type').value;
                        if (!plate || !type) {
                            alert('Mohon lengkapi nomor polisi dan jenis kendaraan!');
                            return;
                        }

                        btn.disabled = true;
                        btn.innerHTML = '<span>⏳</span><span>Memproses...</span>';

                        const formData = new FormData(form);\n\n                        fetch("{{ route('parking.entry.store.ajax') }}", {\n                            method: 'POST',\n                            body: formData\n                        })\n                        .then(response => response.json())\n                        .then(data => {
                            if (data.success) {\n                                window.location.href = `{{ route('parking.entry.receipt', ':code') }}`.replace(':code', data.ticketCode);\n                            } else {
                                alert('Error: ' + (data.message || 'Gagal generate ticket'));
                                btn.disabled = false;
                                btn.innerHTML = '<span>📄</span><span>Generate Struk Parkir</span>';
                            }
                        })
                        .catch(error => {
                            alert('Terjadi kesalahan koneksi!');
                            btn.disabled = false;
                            btn.innerHTML = '<span>📄</span><span>Generate Struk Parkir</span>';
                        });
                    });
                });
                </script>

                <!-- Info Box -->
                <div class="mt-8 p-6 bg-gradient-to-r from-emerald-50 to-sky-50 dark:from-emerald-500/10 dark:to-sky-500/10 border border-emerald-100 dark:border-emerald-500/30 rounded-2xl shadow-xl backdrop-blur-sm">
                    <div class="flex items-start gap-4">
                        <div class="p-3 bg-gradient-to-r from-emerald-100 to-lime-100 dark:from-emerald-400 dark:to-lime-400 rounded-2xl flex-shrink-0 shadow-lg">
                            <svg class="w-7 h-7 text-emerald-600 dark:text-emerald-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12l2 2 4-4m6 2a9 9 0 11-18 0 9 9 0 0118 0z"></path>
                            </svg>
                        </div>
                        <div>
                            <h4 class="text-xl font-bold font-color:black mb-3">Informasi Tarif & Petunjuk</h4>
                            <ul class="space-y-3 text-sm">
                                <li class="flex items-center p-3 bg-white/80 dark:bg-white/40 rounded-xl">
                                    <span class="font-bold text-blue-700 dark:text-blue-300 mr-3">📱</span>
                                    <span>Scan barcode struk di <span class="font-bold">pintu keluar otomatis</span></span>
                                </li>
                                <li class="flex items-center p-3 bg-white/80 dark:bg-white/40 rounded-xl">
                                    <span class="font-bold text-orange-700 dark:text-orange-300 mr-3">⏰</span>
                                    <span>Pembulatan ke jam penuh (minimum 1 jam)</span>
                                </li>
                            </ul>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>

@endsection

