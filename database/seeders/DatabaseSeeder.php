<?php

namespace Database\Seeders;

use App\Models\User;
use App\Models\Product;
use App\Models\ProductVariation;
use App\Models\Coupon;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\Hash;

class DatabaseSeeder extends Seeder
{
    /**
     * Seed the application's database.
     */
    public function run(): void
    {
        // 1. Seed Users for all roles
        $users = [
            [
                'name' => 'Admin User',
                'email' => 'admin@ecomm.com',
                'password' => Hash::make('password'),
                'role' => 'admin',
            ],
            [
                'name' => 'Outdoor Sales Agent',
                'email' => 'sales@ecomm.com',
                'password' => Hash::make('password'),
                'role' => 'outdoor_sales',
            ],
            [
                'name' => 'Inventory Purchaser',
                'email' => 'purchaser@ecomm.com',
                'password' => Hash::make('password'),
                'role' => 'purchaser',
            ],
            [
                'name' => 'Storekeeper Keeper',
                'email' => 'storekeeper@ecomm.com',
                'password' => Hash::make('password'),
                'role' => 'storekeeper',
            ],
            [
                'name' => 'John Customer',
                'email' => 'customer@ecomm.com',
                'password' => Hash::make('password'),
                'role' => 'customer',
            ],
        ];

        foreach ($users as $u) {
            User::updateOrCreate(['email' => $u['email']], $u);
        }

        // 2. Seed Coupons
        $coupons = [
            [
                'code' => 'WELCOME10',
                'discount_amount' => 10.00,
                'discount_type' => 'fixed',
                'min_spend' => 30.00,
                'is_active' => true,
            ],
            [
                'code' => 'SUNNAH20',
                'discount_amount' => 20.00,
                'discount_type' => 'percent',
                'min_spend' => 50.00,
                'is_active' => true,
            ],
        ];

        foreach ($coupons as $c) {
            Coupon::updateOrCreate(['code' => $c['code']], $c);
        }

        // 3. Seed Products and Variations
        // Product 1: Ajwa Dates
        $p1 = Product::updateOrCreate(
            ['name' => 'Premium Ajwa Dates (Al-Madinah)'],
            [
                'description' => 'Soft, dry variety of date fruit from Saudi Arabia. Known for its dark color, rich texture, and intense flavor. Directly imported from Al-Madinah.',
                'category' => 'dates',
                'base_price' => 30.00,
                'discount_price' => 25.00, // Active discount
                'stock' => 0, // Stock managed via variations
                'image_url' => '/images/products/ajwa_dates.jpg',
            ]
        );

        ProductVariation::updateOrCreate(
            ['product_id' => $p1->id, 'value' => '500g'],
            ['name' => 'Size', 'price' => 25.00, 'stock' => 45]
        );
        ProductVariation::updateOrCreate(
            ['product_id' => $p1->id, 'value' => '1kg'],
            ['name' => 'Size', 'price' => 45.00, 'stock' => 30]
        );

        // Product 2: Sidr Honey
        $p2 = Product::updateOrCreate(
            ['name' => 'Pure Organic Sidr Honey'],
            [
                'description' => 'Rare mono-floral honey made from the nectar of Sidr trees. Extremely rich in nutrients and antioxidants, with a uniquely delicious, warm taste.',
                'category' => 'honey',
                'base_price' => 50.00,
                'discount_price' => null,
                'stock' => 0, // Managed via variations
                'image_url' => '/images/products/sidr_honey.jpg',
            ]
        );

        ProductVariation::updateOrCreate(
            ['product_id' => $p2->id, 'value' => '250g'],
            ['name' => 'Weight', 'price' => 25.00, 'stock' => 15]
        );
        ProductVariation::updateOrCreate(
            ['product_id' => $p2->id, 'value' => '500g'],
            ['name' => 'Weight', 'price' => 48.00, 'stock' => 20]
        );
        ProductVariation::updateOrCreate(
            ['product_id' => $p2->id, 'value' => '1kg'],
            ['name' => 'Weight', 'price' => 85.00, 'stock' => 8] // Low stock!
        );

        // Product 3: Olive Oil
        Product::updateOrCreate(
            ['name' => 'Cold-Pressed Extra Virgin Olive Oil'],
            [
                'description' => 'Premium quality cold-pressed extra virgin olive oil. Rich in healthy monounsaturated fats and antioxidants. Perfect for salads, cooking, or raw consumption.',
                'category' => 'oil',
                'base_price' => 20.00,
                'discount_price' => 16.00, // Active discount
                'stock' => 75,
                'image_url' => '/images/products/olive_oil.jpg',
            ]
        );

        // Product 4: Dried Figs
        Product::updateOrCreate(
            ['name' => 'Sun-Dried Organic Figs'],
            [
                'description' => 'Naturally sun-dried premium organic figs. Soft, sweet, chewy, and packed with fiber, potassium, and magnesium. Free from artificial preservatives.',
                'category' => 'dried_fruit',
                'base_price' => 15.00,
                'discount_price' => null,
                'stock' => 35,
                'image_url' => '/images/products/dried_figs.jpg',
            ]
        );

        // Product 5: Taif Pomegranates
        Product::updateOrCreate(
            ['name' => 'Sweet Taif Pomegranates'],
            [
                'description' => 'Fresh, sweet, and juicy pomegranates sourced directly from the orchards of Taif. Celebrated for their vibrant ruby seeds and sweet flavor.',
                'category' => 'sunnah_fruit',
                'base_price' => 10.00,
                'discount_price' => 8.50, // Discount
                'stock' => 4, // Very low stock!
                'image_url' => '/images/products/pomegranates.jpg',
            ]
        );

        // Product 6: Black Seed Oil
        Product::updateOrCreate(
            ['name' => 'Premium Black Seed Oil (Habbatussauda)'],
            [
                'description' => '100% pure cold-pressed oil from Nigella Sativa (black cumin seeds). Celebrated for its immune-supporting qualities and overall wellness benefits.',
                'category' => 'oil',
                'base_price' => 24.00,
                'discount_price' => null,
                'stock' => 0, // Out of stock!
                'image_url' => '/images/products/black_seed_oil.jpg',
            ]
        );
    }
}
