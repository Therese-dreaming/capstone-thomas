<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;
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

    public function ratings(): HasMany
    {
        return $this->hasMany(ReservationRating::class);
    }

    /**
     * Get the average rating for this reservation
     */
    public function getAverageRatingAttribute()
    {
        return $this->ratings()->avg('rating') ?? 0;
    }

    /**
     * Get the total number of ratings for this reservation
     */
    public function getTotalRatingsAttribute()
    {
        return $this->ratings()->count();
    }

    /**
     * Check if a user has rated this reservation
     */
    public function hasUserRated($userId)
    {
        return $this->ratings()->where('user_id', $userId)->exists();
    }

    /**
     * Get user's rating for this reservation
     */
    public function getUserRating($userId)
    {
        return $this->ratings()->where('user_id', $userId)->first();
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
