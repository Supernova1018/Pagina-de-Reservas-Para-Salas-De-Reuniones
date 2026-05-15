<?php

namespace App\Models;

use Illuminate\Notifications\Notifiable;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Str;

class Reservation extends Model
{
    use HasFactory;
    use Notifiable;

    protected $fillable = [
        'slug',
        'space_id',
        'user_id',
        'start_time',
        'end_time',
        'status',
        'user_name',
        'user_email',
        'user_phone',
        'organization',
        'notes',
        'total_price',
    ];

    protected $casts = [
        'start_time'  => 'datetime',
        'end_time'    => 'datetime',
        'total_price' => 'decimal:2',
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
        static::creating(function (Reservation $reservation) {
            if (empty($reservation->slug)) {
                $reservation->slug = 'res-' . Str::random(10);
            }

            // Calculate total price
            if ($reservation->space && $reservation->start_time && $reservation->end_time) {
                $hours = $reservation->start_time->diffInMinutes($reservation->end_time) / 60;
                $reservation->total_price = round($hours * $reservation->space->price_per_hour, 2);
            }
        });
    }

    // -------------------------------------------------------------------------
    // Relations
    // -------------------------------------------------------------------------
    public function space()
    {
        return $this->belongsTo(Space::class);
    }

    public function user()
    {
        return $this->belongsTo(User::class);
    }

    public function rating()
    {
        return $this->hasOne(Rating::class);
    }

    // -------------------------------------------------------------------------
    // Scopes
    // -------------------------------------------------------------------------
    public function scopePending($query)
    {
        return $query->where('status', 'pending');
    }

    public function scopeConfirmed($query)
    {
        return $query->where('status', 'confirmed');
    }

    public function scopeByStatus($query, string $status)
    {
        return $query->where('status', $status);
    }

    // -------------------------------------------------------------------------
    // Helpers
    // -------------------------------------------------------------------------
    public function getStatusLabelAttribute(): string
    {
        return match($this->status) {
            'pending'   => 'Pendiente',
            'confirmed' => 'Confirmada',
            'rejected'  => 'Rechazada',
            'cancelled' => 'Cancelada',
            'completed' => 'Finalizada',
            default     => ucfirst($this->status),
        };
    }

    public function getDurationInHoursAttribute(): float
    {
        return $this->start_time->diffInMinutes($this->end_time) / 60;
    }

    public function canTransitionTo(string $newStatus): bool
    {
        $transitions = [
            'pending'   => ['confirmed', 'rejected'],
            'confirmed' => ['cancelled', 'completed'],
            'rejected'  => [],
            'cancelled' => [],
            'completed' => [],
        ];

        return in_array($newStatus, $transitions[$this->status] ?? []);
    }
}