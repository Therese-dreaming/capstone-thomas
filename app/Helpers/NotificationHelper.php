<?php

namespace App\Helpers;

use App\Models\Notification;
use App\Models\User;

class NotificationHelper
{
    /**
     * Create a notification with proper related data
     */
    public static function create(array $data)
    {
        return Notification::create([
            'user_id' => $data['user_id'],
            'title' => $data['title'],
            'body' => $data['body'] ?? null,
            'type' => $data['type'] ?? 'general',
            'related_id' => $data['related_id'] ?? null,
            'related_type' => $data['related_type'] ?? null,
        ]);
    }

    /**
     * Create reservation-related notification
     */
    public static function createReservationNotification($userId, $title, $body, $reservationId)
    {
        return self::create([
            'user_id' => $userId,
            'title' => $title,
            'body' => $body,
            'type' => 'reservation',
            'related_id' => $reservationId,
            'related_type' => 'App\Models\Reservation',
        ]);
    }

    /**
     * Create event-related notification
     */
    public static function createEventNotification($userId, $title, $body, $eventId)
    {
        return self::create([
            'user_id' => $userId,
            'title' => $title,
            'body' => $body,
            'type' => 'event',
            'related_id' => $eventId,
            'related_type' => 'App\Models\Event',
        ]);
    }

    /**
     * Notify multiple users with reservation data
     */
    public static function notifyUsersAboutReservation($userIds, $title, $body, $reservationId)
    {
        foreach ($userIds as $userId) {
            self::createReservationNotification($userId, $title, $body, $reservationId);
        }
    }

    /**
     * Notify users by role about reservation
     */
    public static function notifyRoleAboutReservation($roles, $title, $body, $reservationId)
    {
        if (!is_array($roles)) {
            $roles = [$roles];
        }

        $users = User::whereIn('role', $roles)->get();
        foreach ($users as $user) {
            self::createReservationNotification($user->id, $title, $body, $reservationId);
        }
    }

    /**
     * Notify users by role about event
     */
    public static function notifyRoleAboutEvent($roles, $title, $body, $eventId)
    {
        if (!is_array($roles)) {
            $roles = [$roles];
        }

        $users = User::whereIn('role', $roles)->get();
        foreach ($users as $user) {
            self::createEventNotification($user->id, $title, $body, $eventId);
        }
    }
}
