<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class BarberProfile extends Model
{
    use HasFactory;

    protected $fillable = [
        'user_id', 'bio', 'experience_years', 'speciality',
        'rating_avg', 'total_reviews', 'total_completed_bookings',
        'is_available', 'latitude', 'longitude', 'current_address',
        'service_radius_km', 'commission_rate', 'status',
    ];

    protected $casts = [
        'rating_avg' => 'decimal:2',
        'is_available' => 'boolean',
        'latitude' => 'decimal:8',
        'longitude' => 'decimal:8',
        'commission_rate' => 'decimal:2',
    ];

    public function user()
    {
        return $this->belongsTo(User::class);
    }

    public function schedules()
    {
        return $this->hasMany(BarberSchedule::class, 'barber_id');
    }

    public function portfolios()
    {
        return $this->hasMany(BarberPortfolio::class, 'barber_id');
    }

    public function bookings()
    {
        return $this->hasMany(Booking::class, 'barber_id');
    }

    public function reviews()
    {
        return $this->hasMany(Review::class, 'barber_id');
    }

    // Scope
    public function scopeActive($query)
    {
        return $query->where('status', 'active');
    }

    public function scopeAvailable($query)
    {
        return $query->where('is_available', true)->where('status', 'active');
    }

    // Calculate distance
    public function distanceTo($latitude, $longitude): float
    {
        if (!$this->latitude || !$this->longitude) return 0;

        $earthRadius = 6371; // km
        $latDiff = deg2rad($latitude - $this->latitude);
        $lonDiff = deg2rad($longitude - $this->longitude);

        $a = sin($latDiff / 2) * sin($latDiff / 2) +
            cos(deg2rad($this->latitude)) * cos(deg2rad($latitude)) *
            sin($lonDiff / 2) * sin($lonDiff / 2);
        $c = 2 * atan2(sqrt($a), sqrt(1 - $a));

        return round($earthRadius * $c, 2);
    }

    public function updateRating(): void
    {
        $this->rating_avg = $this->reviews()->avg('rating') ?? 0;
        $this->total_reviews = $this->reviews()->count();
        $this->save();
    }
}