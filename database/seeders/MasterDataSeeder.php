<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use App\Models\Product;
use App\Models\Variant;
use App\Models\Warehouse;
use App\Models\Rak;

class MasterDataSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        // 1. Create Warehouse
        $warehouse = Warehouse::create([
            'name' => 'Gudang Utama Jakarta',
            'location' => 'Jakarta Selatan',
        ]);

        // 2. Create Raks (Shelves)
        $raks = [
            ['name' => 'Rak A1', 'location_code' => 'A1-01', 'warehouse_id' => $warehouse->id],
            ['name' => 'Rak A2', 'location_code' => 'A2-01', 'warehouse_id' => $warehouse->id],
            ['name' => 'Rak B1', 'location_code' => 'B1-01', 'warehouse_id' => $warehouse->id],
            ['name' => 'Area Display', 'location_code' => 'DSP-01', 'warehouse_id' => $warehouse->id],
        ];

        foreach ($raks as $rakData) {
            Rak::create($rakData);
        }

        // 3. Create Products and Variants
        $products = [
            [
                'name' => 'Nike Air Jordan 1',
                'description' => 'Sepatu basket klasik',
                'price' => 2500000,
                'variants' => [
                    ['color' => 'Red/Black', 'size' => '40'],
                    ['color' => 'Red/Black', 'size' => '41'],
                    ['color' => 'Red/Black', 'size' => '42'],
                    ['color' => 'White/Blue', 'size' => '40'],
                    ['color' => 'White/Blue', 'size' => '42'],
                ]
            ],
            [
                'name' => 'Adidas Ultraboost',
                'description' => 'Sepatu lari nyaman',
                'price' => 1800000,
                'variants' => [
                    ['color' => 'Black', 'size' => '39'],
                    ['color' => 'Black', 'size' => '40'],
                    ['color' => 'Black', 'size' => '41'],
                    ['color' => 'Grey', 'size' => '40'],
                ]
            ],
            [
                'name' => 'Converse Chuck Taylor',
                'description' => 'Sepatu kasual harian',
                'price' => 750000,
                'variants' => [
                    ['color' => 'Black/White', 'size' => '38'],
                    ['color' => 'Black/White', 'size' => '39'],
                    ['color' => 'Black/White', 'size' => '40'],
                ]
            ],
        ];

        foreach ($products as $prodData) {
            $product = Product::create([
                'name' => $prodData['name'],
                'description' => $prodData['description'],
                'price_sell' => $prodData['price'],
                'price_buy' => 0, // Default
            ]);

            foreach ($prodData['variants'] as $varData) {
                Variant::create([
                    'product_id' => $product->id,
                    'color' => $varData['color'],
                    'size' => $varData['size'],
                ]);
            }
        }
    }
}
