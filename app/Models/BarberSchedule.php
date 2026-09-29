<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class BarberSchedule extends Model
{
    use HasFactory;

    protected $fillable = [
        'barber_id', 'day_of_week', 'start_time', 'end_time', 'is_available',
    ];

    protected $casts = [
        'is_available' => 'boolean',
    ];

    public const DAYS = [
        0 => 'Minggu',
        1 => 'Senin',
        2 => 'Selasa',
        3 => 'Rabu',
        4 => 'Kamis',
        5 => 'Jumat',
        6 => 'Sabtu',
    ];

    public function barber()
    {
        return $this->belongsTo(BarberProfile::class, 'barber_id');
    }

    public function getDayNameAttribute(): string
    {
        return self::DAYS[$this->day_of_week] ?? '';
    }
}