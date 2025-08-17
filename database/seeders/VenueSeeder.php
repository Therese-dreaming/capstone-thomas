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
                'price_per_hour' => 1500.00,
                'available_equipment' => [
                    ['name' => 'Projector', 'quantity' => 2, 'category' => 'Visual'],
                    ['name' => 'Wireless Microphones', 'quantity' => 4, 'category' => 'Audio'],
                    ['name' => 'Sound System', 'quantity' => 1, 'category' => 'Audio'],
                    ['name' => 'Conference Tables', 'quantity' => 20, 'category' => 'Furniture'],
                    ['name' => 'Chairs', 'quantity' => 200, 'category' => 'Furniture'],
                ],
            ],
            [
                'name' => 'Executive Meeting Room',
                'capacity' => 25,
                'status' => 'active',
                'is_available' => true,
                'description' => 'An elegant meeting room designed for executive meetings and small group discussions. Equipped with a smart board, video conferencing capabilities, and premium furniture.',
                'price_per_hour' => 800.00,
                'available_equipment' => [
                    ['name' => 'Smart Board', 'quantity' => 1, 'category' => 'Technology'],
                    ['name' => 'Video Conferencing System', 'quantity' => 1, 'category' => 'Technology'],
                    ['name' => 'Conference Table', 'quantity' => 1, 'category' => 'Furniture'],
                    ['name' => 'Executive Chairs', 'quantity' => 25, 'category' => 'Furniture'],
                ],
            ],
            [
                'name' => 'Training Center A',
                'capacity' => 50,
                'status' => 'active',
                'is_available' => false,
                'description' => 'A versatile training room suitable for workshops, training sessions, and educational programs. Features movable tables, whiteboards, and excellent lighting.',
                'price_per_hour' => 600.00,
                'available_equipment' => [
                    ['name' => 'Whiteboards', 'quantity' => 3, 'category' => 'Visual'],
                    ['name' => 'Movable Tables', 'quantity' => 10, 'category' => 'Furniture'],
                    ['name' => 'Training Chairs', 'quantity' => 50, 'category' => 'Furniture'],
                    ['name' => 'Projector', 'quantity' => 1, 'category' => 'Visual'],
                ],
            ],
            [
                'name' => 'Auditorium',
                'capacity' => 500,
                'status' => 'active',
                'is_available' => true,
                'description' => 'A large auditorium with tiered seating, professional stage lighting, and high-quality sound system. Ideal for large presentations, ceremonies, and events.',
                'price_per_hour' => 2500.00,
                'available_equipment' => [
                    ['name' => 'Stage Lighting System', 'quantity' => 1, 'category' => 'Visual'],
                    ['name' => 'Professional Sound System', 'quantity' => 1, 'category' => 'Audio'],
                    ['name' => 'Wireless Microphones', 'quantity' => 8, 'category' => 'Audio'],
                    ['name' => 'Projector Screen', 'quantity' => 2, 'category' => 'Visual'],
                    ['name' => 'Auditorium Seats', 'quantity' => 500, 'category' => 'Furniture'],
                ],
            ],
            [
                'name' => 'Boardroom',
                'capacity' => 12,
                'status' => 'active',
                'is_available' => true,
                'description' => 'An intimate boardroom with a large mahogany table, leather chairs, and wall-mounted displays. Perfect for board meetings and confidential discussions.',
                'price_per_hour' => 500.00,
                'available_equipment' => [
                    ['name' => 'Wall-Mounted Display', 'quantity' => 1, 'category' => 'Visual'],
                    ['name' => 'Boardroom Table', 'quantity' => 1, 'category' => 'Furniture'],
                    ['name' => 'Leather Chairs', 'quantity' => 12, 'category' => 'Furniture'],
                    ['name' => 'Video Conferencing', 'quantity' => 1, 'category' => 'Technology'],
                ],
            ],
            [
                'name' => 'Multi-Purpose Hall',
                'capacity' => 150,
                'status' => 'active',
                'is_available' => true,
                'description' => 'A flexible space that can be configured for various events including workshops, exhibitions, social gatherings, and small conferences.',
                'price_per_hour' => 1000.00,
                'available_equipment' => [
                    ['name' => 'Portable Sound System', 'quantity' => 1, 'category' => 'Audio'],
                    ['name' => 'Portable Projector', 'quantity' => 1, 'category' => 'Visual'],
                    ['name' => 'Folding Tables', 'quantity' => 15, 'category' => 'Furniture'],
                    ['name' => 'Folding Chairs', 'quantity' => 150, 'category' => 'Furniture'],
                    ['name' => 'Display Panels', 'quantity' => 10, 'category' => 'Visual'],
                ],
            ],
            [
                'name' => 'Innovation Lab',
                'capacity' => 30,
                'status' => 'inactive',
                'is_available' => false,
                'description' => 'A collaborative workspace designed for brainstorming sessions and innovation workshops. Currently under renovation with new technology installations.',
                'price_per_hour' => 700.00,
                'available_equipment' => [
                    ['name' => 'Interactive Whiteboards', 'quantity' => 2, 'category' => 'Technology'],
                    ['name' => 'Collaborative Tables', 'quantity' => 5, 'category' => 'Furniture'],
                    ['name' => 'Innovation Chairs', 'quantity' => 30, 'category' => 'Furniture'],
                ],
            ],
            [
                'name' => 'Outdoor Pavilion',
                'capacity' => 100,
                'status' => 'active',
                'is_available' => true,
                'description' => 'A covered outdoor venue perfect for casual meetings, team building activities, and networking events. Features natural lighting and garden views.',
                'price_per_hour' => 400.00,
                'available_equipment' => [
                    ['name' => 'Portable Sound System', 'quantity' => 1, 'category' => 'Audio'],
                    ['name' => 'Outdoor Tables', 'quantity' => 10, 'category' => 'Furniture'],
                    ['name' => 'Outdoor Chairs', 'quantity' => 100, 'category' => 'Furniture'],
                    ['name' => 'Canopy', 'quantity' => 1, 'category' => 'Furniture'],
                ],
            ],
            [
                'name' => 'Small Conference Room B',
                'capacity' => 15,
                'status' => 'active',
                'is_available' => false,
                'description' => 'A cozy conference room suitable for small team meetings and one-on-one discussions. Equipped with basic presentation tools and comfortable seating.',
                'price_per_hour' => 300.00,
                'available_equipment' => [
                    ['name' => 'TV Display', 'quantity' => 1, 'category' => 'Visual'],
                    ['name' => 'Conference Table', 'quantity' => 1, 'category' => 'Furniture'],
                    ['name' => 'Office Chairs', 'quantity' => 15, 'category' => 'Furniture'],
                ],
            ],
            [
                'name' => 'Library Meeting Space',
                'capacity' => 8,
                'status' => 'active',
                'is_available' => true,
                'description' => 'A quiet meeting space within the library area, ideal for study groups, small meetings, and focused discussions. Features natural lighting and bookshelf ambiance.',
                'price_per_hour' => 200.00,
                'available_equipment' => [
                    ['name' => 'Study Table', 'quantity' => 1, 'category' => 'Furniture'],
                    ['name' => 'Study Chairs', 'quantity' => 8, 'category' => 'Furniture'],
                    ['name' => 'Whiteboard', 'quantity' => 1, 'category' => 'Visual'],
                ],
            ],
        ];

        foreach ($venues as $venue) {
            Venue::create($venue);
        }
    }
}
