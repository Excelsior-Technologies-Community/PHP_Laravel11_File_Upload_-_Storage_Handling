<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use App\Models\Tag;

class TagSeeder extends Seeder
{
    public function run(): void
    {
        $tags = [
            'Electronics',
            'Footwear',
            'Accessories',
            'Home',
            'Sports',
            'Stationery',
            'Clothing',
            'Books',
        ];

        foreach ($tags as $tag) {
            Tag::create(['tag_name' => $tag]);
        }
    }
}