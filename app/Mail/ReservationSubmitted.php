<?php

namespace App\Mail;

use Illuminate\Bus\Queueable;
use Illuminate\Mail\Mailable;
use Illuminate\Queue\SerializesModels;
use App\Models\Reservation;
use App\Models\User;

class ReservationSubmitted extends Mailable
{
    use Queueable, SerializesModels;

    public $reservation;
    public $user;

    public function __construct(Reservation $reservation, User $user)
    {
        $this->reservation = $reservation;
        $this->user = $user;
    }

    public function build()
    {
        return $this->subject('Reservation Submitted: ' . ($this->reservation->event_title ?? ''))
            ->markdown('emails.reservations.submitted');
    }
}
