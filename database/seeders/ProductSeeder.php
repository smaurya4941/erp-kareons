<?php

namespace Database\Seeders;

use App\Models\Product;
use App\Services\ProductService;
use Illuminate\Database\Seeder;

class ProductSeeder extends Seeder
{
    /**
     * Seed the products table with name-only products.
     * Other details are left null for the admin to fill in later.
     */
    public function run(): void
    {
        $products = [
            'Stress Cure',
            'Asthi Sukh Oil',
            'Asthi Sukh Roll On',
            'Provit',
            'Draksha Cough Syrup',
            'Gyno Care Intimate Wash',
            'Gyno Care Intimate Cream',
            'Liv Cure DS',
            'Acetosum',
            'Shatpushpadi Tailam',
            'Sting Guard',
            'Hepato Care',
            'D-Tan Lotion',
            'Karekesh Hair Oil',
            'Karekesh Shampoo',
            'Karekesh Conditioner',
            'Saffron Hydrating Face Serum',
            'Burn Care Cream',
            'Kare Hb Syrup',
            'Kare Hb Tablet',
            'Women Bliss Plus',
        ];

        $service = app(ProductService::class);

        foreach ($products as $name) {
            // Skip if it already exists so the seeder is safe to re-run.
            if (Product::where('name', $name)->exists()) {
                continue;
            }

            $service->createProduct(['name' => $name]);
        }
    }
}
