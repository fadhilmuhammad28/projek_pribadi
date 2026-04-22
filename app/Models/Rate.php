<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Rate extends Model
{
    use HasFactory;

    protected $fillable = [
        'vehicle_type',
        'first_hour',
        'additional_hour',
    ];

    protected $casts = [
        'first_hour' => 'decimal:0',
        'additional_hour' => 'decimal:0',
    ];

    public static function forType($type)
    {
        return self::where('vehicle_type', $type)->first();
    }
}

