<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;

class InfoSeeder extends Seeder
{
    public function run()
    {
        DB::table('info')->insert([
            [
                'title' => 'Style Tips for Spring 2025',
                'description' => 'Spring 2025 fashion is all about effortless elegance, playful colors, and sustainable choices.
                Whether you are refreshing your wardrobe or looking for styling inspiration,
                these trends will help you stay ahead.',
                'img' => 'image1.jpg'
            ],
            [
                'title' => '36 Retro Sneakers to Shop—From Old-School Classics to Modern Designer Pairs',
                'description' => 'There’s no denying that retro sneakers are a bonafide thing. Each year, a rebirth of some classic sneaker style gains mass traction, making it the It shoe of the year. These sneakers are often catapulted to stardom by the stars themselves—think A-listers, supermodels, and street style regulars whose adoption en masse turn them into a closet staple. But more often than not, trends often start on the runway, and contemporary designers have pushed the retro sneaker agenda forward the past few seasons. Their contemporary interpretations have caused a frenzy of waitlists, further proving that when it comes to your sneaker game, all things vintage-inspired is still definitely en mode.',
                'img' => 'image2.jpg'
            ]
        ]);
    }
}
