<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Review extends Model
{
    use HasFactory;

    protected $fillable = [
        'booking_id', 'customer_id', 'barber_id',
        'rating', 'comment', 'barber_reply', 'replied_at',
    ];

    protected $casts = [
        'replied_at' => 'datetime',
    ];

    public function booking()
    {
        return $this->belongsTo(Booking::class);
    }

    public function customer()
    {
        return $this->belongsTo(User::class, 'customer_id');
    }

    public function barber()
    {
        return $this->belongsTo(BarberProfile::class, 'barber_id');
    }

    protected static function booted()
    {
        static::created(function ($review) {
            $review->barber->updateRating();
        });

        static::updated(function ($review) {
            $review->barber->updateRating();
        });
    }
}