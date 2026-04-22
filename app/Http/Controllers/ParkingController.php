<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\Ticket;
use App\Models\ParkingLot;
use Illuminate\Support\Str;
use Illuminate\Support\Facades\DB;
use Carbon\Carbon;

class ParkingController extends Controller
{
    public function __construct()
    {
        $this->middleware('auth')->except(['entry', 'storeEntry', 'storeEntryAjax']);
    }

    public function dashboard(Request $request)
    {
        $availableLots = ParkingLot::available()->count();
        $totalLots = ParkingLot::count();
        $activeTickets = Ticket::where('status', 'entry')->count();
        $todayRevenue = Ticket::where('status', 'paid')
            ->whereDate('exit_time', today())
            ->sum('fee');
        
        $search = $request->query('search');
        $status_filter = $request->query('status', 'all');
        
        $tickets = collect(); // Default empty
        $filters = $request->query();
        $search = $request->get('search');
        $status_filter = $request->get('status', 'all');

        try {
            $query = Ticket::with('parkingLot');
            if ($search) {
                $query->where(function ($q) use ($search) {
                    $q->where('plate_number', 'like', '%' . $search . '%')
                      ->orWhere('ticket_code', 'like', '%' . $search . '%');
                });
            }
            if ($status_filter !== 'all') {
                $query->where('status', $status_filter);
            }
            
            $tickets = $query->orderBy('updated_at', 'desc')->paginate(15);
        } catch (\Exception $e) {
            // Fallback empty on DB error
            $tickets = collect()->paginate(15);
        }

        $availableLots = ParkingLot::available()->count() ?? 0;
        $totalLots = ParkingLot::count() ?? 0;
        $activeTickets = Ticket::where('status', 'entry')->count() ?? 0;
        $todayRevenue = (Ticket::where('status', 'paid')
            ->whereDate('exit_time', today())
            ->sum('fee')) ?? 0;
        
        return view('parking.dashboard', compact(
            'availableLots', 'totalLots', 'activeTickets', 
            'todayRevenue', 'tickets', 'filters', 'search', 'status_filter'
        ));
    }

    public function entry()
    {
        return view('parking.entry');
    }

    public function printEntryReceipt($ticketCode)
    {
        $ticket = Ticket::where('ticket_code', $ticketCode)
            ->where('status', 'entry') // Ensure active entry ticket
            ->with('parkingLot')
            ->first();
            
        if (!$ticket) {
            abort(404, 'Ticket not found or already processed. Please generate a new entry ticket.');
        }
        
        return view('parking.entry-receipt', compact('ticketCode', 'ticket'));
    }

