<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Str;

class Booking extends Model
{
    use HasFactory;

    protected $fillable = [
        'booking_code', 'customer_id', 'barber_id',
        'booking_date', 'booking_time', 'estimated_end_time',
        'address', 'address_detail', 'latitude', 'longitude', 'notes',
        'status', 'subtotal', 'discount', 'voucher_code',
        'travel_fee', 'total_price',
        'payment_method', 'payment_status',
        'confirmed_at', 'started_at', 'completed_at',
        'cancelled_at', 'cancel_reason', 'cancelled_by',
    ];

    protected $casts = [
        'booking_date' => 'date',
        'subtotal' => 'decimal:2',
        'discount' => 'decimal:2',
        'travel_fee' => 'decimal:2',
        'total_price' => 'decimal:2',
        'confirmed_at' => 'datetime',
        'started_at' => 'datetime',
        'completed_at' => 'datetime',
        'cancelled_at' => 'datetime',
    ];

    protected static function boot()
    {
        parent::boot();
        static::creating(function ($model) {
            $model->booking_code = 'BG-' . strtoupper(Str::random(8));
        });
    }

    // Relationships
    public function customer()
    {
        return $this->belongsTo(User::class, 'customer_id');
    }

    public function barber()
    {
        return $this->belongsTo(BarberProfile::class, 'barber_id');
    }

    public function services()
    {
        return $this->hasMany(BookingService::class);
    }

    public function payment()
    {
        return $this->hasOne(Payment::class);
    }

    public function review()
    {
        return $this->hasOne(Review::class);
    }

    // Scopes
    public function scopePending($query)
    {
        return $query->where('status', 'pending');
    }

    public function scopeActive($query)
    {
        return $query->whereIn('status', ['pending', 'confirmed', 'on_the_way', 'arrived', 'in_progress']);
    }

    public function scopeCompleted($query)
    {
        return $query->where('status', 'completed');
    }

    // Status Helpers
    public function isPending(): bool { return $this->status === 'pending'; }
    public function isConfirmed(): bool { return $this->status === 'confirmed'; }
    public function isOnTheWay(): bool { return $this->status === 'on_the_way'; }
    public function isArrived(): bool { return $this->status === 'arrived'; }
    public function isInProgress(): bool { return $this->status === 'in_progress'; }
    public function isCompleted(): bool { return $this->status === 'completed'; }
    public function isCancelled(): bool { return $this->status === 'cancelled'; }

    public function canBeCancelled(): bool
    {
        return in_array($this->status, ['pending', 'confirmed']);
    }

    // Formatted
    public function getFormattedTotalAttribute(): string
    {
        return 'Rp ' . number_format($this->total_price, 0, ',', '.');
    }

    public function getStatusLabelAttribute(): string
    {
        $labels = [
            'pending' => 'Menunggu Konfirmasi',
            'confirmed' => 'Dikonfirmasi',
            'on_the_way' => 'Dalam Perjalanan',
            'arrived' => 'Sudah Sampai',
            'in_progress' => 'Sedang Dikerjakan',
            'completed' => 'Selesai',
            'cancelled' => 'Dibatalkan',
        ];
        return $labels[$this->status] ?? $this->status;
    }

    public function getStatusColorAttribute(): string
    {
        $colors = [
            'pending' => 'yellow',
            'confirmed' => 'blue',
            'on_the_way' => 'indigo',
            'arrived' => 'purple',
            'in_progress' => 'orange',
            'completed' => 'green',
            'cancelled' => 'red',
        ];
        return $colors[$this->status] ?? 'gray';
    }
}