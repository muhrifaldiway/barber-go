<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Payment extends Model
{
    use HasFactory;

    protected $fillable = [
        'booking_id', 'payment_method', 'amount', 'status',
        'gateway_reference', 'gateway_response', 'payment_url',
        'paid_at', 'expired_at', 'metadata',
    ];

    protected $casts = [
        'amount' => 'decimal:2',
        'paid_at' => 'datetime',
        'expired_at' => 'datetime',
        'metadata' => 'array',
    ];

    public function booking()
    {
        return $this->belongsTo(Booking::class);
    }
}