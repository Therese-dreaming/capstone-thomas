<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Support\Str;

class Event extends Model
{
    protected $fillable = [
        'event_id',
        'title',
        'description',
        'start_date',
        'end_date',
        'venue_id',
        'organizer',
        'department',
        'status',
        'max_participants',
        'equipment_details',
        'completion_notes',
        'completion_date',
        'completed_by',
    ];

    protected $casts = [
        'start_date' => 'datetime',
        'end_date' => 'datetime',
        'max_participants' => 'integer',
        'equipment_details' => 'array',
        'completion_date' => 'datetime',
    ];

    public function venue(): BelongsTo
    {
        return $this->belongsTo(Venue::class);
    }

    /**
     * Boot the model.
     */
    protected static function boot()
    {
        parent::boot();

        static::creating(function ($event) {
            if (empty($event->event_id)) {
                $event->event_id = static::generateEventId();
            }
        });
    }

    /**
     * Generate a unique event ID.
     */
    public static function generateEventId(): string
    {
        do {
            // Format: EVT-YYYYMMDD-XXXX (e.g., EVT-20240908-1234)
            $date = now()->format('Ymd');
            $random = strtoupper(Str::random(4));
            $eventId = "EVT-{$date}-{$random}";
        } while (static::where('event_id', $eventId)->exists());

        return $eventId;
    }
}
