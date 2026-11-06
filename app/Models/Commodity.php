<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\HasMany;

/**
 * MODEL: a basic good sold in the market (rice, pork, tomato...).
 */
class Commodity extends Model
{
    protected $fillable = ['name', 'category', 'unit', 'is_active'];

    protected function casts(): array
    {
        return ['is_active' => 'boolean'];
    }

    public function prices(): HasMany
    {
        return $this->hasMany(Price::class);
    }

    public function scopeActive(Builder $query): Builder
    {
        return $query->where('is_active', true);
    }
}
