<?php

namespace Database\Seeders;

use App\Models\Reservation;
use App\Models\User;
use App\Models\Venue;
use Illuminate\Database\Seeder;

class ReservationSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        $users = User::all();
        $venues = Venue::all();

        if ($users->isEmpty() || $venues->isEmpty()) {
            return;
        }

        $sampleReservations = [
            [
                'event_title' => 'Annual Department Meeting',
                'capacity' => 120,
                'purpose' => 'Annual review and planning session for the Computer Science Department',
                'start_date' => now()->addDays(5)->setTime(9, 0),
                'end_date' => now()->addDays(5)->setTime(12, 0),
                'status' => 'pending',
            ],
            [
                'event_title' => 'Student Council Elections',
                'capacity' => 500,
                'purpose' => 'Annual student council election process',
                'start_date' => now()->addDays(10)->setTime(8, 0),
                'end_date' => now()->addDays(10)->setTime(17, 0),
                'status' => 'approved_IOSA',
            ],
            [
                'event_title' => 'Faculty Workshop',
                'capacity' => 30,
                'purpose' => 'Professional development workshop for faculty members',
                'start_date' => now()->addDays(3)->setTime(13, 0),
                'end_date' => now()->addDays(3)->setTime(16, 0),
                'status' => 'rejected_IOSA',
            ],
            [
                'event_title' => 'Research Symposium',
                'capacity' => 200,
                'purpose' => 'Annual research presentation and networking event',
                'start_date' => now()->addDays(15)->setTime(10, 0),
                'end_date' => now()->addDays(15)->setTime(16, 0),
                'status' => 'approved_mhadel',
            ],
            [
                'event_title' => 'Alumni Homecoming',
                'capacity' => 300,
                'purpose' => 'Annual alumni gathering and networking event',
                'start_date' => now()->addDays(20)->setTime(18, 0),
                'end_date' => now()->addDays(20)->setTime(22, 0),
                'status' => 'approved_OTP',
            ],
            [
                'event_title' => 'Sports Tournament',
                'capacity' => 150,
                'purpose' => 'Inter-department sports competition',
                'start_date' => now()->addDays(25)->setTime(14, 0),
                'end_date' => now()->addDays(25)->setTime(18, 0),
                'status' => 'rejected_mhadel',
            ],
            [
                'event_title' => 'Academic Conference',
                'capacity' => 400,
                'purpose' => 'International academic conference on technology',
                'start_date' => now()->addDays(30)->setTime(9, 0),
                'end_date' => now()->addDays(30)->setTime(17, 0),
                'status' => 'rejected_OTP',
            ],
        ];

        foreach ($sampleReservations as $reservationData) {
            Reservation::create([
                'user_id' => $users->random()->id,
                'venue_id' => $venues->random()->id,
                'event_title' => $reservationData['event_title'],
                'capacity' => $reservationData['capacity'],
                'purpose' => $reservationData['purpose'],
                'start_date' => $reservationData['start_date'],
                'end_date' => $reservationData['end_date'],
                'status' => $reservationData['status'],
                'activity_grid' => 'Sample activity grid data',
                'notes' => 'Sample notes for the reservation',
            ]);
        }
    }
} 