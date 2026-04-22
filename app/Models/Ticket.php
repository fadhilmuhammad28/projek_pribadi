<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Carbon\Carbon;

class Ticket extends Model
{
    use HasFactory;

    protected $fillable = [
        'ticket_code',
        'plate_number',
        'vehicle_type',
        'vehicle_model',
        'entry_time',
        'exit_time',
        'parking_lot_id',
        'status',
        'fee',
        'qr_code_data',
    ];

    public function scopeActive($query)
    {
        return $query->where('status', 'entry');
    }

    public function scopePaid($query)
    {
        return $query->where('status', 'paid');
    }

    protected $casts = [
        'entry_time' => 'datetime',
        'exit_time' => 'datetime',
        'fee' => 'decimal:2',
    ];

    public function parkingLot(): BelongsTo
    {
        return $this->belongsTo(ParkingLot::class);
    }

    public function calculateFee(): float
    {
        if (!$this->exit_time) {
            return 0;
        }

        // Ubah ke timestamp
        $masuk = $this->entry_time->timestamp;
        $keluar = $this->exit_time->timestamp;

        // Hitung selisih waktu dalam detik
        $selisih = $keluar - $masuk;

        // Ubah ke jam (dibulatkan ke atas)
        $jam = ceil($selisih / 3600);

        // Use configurable rates
        $rate = \App\Models\Rate::forType($this->vehicle_type);
        if ($rate) {
            $first_hour = $rate->first_hour;
            $additional_hour = $rate->additional_hour;
        } else {
            // Fallback defaults
            switch ($this->vehicle_type) {
                case 'motor':
                    $first_hour = 3000;
                    $additional_hour = 2000;
                    break;
                case 'mobil':
                    $first_hour = 5000;
                    $additional_hour = 3000;
                    break;
                case 'truk':
                    $first_hour = 8000;
                    $additional_hour = 5000;
                    break;
                default:
                    $first_hour = 5000;
                    $additional_hour = 3000;
            }
        }
        
        if ($jam <= 1) {
            $biaya = $first_hour;
        } else {
            $biaya = $first_hour + (($jam - 1) * $additional_hour);
        }

        return (float)$biaya;
    }

    public function generateTicketCode(): string
    {
        return 'TICKET-' . now()->format('Ymd-His');
    }
}

