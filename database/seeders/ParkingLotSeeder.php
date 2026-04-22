<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use App\Models\ParkingLot;

class ParkingLotSeeder extends Seeder
{
    public function run(): void
    {
        $lots = [];
        for ($i = 1; $i <= 25; $i++) {
            $lots[] = ['lot_number' => 'A' . $i];
            $lots[] = ['lot_number' => 'B' . $i];
        }

        foreach ($lots as $lot) {
            ParkingLot::create($lot);
        }
    }
}

