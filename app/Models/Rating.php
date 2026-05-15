<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class Rating extends Model
{
    protected $fillable = [
        'reservation_id',
        'user_id',
        'service_satisfaction',
        'room_meets_expectations',
        'comment',
    ];

    protected $casts = [
        'service_satisfaction' => 'integer',
        'room_meets_expectations' => 'integer',
    ];

    public function reservation(): BelongsTo
    {
        return $this->belongsTo(Reservation::class);
    }

    public function user(): BelongsTo
    {
        return $this->belongsTo(User::class);
    }
}