    public function storeEntryAjax(Request $request)
    {
        try {
            $request->validate([
                'plate_number' => 'required|string|max:20',
                'vehicle_type' => 'required|in:mobil,motor,truk',
            ]);

            $ticketCode = 'T-' . now()->format('YmdHi') . '-' . strtoupper(Str::random(3));

            DB::transaction(function () use ($request, $ticketCode) {
                $activeTicket = Ticket::where('plate_number', strtoupper($request->plate_number))
                    ->where('status', 'entry')
                    ->first();

                if ($activeTicket) {
                    throw new \Exception('Kendaraan sudah parkir!');
                }


                $lot = ParkingLot::available()->first();
                $parkingLotId = $lot ? $lot->id : null;

                Ticket::create([
                    'ticket_code' => $ticketCode,
                    'plate_number' => strtoupper($request->plate_number),
                    'vehicle_type' => $request->vehicle_type,
                    'entry_time' => now(),
                    'parking_lot_id' => $parkingLotId,
                    'status' => 'entry',
                ]);

                if ($lot) {
                    $lot->update(['status' => 'occupied']);
                }

            });

            return response()->json([
                'success' => true,
                'ticketCode' => $ticketCode,
                'plate_number' => strtoupper($request->plate_number),
                'entry_time' => now()->format('d/m/Y H:i:s')
            ]);

        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => $e->getMessage()
            ], 400);
        }
    }

    public function storeEntry(Request $request)
    {
        $request->validate([
            'plate_number' => 'required|string|max:20|regex:/^[A-Z]{1,2}\\s?\\d{1,4}\\s?[A-Z]{1,3}$/',
            'vehicle_type' => 'required|in:mobil,motor,truk',
            'vehicle_model' => 'nullable|string|max:50',
        ], [
            'plate_number.regex' => 'Format nomor polisi tidak valid. Contoh: B 1234 ABC atau D 12 AB',
        ]);

        $ticketCode = 'T-' . now()->format('YmdHi') . '-' . strtoupper(Str::random(3));
        
        DB::transaction(function () use ($request, $ticketCode) {
            $activeTicket = Ticket::where('plate_number', strtoupper($request->plate_number))
                ->where('status', 'entry')
                ->first();
            if ($activeTicket) {
                return back()->with('error', 'Vehicle already parked! Ticket: ' . $activeTicket->ticket_code);
            }

            $lot = ParkingLot::available()->first();
            if (!$lot) {
                return back()->with('error', 'No available parking lots!');
            }

            Ticket::create([
                'ticket_code' => $ticketCode,
                'qr_code_data' => 'TICKET:' . $ticketCode . '|PLATE:' . strtoupper($request->plate_number) . '|ENTRY:' . now()->format('Y-m-d H:i:s'),
                'plate_number' => strtoupper($request->plate_number),
                'vehicle_type' => $request->vehicle_type,
                'vehicle_model' => $request->vehicle_model ?? '',
                'entry_time' => now(),
                'parking_lot_id' => $lot->id,
                'status' => 'entry',
            ]);

            $lot->update(['status' => 'occupied']);
        });

        return redirect()->route('parking.entry.receipt', $ticketCode)->with('success', 'Vehicle entered successfully! Ticket: ' . $ticketCode);
    }

    public function exitForm()
    {
        return view('parking.exit');
    }

    public function processExit(Request $request)
    {
        $request->validate([
            'ticket_code' => 'required|string|max:50',
        ]);

        $ticket = Ticket::where('ticket_code', $request->ticket_code)
            ->where('status', 'entry')
            ->with('parkingLot')
            ->first();

        if (!$ticket) {
            return back()->with('error', 'Invalid or already processed ticket!');
        }

        $ticket->exit_time = now();
        $ticket->status = 'paid';
        $ticket->fee = $ticket->calculateFee();
        $ticket->save();

        $ticket->parkingLot->update(['status' => 'available']);

        return view('parking.receipt', compact('ticket'));
    }

    public function scanTicket(Request $request)
    {
        $request->validate([
            'ticket_code' => 'required|string|max:50',
        ]);

        $ticket = Ticket::where('ticket_code', $request->ticket_code)
            ->where('status', 'entry')
            ->with('parkingLot')
            ->first();

        if (!$ticket) {
            return response()->json([
                'valid' => false,
                'message' => 'Invalid or already processed ticket!'
            ], 404);
        }

        $fee = $ticket->calculateFee();
        $duration = $ticket->entry_time->diffForHumans(now());

        return response()->json([
            'valid' => true,
            'ticket_code' => $ticket->ticket_code,
            'plate_number' => $ticket->plate_number,
            'entry_time' => $ticket->entry_time->format('d/m/Y H:i'),
            'duration' => $duration,
            'fee' => 'Rp ' . number_format($fee),
            'lot' => $ticket->parkingLot->lot_number
        ]);
    }

    public function reports(Request $request)
    {
        $todayRevenue = Ticket::where('status', 'paid')
            ->whereDate('exit_time', today())
            ->sum('fee');
            
        $weeklyRevenue = Ticket::where('status', 'paid')
            ->where('exit_time', '>=', now()->subDays(7))
            ->sum('fee');
            
        $monthlyRevenue = Ticket::where('status', 'paid')
            ->whereMonth('exit_time', now()->month)
            ->sum('fee');
            
        $vehicleTypeStats = Ticket::where('status', 'paid')
            ->selectRaw('vehicle_type, SUM(fee) as total_fee, COUNT(*) as count')
            ->groupBy('vehicle_type')
            ->get();
            
        $dailyData = [];
        for ($i = 6; $i >= 0; $i--) {
            $date = now()->subDays($i)->format('Y-m-d');
            $revenue = Ticket::where('status', 'paid')
                ->whereDate('exit_time', $date)
                ->sum('fee');
            $dailyData[] = (float) $revenue;
        }
        
        $dailyLabels = [];
        for ($i = 6; $i >= 0; $i--) {
            $dailyLabels[] = now()->subDays($i)->format('d M');
        }

        return view('parking.reports', compact(
            'todayRevenue', 'weeklyRevenue', 'monthlyRevenue', 
            'vehicleTypeStats', 'dailyData', 'dailyLabels'
        ));
    }

    public function ratesIndex()
    {
        $rates = \App\Models\Rate::all();
        return view('parking.rates', compact('rates'));
    }

    public function ratesUpdate(Request $request)
    {
        $request->validate([
            'motor_first' => 'required|numeric|min:0',
            'motor_additional' => 'required|numeric|min:0',
            'mobil_first' => 'required|numeric|min:0',
            'mobil_additional' => 'required|numeric|min:0',
            'truk_first' => 'required|numeric|min:0',
            'truk_additional' => 'required|numeric|min:0',
        ]);

        $types = ['motor', 'mobil', 'truk'];
        foreach ($types as $type) {
            \App\Models\Rate::updateOrCreate(
                ['vehicle_type' => $type],
                [
                    'first_hour' => (int) $request->input($type . '_first'),
                    'additional_hour' => (int) $request->input($type . '_additional'),
                ]
            );
        }

        return redirect()->route('rates')->with('success', 'Tarif parkir berhasil diupdate!');
    }
}
