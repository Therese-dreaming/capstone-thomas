<?php

namespace App\Mail;

use Illuminate\Bus\Queueable;
use Illuminate\Mail\Mailable;
use Illuminate\Queue\SerializesModels;
use App\Models\Reservation;
use App\Models\User;

class ReservationStatusChanged extends Mailable
{
    use Queueable, SerializesModels;

    public $reservation;
    public $user;
    public $newStatus;
    public $actorLabel;
    public $extra;

    public function __construct(Reservation $reservation, User $user, string $newStatus, string $actorLabel, array $extra = [])
    {
        $this->reservation = $reservation;
        $this->user = $user;
        $this->newStatus = $newStatus;
        $this->actorLabel = $actorLabel;
        $this->extra = $extra;
    }

    public function build()
    {
        $subject = 'Reservation ' . ucfirst(str_replace('_', ' ', $this->newStatus)) . ': ' . ($this->reservation->event_title ?? '');
        return $this->subject($subject)->markdown('emails.reservations.status_changed');
    }
}
