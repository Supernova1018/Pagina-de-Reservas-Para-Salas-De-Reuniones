<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class ReservationFeedback extends Model
{
    use HasFactory;

    protected $fillable = [
        'reservation_id',
        'service_satisfactory',
        'space_met_expectations',
        'overall_score',
        'comment',
    ];

    protected $casts = [
        'service_satisfactory' => 'boolean',
        'space_met_expectations' => 'boolean',
        'overall_score' => 'integer',
    ];

    public function reservation()
    {
        return $this->belongsTo(Reservation::class);
    }
}