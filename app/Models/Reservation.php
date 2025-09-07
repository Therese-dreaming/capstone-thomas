<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Support\Str;

class Reservation extends Model
{
    protected $fillable = [
        'user_id',
        'reservation_id',
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
        'notes',
        'department',
        'completion_notes',
        'completion_date',
        'completed_by',
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
        'completion_date' => 'datetime',
    ];

    public function user(): BelongsTo
    {
        return $this->belongsTo(User::class);
    }

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

        static::creating(function ($reservation) {
            if (empty($reservation->reservation_id)) {
                $reservation->reservation_id = static::generateReservationId();
            }
        });
    }

    /**
     * Generate a unique reservation ID.
     */
    public static function generateReservationId(): string
    {
        do {
            // Format: RES-YYYYMMDD-XXXX (e.g., RES-20240908-1234)
            $date = now()->format('Ymd');
            $random = strtoupper(Str::random(4));
            $reservationId = "RES-{$date}-{$random}";
        } while (static::where('reservation_id', $reservationId)->exists());

        return $reservationId;
    }
}
