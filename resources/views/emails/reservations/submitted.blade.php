@component('mail::message')
# Reservation Submitted

Hello {{ $user->name }},

Your reservation has been submitted successfully.

- Event: {{ $reservation->event_title }}
- Venue: {{ optional($reservation->venue)->name ?? '—' }}
- Start: {{ optional($reservation->start_date)->format('M d, Y g:i A') }}
- End: {{ optional($reservation->end_date)->format('M d, Y g:i A') }}

We'll notify you as it moves through approvals.

Thanks,
{{ config('app.name') }}
@endcomponent
