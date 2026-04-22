@extends('layouts.app')

@section('content')
<div class="max-w-2xl mx-auto">
    <div class="bg-white p-8 rounded-lg shadow-lg print:p-4 print:shadow-none">
        <div class="text-center mb-8">
            <h1 class="text-3xl font-bold text-gray-900 mb-2">STRUK PARKIR</h1>
            <p class="text-lg text-gray-600">Sistem Parkir 1 Gerbang</p>
        </div>

        <div class="space-y-4 mb-8">
            <div>
                <span class="font-semibold">No. Ticket:</span>
                <span class="font-bold text-xl ml-2">{{ $ticket->ticket_code }}</span>
            </div>
            <div>
                <span class="font-semibold">Nomor Polisi:</span>
                <span class="font-bold ml-2">{{ $ticket->plate_number }}</span>
            </div>
            <div>
                <span class="font-semibold">Lot Parkir:</span>
                <span class="font-bold ml-2">{{ $ticket->parkingLot->lot_number }}</span>
            </div>
            <div>
                <span class="font-semibold">Masuk:</span>
                <span class="ml-2">{{ $ticket->entry_time->format('d/m/Y H:i:s') }}</span>
            </div>
            <div>
                <span class="font-semibold">Keluar:</span>
                <span class="ml-2">{{ $ticket->exit_time->format('d/m/Y H:i:s') }}</span>
            </div>
            <div class="border-t pt-4">
                <span class="font-semibold text-xl">Durasi: {{ $ticket->entry_time->diffForHumans($ticket->exit_time) }}</span>
            </div>
            <div class="border-t pt-4 text-2xl font-bold text-green-600 text-center">
                biaya parkir: Rp {{ number_format($ticket->fee, 0, ',', '.') }}
            </div>
        </div>

        <div class="text-center mb-6">


            <svg id="barcode" class="inline-block p-4 bg-white border-2 border-gray-200 rounded-xl shadow-md mx-auto" style="width: 160px; height: 50px;"></svg>



            <script src="https://cdn.jsdelivr.net/npm/jsbarcode@3.11.5/dist/JsBarcode.all.min.js"></script>
            <script>

                JsBarcode("#barcode", '{{ $ticket->ticket_code }}', {

                    format: "CODE128",
                    width: 2,
                    height: 80,
                    displayValue: true,
                    fontSize: 16
                });
            </script>

        </div>
        
        <div class="flex space-x-4 pt-8 border-t">
            <a href="{{ route('parking.dashboard') }}" class="flex-1 bg-blue-600 text-white py-3 px-4 rounded-lg font-semibold text-center hover:bg-blue-700">
                Dashboard
            </a>
            <button onclick="window.print()" class="flex-1 bg-gray-600 text-white py-3 px-4 rounded-lg font-semibold hover:bg-gray-700">
                Print Struk
            </button>
        </div>
    </div>
</div>

<style>
@media print {
    nav, .flex.space-x-4 { display: none !important; }
    main { padding: 0 !important; }
    .print\:p-4 { padding: 1rem !important; }
}
</style>
@endsection

