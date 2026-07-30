<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use App\Models\Product;
use App\Models\Tag;

class ProductSeeder extends Seeder
{
    public function run(): void
    {
        $tags = Tag::pluck('id')->toArray();

        if (empty($tags)) {
            $tags = [
                Tag::create(['tag_name' => 'Electronics'])->id,
                Tag::create(['tag_name' => 'Footwear'])->id,
                Tag::create(['tag_name' => 'Accessories'])->id,
                Tag::create(['tag_name' => 'Home'])->id,
                Tag::create(['tag_name' => 'Sports'])->id,
            ];
        }

        $products = [
            ['name' => 'Wireless Headphones', 'details' => 'High quality wireless headphones with noise cancellation', 'size' => 'Medium', 'color' => 'Black', 'category' => 'Electronics', 'price' => 2999],
            ['name' => 'Smart Watch', 'details' => 'Fitness tracker with heart rate monitor', 'size' => 'Small', 'color' => 'Silver', 'category' => 'Electronics', 'price' => 4999],
            ['name' => 'Running Shoes', 'details' => 'Lightweight running shoes for men', 'size' => '10', 'color' => 'Red', 'category' => 'Footwear', 'price' => 1999],
            ['name' => 'Backpack', 'details' => 'Waterproof backpack with laptop compartment', 'size' => 'Large', 'color' => 'Blue', 'category' => 'Accessories', 'price' => 1499],
            ['name' => 'Sunglasses', 'details' => 'UV protection polarized sunglasses', 'size' => 'Medium', 'color' => 'Brown', 'category' => 'Accessories', 'price' => 999],
            ['name' => 'Coffee Maker', 'details' => 'Automatic drip coffee maker with timer', 'size' => 'Small', 'color' => 'White', 'category' => 'Home', 'price' => 3499],
            ['name' => 'Yoga Mat', 'details' => 'Non-slip exercise yoga mat 6mm', 'size' => 'Large', 'color' => 'Purple', 'category' => 'Sports', 'price' => 799],
            ['name' => 'Desk Lamp', 'details' => 'LED desk lamp with adjustable brightness', 'size' => 'Medium', 'color' => 'Black', 'category' => 'Home', 'price' => 1299],
            ['name' => 'Water Bottle', 'details' => 'Stainless steel insulated water bottle 1L', 'size' => 'Medium', 'color' => 'Green', 'category' => 'Sports', 'price' => 599],
            ['name' => 'Bluetooth Speaker', 'details' => 'Portable bluetooth speaker with bass boost', 'size' => 'Small', 'color' => 'Blue', 'category' => 'Electronics', 'price' => 1799],
            ['name' => 'Notebook Set', 'details' => 'Set of 3 ruled notebooks 200 pages', 'size' => 'Small', 'color' => 'Yellow', 'category' => 'Stationery', 'price' => 299],
            ['name' => 'Pen Drive', 'details' => '64GB USB 3.0 pen drive', 'size' => 'Small', 'color' => 'Silver', 'category' => 'Electronics', 'price' => 899],
            ['name' => 'Sneakers', 'details' => 'Casual canvas sneakers for women', 'size' => '7', 'color' => 'White', 'category' => 'Footwear', 'price' => 1599],
            ['name' => 'Laptop Stand', 'details' => 'Adjustable aluminum laptop stand', 'size' => 'Medium', 'color' => 'Gray', 'category' => 'Accessories', 'price' => 1099],
            ['name' => 'Phone Case', 'details' => 'Shockproof transparent phone case', 'size' => 'Small', 'color' => 'Clear', 'category' => 'Accessories', 'price' => 399],
            ['name' => 'Gaming Mouse', 'details' => 'RGB gaming mouse with 6 buttons', 'size' => 'Small', 'color' => 'Black', 'category' => 'Electronics', 'price' => 1299],
            ['name' => 'Plant Pot', 'details' => 'Ceramic plant pot with drainage hole', 'size' => 'Medium', 'color' => 'Terracotta', 'category' => 'Home', 'price' => 449],
            ['name' => 'Wrist Watch', 'details' => 'Classic analog wrist watch with leather strap', 'size' => 'Medium', 'color' => 'Brown', 'category' => 'Accessories', 'price' => 2499],
            ['name' => 'Camping Tent', 'details' => '4 person waterproof camping tent', 'size' => 'Large', 'color' => 'Orange', 'category' => 'Sports', 'price' => 3999],
            ['name' => 'Earbuds', 'details' => 'True wireless earbuds with charging case', 'size' => 'Small', 'color' => 'White', 'category' => 'Electronics', 'price' => 2199],
        ];

        foreach ($products as $index => $product) {
            $imagePath = $this->generatePlaceholderImage($product['name'], $index);

            Product::create([
                'name' => $product['name'],
                'details' => $product['details'],
                'images' => $imagePath ? [$imagePath] : [],
                'size' => $product['size'],
                'color' => $product['color'],
                'category' => $product['category'],
                'price' => $product['price'],
                'tag_ids' => [$tags[$index % count($tags)]],
                'status' => 'active',
            ]);
        }
    }

    private function generatePlaceholderImage($productName, $index)
    {
        try {
            $filename = 'product_' . ($index + 1) . '_' . strtolower(str_replace(' ', '_', $productName)) . '.jpg';
            $savePath = public_path('images/' . $filename);

            if (function_exists('imagecreatetruecolor')) {
                $width = 800;
                $height = 600;
                $img = imagecreatetruecolor($width, $height);

                $colors = [
                    imagecolorallocate($img, 66, 135, 245),
                    imagecolorallocate($img, 245, 66, 135),
                    imagecolorallocate($img, 66, 245, 135),
                    imagecolorallocate($img, 245, 200, 66),
                    imagecolorallocate($img, 135, 66, 245),
                ];

                $bgColor = $colors[$index % count($colors)];
                imagefilledrectangle($img, 0, 0, $width, $height, $bgColor);

                $textColor = imagecolorallocate($img, 255, 255, 255);
                $font = 5;
                $text = $productName;

                $textWidth = imagefontwidth($font) * strlen($text);
                $textHeight = imagefontheight($font);
                $x = ($width - $textWidth) / 2;
                $y = ($height - $textHeight) / 2;

                imagestring($img, $font, $x, $y, $text, $textColor);

                imagejpeg($img, $savePath, 80);
                imagedestroy($img);

                return 'images/' . $filename;
            }

            return null;
        } catch (\Exception $e) {
            return null;
        }
    }
}