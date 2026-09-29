<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class BarberPortfolio extends Model
{
    use HasFactory;

    protected $fillable = ['barber_id', 'image', 'caption', 'sort_order'];

    public function barber()
    {
        return $this->belongsTo(BarberProfile::class, 'barber_id');
    }

    public function getImageUrlAttribute(): string
    {
        return asset('storage/' . $this->image);
    }
}