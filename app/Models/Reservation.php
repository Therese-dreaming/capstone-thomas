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
        'status',
        'notes'
    ];

    protected $casts = [
        'start_date' => 'datetime',
        'end_date' => 'datetime',
        'activity_grid' => 'string',
    ];

    public function user(): BelongsTo
    {
        return $this->belongsTo(User::class);
    }

    public function venue(): BelongsTo
    {
        return $this->belongsTo(Venue::class);
    }

    public function equipment()
    {
        return $this->belongsToMany(Equipment::class, 'reservation_equipment')
            ->withPivot('quantity');
    }
}
