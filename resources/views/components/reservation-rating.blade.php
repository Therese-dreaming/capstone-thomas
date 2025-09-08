@props([
    'reservation' => null,
    'rating' => 0,
    'interactive' => false,
    'size' => 'md', // sm, md, lg
    'showAverage' => true,
    'showCount' => true
])

@php
    $sizeClasses = [
        'sm' => 'text-sm',
        'md' => 'text-base',
        'lg' => 'text-lg'
    ];
    $starSizeClasses = [
        'sm' => 'w-4 h-4',
        'md' => 'w-5 h-5',
        'lg' => 'w-6 h-6'
    ];
    $sizeClass = $sizeClasses[$size] ?? $sizeClasses['md'];
    $starSizeClass = $starSizeClasses[$size] ?? $starSizeClasses['md'];
@endphp

<div class="reservation-rating-component {{ $sizeClass }}" data-reservation-id="{{ $reservation?->id }}">
    @if($showAverage && $reservation)
        <div class="flex items-center space-x-2 mb-2">
            <div class="flex items-center space-x-1">
                @for($i = 1; $i <= 5; $i++)
                    <i class="fas fa-star {{ $starSizeClass }} {{ $i <= $reservation->average_rating ? 'text-yellow-400' : 'text-gray-300' }}"></i>
                @endfor
            </div>
            <span class="text-gray-600 font-medium">{{ number_format($reservation->average_rating, 1) }}</span>
            @if($showCount)
                <span class="text-gray-500 text-sm">({{ $reservation->total_ratings }} {{ Str::plural('rating', $reservation->total_ratings) }})</span>
            @endif
        </div>
    @endif

    @if($interactive && $reservation && $reservation->status === 'completed')
        <div class="rating-form">
            <div class="flex items-center space-x-1 mb-2">
                <span class="text-sm font-medium text-gray-700">Your Rating:</span>
                <div class="flex items-center space-x-1" id="star-rating-{{ $reservation->id }}">
                    @for($i = 1; $i <= 5; $i++)
                        <i class="fas fa-star {{ $starSizeClass }} text-gray-300 cursor-pointer hover:text-yellow-400 transition-colors duration-200 star-rating-star" 
                           data-rating="{{ $i }}" 
                           data-reservation-id="{{ $reservation->id }}"></i>
                    @endfor
                </div>
            </div>
            
            <form id="rating-form-{{ $reservation->id }}" class="hidden">
                @csrf
                <input type="hidden" name="rating" id="rating-input-{{ $reservation->id }}" value="0">
                <div class="mb-3">
                    <label for="comment-{{ $reservation->id }}" class="block text-sm font-medium text-gray-700 mb-1">Comment (Optional)</label>
                    <textarea name="comment" 
                              id="comment-{{ $reservation->id }}" 
                              rows="3" 
                              class="w-full px-3 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-blue-500 focus:border-blue-500 transition-colors"
                              placeholder="Share your thoughts about this reservation..."></textarea>
                </div>
                <div class="flex space-x-2">
                    <button type="submit" 
                            class="px-4 py-2 bg-blue-600 text-white rounded-lg hover:bg-blue-700 transition-colors text-sm font-medium">
                        <i class="fas fa-star mr-1"></i>Submit Rating
                    </button>
                    <button type="button" 
                            onclick="cancelRating({{ $reservation->id }})"
                            class="px-4 py-2 bg-gray-500 text-white rounded-lg hover:bg-gray-600 transition-colors text-sm font-medium">
                        Cancel
                    </button>
                </div>
            </form>
            
            <button type="button" 
                    onclick="showRatingForm({{ $reservation->id }})"
                    class="px-3 py-1 bg-blue-600 text-white rounded-lg hover:bg-blue-700 transition-colors text-sm font-medium">
                <i class="fas fa-star mr-1"></i>Rate Reservation
            </button>
        </div>
    @endif
</div>

@push('scripts')
<script>
document.addEventListener('DOMContentLoaded', function() {
    // Load existing ratings for all reservations
    document.querySelectorAll('.reservation-rating-component[data-reservation-id]').forEach(function(component) {
        const reservationId = component.dataset.reservationId;
        loadReservationRating(reservationId);
    });
});

