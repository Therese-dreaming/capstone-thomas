<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class Event extends Model
{
    protected $fillable = [
        'title',
        'description',
        'start_date',
        'end_date',
        'venue_id',
        'organizer',
        'department',
        'status',
        'max_participants',
        'completion_notes',
        'completion_date',
        'completed_by',
    ];

    protected $casts = [
        'start_date' => 'datetime',
        'end_date' => 'datetime',
        'max_participants' => 'integer',
        'completion_date' => 'datetime',
    ];

    public function venue(): BelongsTo
    {
        return $this->belongsTo(Venue::class);
    }
}
