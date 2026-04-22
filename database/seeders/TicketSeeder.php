<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use App\Models\Ticket;
use App\Models\ParkingLot;
use Carbon\Carbon;

class TicketSeeder extends Seeder
{
    public function run(): void
    {
        $lots = ParkingLot::inRandomOrder()->limit(5)->pluck('id')->toArray();
        

        // Paid tickets
        Ticket::create([
            'ticket_code' => 'T-PAID001',
            'plate_number' => 'E 999 GHI',
            'vehicle_type' => 'truk',
            'entry_time' => Carbon::now()->subDay(),
            'exit_time' => Carbon::now()->subHour(3),
            'fee' => 25000,
            'parking_lot_id' => $lots[1],
            'status' => 'paid',
        ]);
    }
}