function loadReservationRating(reservationId) {
    fetch(`/user/reservations/${reservationId}/rating`, {
        method: 'GET',
        headers: {
            'X-Requested-With': 'XMLHttpRequest',
            'Accept': 'application/json',
        }
    })
    .then(response => response.json())
    .then(data => {
        if (data.user_rating) {
            // Highlight user's rating
            const stars = document.querySelectorAll(`#star-rating-${reservationId} .star-rating-star`);
            stars.forEach((star, index) => {
                if (index < data.user_rating) {
                    star.classList.remove('text-gray-300');
                    star.classList.add('text-yellow-400');
                } else {
                    star.classList.remove('text-yellow-400');
                    star.classList.add('text-gray-300');
                }
            });
            
            // Fill in comment if exists
            const commentField = document.getElementById(`comment-${reservationId}`);
            if (commentField && data.user_comment) {
                commentField.value = data.user_comment;
            }
        }
    })
    .catch(error => console.error('Error loading rating:', error));
}

function showRatingForm(reservationId) {
    const form = document.getElementById(`rating-form-${reservationId}`);
    const button = event.target;
    
    form.classList.remove('hidden');
    button.classList.add('hidden');
}

function cancelRating(reservationId) {
    const form = document.getElementById(`rating-form-${reservationId}`);
    const button = form.previousElementSibling;
    
    form.classList.add('hidden');
    button.classList.remove('hidden');
    
    // Reset form
    document.getElementById(`rating-input-${reservationId}`).value = '0';
    document.getElementById(`comment-${reservationId}`).value = '';
    
    // Reset stars
    const stars = document.querySelectorAll(`#star-rating-${reservationId} .star-rating-star`);
    stars.forEach(star => {
        star.classList.remove('text-yellow-400');
        star.classList.add('text-gray-300');
    });
}

// Star rating interaction
document.addEventListener('click', function(e) {
    if (e.target.classList.contains('star-rating-star')) {
        const rating = parseInt(e.target.dataset.rating);
        const reservationId = e.target.dataset.reservationId;
        
        // Update visual stars
        const stars = document.querySelectorAll(`#star-rating-${reservationId} .star-rating-star`);
        stars.forEach((star, index) => {
            if (index < rating) {
                star.classList.remove('text-gray-300');
                star.classList.add('text-yellow-400');
            } else {
                star.classList.remove('text-yellow-400');
                star.classList.add('text-gray-300');
            }
        });
        
        // Update hidden input
        document.getElementById(`rating-input-${reservationId}`).value = rating;
    }
});

// Form submission
document.addEventListener('submit', function(e) {
    if (e.target.id && e.target.id.startsWith('rating-form-')) {
        e.preventDefault();
        
        const reservationId = e.target.id.replace('rating-form-', '');
        const formData = new FormData(e.target);
        
        fetch(`/user/reservations/${reservationId}/rate`, {
            method: 'POST',
            body: formData,
            headers: {
                'X-Requested-With': 'XMLHttpRequest',
                'Accept': 'application/json',
            }
        })
        .then(response => response.json())
        .then(data => {
            if (data.success) {
                // Show success message
                showNotification(data.message, 'success');
                
                // Update average rating display
                const component = document.querySelector(`.reservation-rating-component[data-reservation-id="${reservationId}"]`);
                if (component) {
                    // Reload the component or update the display
                    location.reload(); // Simple reload for now
                }
                
                // Hide form
                cancelRating(reservationId);
            } else {
                showNotification(data.message, 'error');
            }
        })
        .catch(error => {
            console.error('Error submitting rating:', error);
            showNotification('An error occurred while submitting your rating.', 'error');
        });
    }
});

function showNotification(message, type) {
    // Create notification element
    const notification = document.createElement('div');
    notification.className = `fixed top-4 right-4 z-50 px-6 py-3 rounded-lg shadow-lg text-white font-medium ${
        type === 'success' ? 'bg-green-500' : 'bg-red-500'
    }`;
    notification.textContent = message;
    
    document.body.appendChild(notification);
    
    // Remove after 3 seconds
    setTimeout(() => {
        notification.remove();
    }, 3000);
}
</script>
@endpush
