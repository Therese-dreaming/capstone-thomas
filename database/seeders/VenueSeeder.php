<?php

namespace Database\Seeders;

use App\Models\Venue;
use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;

class VenueSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        $venues = [
            [
                'name' => 'Main Conference Hall',
                'capacity' => 200,
                'status' => 'active',
                'is_available' => true,
                'description' => 'A spacious conference hall equipped with modern audio-visual systems, perfect for large meetings, seminars, and presentations. Features include projector screens, wireless microphones, and comfortable seating arrangements.',
            ],
            [
                'name' => 'Executive Meeting Room',
                'capacity' => 25,
                'status' => 'active',
                'is_available' => true,
                'description' => 'An elegant meeting room designed for executive meetings and small group discussions. Equipped with a smart board, video conferencing capabilities, and premium furniture.',
            ],
            [
                'name' => 'Training Center A',
                'capacity' => 50,
                'status' => 'active',
                'is_available' => false,
                'description' => 'A versatile training room suitable for workshops, training sessions, and educational programs. Features movable tables, whiteboards, and excellent lighting.',
            ],
            [
                'name' => 'Auditorium',
                'capacity' => 500,
                'status' => 'active',
                'is_available' => true,
                'description' => 'A large auditorium with tiered seating, professional stage lighting, and high-quality sound system. Ideal for large presentations, ceremonies, and events.',
            ],
            [
                'name' => 'Boardroom',
                'capacity' => 12,
                'status' => 'active',
                'is_available' => true,
                'description' => 'An intimate boardroom with a large mahogany table, leather chairs, and wall-mounted displays. Perfect for board meetings and confidential discussions.',
            ],
            [
                'name' => 'Multi-Purpose Hall',
                'capacity' => 150,
                'status' => 'active',
                'is_available' => true,
                'description' => 'A flexible space that can be configured for various events including workshops, exhibitions, social gatherings, and small conferences.',
            ],
            [
                'name' => 'Innovation Lab',
                'capacity' => 30,
                'status' => 'inactive',
                'is_available' => false,
                'description' => 'A collaborative workspace designed for brainstorming sessions and innovation workshops. Currently under renovation with new technology installations.',
            ],
            [
                'name' => 'Outdoor Pavilion',
                'capacity' => 100,
                'status' => 'active',
                'is_available' => true,
                'description' => 'A covered outdoor venue perfect for casual meetings, team building activities, and networking events. Features natural lighting and garden views.',
            ],
            [
                'name' => 'Small Conference Room B',
                'capacity' => 15,
                'status' => 'active',
                'is_available' => false,
                'description' => 'A cozy conference room suitable for small team meetings and one-on-one discussions. Equipped with basic presentation tools and comfortable seating.',
            ],
            [
                'name' => 'Library Meeting Space',
                'capacity' => 8,
                'status' => 'active',
                'is_available' => true,
                'description' => 'A quiet meeting space within the library area, ideal for study groups, small meetings, and focused discussions. Features natural lighting and bookshelf ambiance.',
            ],
        ];

        foreach ($venues as $venue) {
            Venue::create($venue);
        }
    }
}
