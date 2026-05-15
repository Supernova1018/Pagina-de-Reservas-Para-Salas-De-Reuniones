<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Str;

class Space extends Model
{
    use HasFactory;

    protected $fillable = [
        'name',
        'slug',
        'type',
        'capacity',
        'description',
        'rules',
        'price_per_hour',
        'is_active',
        'image',
        'location',
    ];

    protected $casts = [
        'is_active'      => 'boolean',
        'price_per_hour' => 'decimal:2',
        'capacity'       => 'integer',
    ];

    // -------------------------------------------------------------------------
    // Route model binding por slug
    // -------------------------------------------------------------------------
    public function getRouteKeyName(): string
    {
        return 'slug';
    }

    // -------------------------------------------------------------------------
    // Auto-generate slug before creating
    // -------------------------------------------------------------------------
    protected static function booted(): void
    {
        static::creating(function (Space $space) {
            if (empty($space->slug)) {
                $space->slug = Str::slug($space->name);
            }
        });
    }

    // -------------------------------------------------------------------------
    // Relations
    // -------------------------------------------------------------------------
    public function availabilities()
    {
        return $this->hasMany(Availability::class);
    }

    public function reservations()
    {
        return $this->hasMany(Reservation::class);
    }

    public function blockedSlots()
    {
        return $this->hasMany(BlockedSlot::class);
    }

    // -------------------------------------------------------------------------
    // Scopes
    // -------------------------------------------------------------------------
    public function scopeActive($query)
    {
        return $query->where('is_active', true);
    }

    // -------------------------------------------------------------------------
    // Helpers
    // -------------------------------------------------------------------------
    public function getTypeLabel(): string
    {
        return match($this->type) {
            'university' => 'Universitaria',
            'corporate'  => 'Corporativa',
            default      => ucfirst($this->type),
        };
    }
}