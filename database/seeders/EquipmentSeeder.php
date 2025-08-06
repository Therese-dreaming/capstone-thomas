<?php

namespace Database\Seeders;

use App\Models\Equipment;
use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;

class EquipmentSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        $equipment = [
            [
                'name' => 'Wireless Microphone Set',
                'category' => 'Audio',
                'total_quantity' => 12,
            ],
            [
                'name' => 'Projector - Full HD',
                'category' => 'Visual',
                'total_quantity' => 8,
            ],
            [
                'name' => 'Portable Speaker System',
                'category' => 'Audio',
                'total_quantity' => 6,
            ],
            [
                'name' => 'LED Stage Lighting Kit',
                'category' => 'Lighting',
                'total_quantity' => 4,
            ],
            [
                'name' => 'Folding Chairs',
                'category' => 'Furniture',
                'total_quantity' => 200,
            ],
            [
                'name' => 'Round Tables (8-seater)',
                'category' => 'Furniture',
                'total_quantity' => 25,
            ],
            [
                'name' => 'Laptop Computer',
                'category' => 'Technology',
                'total_quantity' => 5,
            ],
            [
                'name' => 'Extension Cords (50ft)',
                'category' => 'Other',
                'total_quantity' => 15,
            ],
            [
                'name' => 'Whiteboard with Stand',
                'category' => 'Visual',
                'total_quantity' => 10,
            ],
            [
                'name' => 'Flip Chart with Markers',
                'category' => 'Visual',
                'total_quantity' => 8,
            ],
            [
                'name' => 'Fire Extinguisher',
                'category' => 'Safety',
                'total_quantity' => 20,
            ],
            [
                'name' => 'First Aid Kit',
                'category' => 'Safety',
                'total_quantity' => 12,
            ],
            [
                'name' => 'Spotlight - LED',
                'category' => 'Lighting',
                'total_quantity' => 6,
            ],
            [
                'name' => 'Podium with Microphone',
                'category' => 'Furniture',
                'total_quantity' => 3,
            ],
            [
                'name' => 'Video Camera - HD',
                'category' => 'Technology',
                'total_quantity' => 2,
            ],
        ];

        foreach ($equipment as $item) {
            Equipment::create($item);
        }
    }
}
