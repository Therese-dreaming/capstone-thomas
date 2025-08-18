<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class Reservation extends Model
{
    protected $fillable = [
        'user_id',
        'event_title',
        'capacity',
        'venue_id',
        'purpose',
        'start_date',
        'end_date',
        'activity_grid',
        'equipment_details',
        'price_per_hour',
        'base_price',
        'discount_percentage',
        'final_price',
        'duration_hours',
        'status',
        'notes'
    ];

    protected $casts = [
        'start_date' => 'datetime',
        'end_date' => 'datetime',
        'activity_grid' => 'string',
        'equipment_details' => 'array',
        'price_per_hour' => 'decimal:2',
        'base_price' => 'decimal:2',
        'discount_percentage' => 'integer',
        'final_price' => 'decimal:2',
        'duration_hours' => 'integer',
    ];

    public function user(): BelongsTo
    {
        return $this->belongsTo(User::class);
    }

    public function venue(): BelongsTo
    {
        return $this->belongsTo(Venue::class);
    }
}
