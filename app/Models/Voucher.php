<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Voucher extends Model
{
    use HasFactory;

    protected $fillable = [
        'code', 'name', 'description', 'type', 'value',
        'min_order', 'max_discount', 'usage_limit',
        'used_count', 'per_user_limit', 'valid_from', 'valid_until', 'is_active',
    ];

    protected $casts = [
        'value' => 'decimal:2',
        'min_order' => 'decimal:2',
        'max_discount' => 'decimal:2',
        'valid_from' => 'datetime',
        'valid_until' => 'datetime',
        'is_active' => 'boolean',
    ];

    public function isValid(): bool
    {
        return $this->is_active &&
            now()->between($this->valid_from, $this->valid_until) &&
            ($this->usage_limit === null || $this->used_count < $this->usage_limit);
    }

    public function calculateDiscount(float $subtotal): float
    {
        if ($subtotal < $this->min_order) return 0;

        $discount = $this->type === 'percentage'
            ? $subtotal * ($this->value / 100)
            : $this->value;

        if ($this->max_discount) {
            $discount = min($discount, $this->max_discount);
        }

        return round($discount, 2);
    }

    public function scopeActive($query)
    {
        return $query->where('is_active', true)
            ->where('valid_from', '<=', now())
            ->where('valid_until', '>=', now());
    }
}