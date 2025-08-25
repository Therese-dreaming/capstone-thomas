@component('mail::message')
# Reservation Status Update

Hello {{ $user->name }},

Your reservation has an update from {{ $actorLabel }}.

- Event: {{ $reservation->event_title }}
- Venue: {{ optional($reservation->venue)->name ?? '—' }}
- Start: {{ optional($reservation->start_date)->format('M d, Y g:i A') }}
- End: {{ optional($reservation->end_date)->format('M d, Y g:i A') }}
- New Status: **{{ strtoupper($newStatus) }}**

@isset($extra['reason'])
- Reason: {{ $extra['reason'] }}
@endisset

@isset($extra['pricing'])
## Pricing Details
- Base Price: {{ $extra['pricing']['base_price'] ?? '—' }}
- Discount: {{ $extra['pricing']['discount'] ?? '—' }}
- Final Price: {{ $extra['pricing']['final_price'] ?? '—' }}
@endisset

Thanks,
{{ config('app.name') }}
@endcomponent
