<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\HasMany;

class Voucher extends Model
{
    protected $fillable = [
        'code',
        'type',
        'value',
        'quota',
        'used_count',
        'valid_from',
        'valid_until',
        'is_active',
    ];

    protected function casts(): array
    {
        return [
            'value' => 'decimal:2',
            'valid_from' => 'date',
            'valid_until' => 'date',
            'is_active' => 'boolean',
            'quota' => 'integer',
            'used_count' => 'integer',
        ];
    }

    public function payments(): HasMany
    {
        return $this->hasMany(Payment::class);
    }

    public function isValid(): bool
    {
        if (! $this->is_active) {
            return false;
        }

        $today = today();
        if ($this->valid_from->gt($today) || $this->valid_until->lt($today)) {
            return false;
        }

        if ($this->used_count >= $this->quota) {
            return false;
        }

        return true;
    }

    public function calculateDiscount(float $amount): float
    {
        if (! $this->isValid()) {
            return 0.0;
        }

        if ($this->type === 'percent') {
            $discount = ($this->value / 100) * $amount;
        } else {
            $discount = (float) $this->value;
        }

        return min($discount, $amount);
    }
}
