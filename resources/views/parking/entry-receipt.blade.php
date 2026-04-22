@extends('layouts.app')

@section('header')
    <h2 class="font-semibold text-lg text-gray-800 dark:text-gray-200 leading-tight">
        🖨️ Entry Receipt - {{ $ticketCode }}
    </h2>
@endsection

@section('content')
<script src="https://cdn.jsdelivr.net/npm/jsbarcode@3.11.5/dist/JsBarcode.all.min.js"></script>

<div class="py-6">
    <div class="max-w-lg mx-auto sm:px-4 lg:px-4">
        <div class="bg-white p-4 print:p-2">
            <div class="text-center mb-4 print:mb-2">
                <h1 class="text-xl font-bold mb-1">STRUK MASUK PARKIR</h1>
                <p class="text-base font-semibold">Sistem Parkir 1 Gerbang</p>
            </div>

            <table class="w-full text-sm print:text-xs">
                <tr>
                    <td class="font-semibold py-1 w-1/3">No. Ticket:</td>
                    <td class="font-mono text-lg font-bold px-2">{{ $ticketCode }}</td>
                </tr>
                <tr>
                    <td class="font-semibold py-1 w-1/3">Nomor Polisi:</td>
                    <td class="font-bold uppercase tracking-wide px-2">{{ $ticket->plate_number }}</td>
                </tr>
                <tr>
                    <td class="font-semibold py-1 w-1/3">Waktu Masuk:</td>
                    <td class="font-bold px-2">{{ $ticket->entry_time->format('d/m/Y H:i:s') }}</td>
                </tr>
                @if($ticket->parkingLot)
                <tr>
                    <td class="font-semibold py-1 w-1/3">Lot Parkir:</td>
                    <td class="px-2">{{ $ticket->parkingLot->lot_number }} - {{ $ticket->vehicle_type == 'mobil' ? 'Mobil' : ($ticket->vehicle_type == 'motor' ? 'Motor' : 'Truk') }}</td>
                </tr>
                @endif
                <tr>
                    <td class="font-semibold py-1 w-1/3">Petunjuk:</td>
                    <td class="font-bold px-2">Simpan struk ini untuk keluar otomatis via scan barcode</td>
                </tr>
            </table>

            <!-- Barcode -->
            <div class="text-center mt-6 print:mt-4 mb-4">
                <svg id="barcode" class="inline-block mx-auto" style="width: 250px; height: 60px;"></svg>
            </div>

            <div class="text-center text-xs print:hidden mb-4">
                Scan Barcode di pintu keluar otomatis | Dicetak: {{ now()->format('d/m/Y H:i:s') }}
            </div>

            <!-- Actions -->
            <div class="flex flex-col sm:flex-row gap-2 pt-2 border-t print:hidden">
                <a href="{{ route('parking.dashboard') }}" class="flex-1 bg-gray-600 hover:bg-gray-700 text-white font-bold py-2 px-4 rounded text-sm text-center">
                    ← Kembali Dashboard
                </a>
                <button onclick="printReceipt()" class="flex-1 bg-emerald-600 hover:bg-emerald-700 text-white font-bold py-2 px-4 rounded text-sm text-center">
                    <svg class="w-4 h-4 inline mr-1" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M17 17h2a2 2 0 002-2v-4a2 2 0 00-2-2H5a2 2 0 00-2 2v4a2 2 0 002 2h2m2 4h6a2 2 0 002-2v-4a2 2 0 00-2-2H9a2 2 0 00-2 2v4a2 2 0 002 2zm8-12V5a2 2 0 00-2-2H9a2 2 0 00-2 2v4a2 2 0 002 2h8a2 2 0 002-2z"></path>
                    </svg>
                    Print Struk
                </button>
            </div>
        </div>
    </div>
</div>

<script>
    JsBarcode("#barcode", '{{ $ticketCode }}', {
        format: "CODE128",
        width: 1,
        height: 60,
        displayValue: true,
        fontSize: 14,
        textMargin: 4,
        margin: 5
    });

    function printReceipt() {
        window.print();
        setTimeout(() => {
            if (confirm('Struk berhasil dicetak! Kembali ke dashboard?')) {
                window.location.href = "{{ route('parking.dashboard') }}";
            }
        }, 1000);
    }

    setTimeout(printReceipt, 2000);
</script>

<style>
@media print {
    nav, header, footer, .print\\:hidden { display: none !important; }
    body { background: white !important; margin: 0 !important; }
    main { padding: 0 !important; margin: 0 !important; }
    table { width: 70mm !important; font-size: 10px !important; }
    * { color: black !important; background: white !important; box-shadow: none !important; }
}
</style>
@endsection

