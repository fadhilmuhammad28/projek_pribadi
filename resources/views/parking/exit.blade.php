@extends('layouts.app')
@include('layouts.navigation')

@section('content')
<div class="max-w-4xl mx-auto p-6">
        <div class="bg-white rounded-xl shadow-xl overflow-hidden">
        {{-- Header --}}
        <div class="navbar-container bg-gradient-to-r from-blue-600 to-indigo-600 dark:bg-gray-900 p-8 text-white text-center">
            <h1 class="text-4xl font-bold mb-2"> Pintu Keluar Parkir</h1>
            <p class="text-xl opacity-90">Scan struk ticket untuk keluar</p>
        </div>

        <div class="p-8 grid lg:grid-cols-2 gap-8 items-start">
                
                <div id="scanResult" class="mt-6 p-6 rounded-xl bg-gray-50 border hidden">
                    <h3 class="text-xl font-bold mb-4 text-gray-800">Scan Result</h3>
                    <div id="ticketInfo" class="space-y-3 text-sm"></div>
                        <button id="confirmExit" class="w-full mt-6 navbar-button bg-blue-600 hover:bg-blue-700 text-white py-3 px-4 rounded-lg font-semibold transition duration-200 hidden">
                            ✅ Process Exit & Print Receipt
                        </button>
                </div>

                {{-- Hidden form for submission --}}
                <form id="exitForm" method="POST" action="{{ route('parking.exit.process') }}" class="hidden">
                    @csrf
                    <input type="text" name="ticket_code" id="hiddenTicketCode">
                </form>
            </div>

            {{-- Manual Input & Preview --}}
            <div>
                <div class="navbar-container/90 bg-blue-50 p-6 rounded-xl border border-blue-200 mb-6 opacity-90">
                    <h3 class="text-lg font-bold mb-4 text-blue-800 flex items-center">
                        <svg class="w-5 h-5 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 15v2m-6 4h12a2 2 0 002-2v-6a2 2 0 00-2-2H6a2 2 0 00-2 2v6a2 2 0 002 2zm10-10V7a4 4 0 00-8 0v4h8z"></path>
                        </svg>
                        Fallback: Manual Input
                    </h3>
                    <form method="POST" action="{{ route('parking.exit.process') }}">
                        @csrf
                        <div class="mb-4">
                            <label class="block text-sm font-semibold text-gray-700 mb-2">Ticket Code / No. Struk</label>
                           <input type="text" name="ticket_code" value="{{ old('ticket_code') }}" 
                                   class="w-full px-4 py-3 border border-gray-300 rounded-lg focus:ring-2 focus:ring-blue-500 focus:border-blue-500 @error('ticket_code') border-red-500 @enderror uppercase input-field-input"
                                   placeholder="T-20241001-123ABC" required autocomplete="off">
                            @error('ticket_code')
                                <p class="mt-2 text-sm text-red-600">{{ $message }}</p>
                            @enderror
                        </div>
                        <button type="submit" class="w-full bg-blue-600 text-white py-3 px-4 navbar-button rounded-lg font-semibold hover:bg-blue-700 focus:ring-2 focus:ring-blue-500">
                            Process Exit
                        </button>
                    </form>
                </div>

                <div id="previewInfo" class="bg-emerald-50 p-6 rounded-xl border border-emerald-200 hidden">
                    <h3 class="text-xl font-bold mb-4 text-emerald-800">Ticket Valid ✅</h3>
                    <div class="space-y-3 text-sm">
                        <div><span class="font-semibold">Ticket:</span> <span id="previewTicket" class="font-mono"></span></div>
                        <div><span class="font-semibold">Plat:</span> <span id="previewPlate"></span></div>
                        <div><span class="font-semibold">Lot:</span> <span id="previewLot"></span></div>
                        <div><span class="font-semibold">Masuk:</span> <span id="previewEntry"></span></div>
                        <div><span class="font-semibold">Durasi:</span> <span id="previewDuration"></span></div>
                        <div class="text-2xl font-bold text-green-600 mt-4"><span id="previewFee"></span></div>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>

{{-- QR Scanner Library --}}
<script src="https://unpkg.com/html5-qrcode@2.3.8/html5-qrcode.min.js"></script>
<script>
    let scanner = null;
    const scannerContainer = document.getElementById('scanner');
    const startBtn = document.getElementById('startScanner');
    const stopBtn = document.getElementById('stopScanner');
    const scanResult = document.getElementById('scanResult');
    const ticketInfo = document.getElementById('ticketInfo');
    const confirmBtn = document.getElementById('confirmExit');
    const hiddenTicketCode = document.getElementById('hiddenTicketCode');
    const previewInfo = document.getElementById('previewInfo');

    // Start Scanner
    startBtn.addEventListener('click', async () => {
        try {
            scanner = new Html5Qrcode('scanner');
            const config = { fps: 10, qrbox: { width: 250, height: 250 } };
            await scanner.start(
                { facingMode: 'environment' },
                config,
                onScanSuccess,
                onScanError
            );
            startBtn.classList.add('hidden');
            stopBtn.classList.remove('hidden');
        } catch (err) {
            alert('Error starting scanner: ' + err);
        }
    });

    // Stop Scanner
    stopBtn.addEventListener('click', () => {
        if (scanner) {
            scanner.stop().then(() => {
                scanner.clear();
                startBtn.classList.remove('hidden');
                stopBtn.classList.add('hidden');
            });
        }
    });

    // On Scan Success
    function onScanSuccess(decodedText, decodedResult) {
        console.log('Scanned:', decodedText);
        
        // Validate via AJAX
        fetch('{{ route('parking.exit.scan') }}', {
            method: 'POST',
            headers: {
                'Content-Type': 'application/json',
                'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').getContent()
            },
            body: JSON.stringify({ ticket_code: decodedText })
        })
        .then(response => response.json())
        .then(data => {
            if (data.valid) {
                showScanResult(data);
                scanner.stop().then(() => scanner.clear());
            } else {
                alert(data.message || 'Invalid ticket!');
            }
        })
        .catch(err => alert('Validation error: ' + err));
    }

    function onScanError() {
        // Silent fail
    }

    function showScanResult(data) {
        hiddenTicketCode.value = data.ticket_code;
        
        // Update preview
        document.getElementById('previewTicket').textContent = data.ticket_code;
        document.getElementById('previewPlate').textContent = data.plate_number;
        document.getElementById('previewLot').textContent = data.lot;
        document.getElementById('previewEntry').textContent = data.entry_time;
        document.getElementById('previewDuration').textContent = data.duration;
        document.getElementById('previewFee').textContent = data.fee;
        
        previewInfo.classList.remove('hidden');
        scanResult.classList.remove('hidden');
        confirmBtn.classList.remove('hidden');
        confirmBtn.onclick = () => document.getElementById('exitForm').submit();
        
        // Auto-scroll
        scanResult.scrollIntoView({ behavior: 'smooth' });
    }
</script>

<style>
#scanner video {
    width: 100% !important;
    height: 100% !important;
    object-fit: cover;
}
@media (max-width: 1024px) {
    .scanner-section { order: 2; }
}
</style>
@endsection

