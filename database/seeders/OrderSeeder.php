<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use App\Models\Product;
use App\Models\Order;

class OrderSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        // Get a product from seller ID 1 (or change as needed)
        $product = Product::where('seller_id', 1)->first();

        if (!$product) {
            $this->command->error('❌ No product found for seller ID 1. Please create a product first.');
            return;
        }

        // Create multiple orders (duplicates for testing)
        Order::create([
            'product_id' => $product->id,
            'seller_id' => 1,
            'customer_name' => 'Alice Johnson',
            'customer_email' => 'alice@example.com',
            'customer_phone' => '+1234567890',
            'customer_address' => '123 Maple St, New York, USA',
            'quantity' => 1,
            'total_price' => $product->price,
            'status' => 'Pending',
            'created_at' => now()->subDays(5),
        ]);

        Order::create([
            'product_id' => $product->id,
            'seller_id' => 1,
            'customer_name' => 'Bob Smith',
            'customer_email' => 'bob@example.com',
            'customer_phone' => '+1987654321',
            'customer_address' => '456 Oak Ave, Los Angeles, USA',
            'quantity' => 2,
            'total_price' => $product->price * 2,
            'status' => 'Shipped',
            'created_at' => now()->subDays(3),
        ]);

        Order::create([
            'product_id' => $product->id,
            'seller_id' => 1,
            'customer_name' => 'Carol Davis',
            'customer_email' => 'carol@example.com',
            'customer_phone' => '+1122334455',
            'customer_address' => '789 Pine Rd, Chicago, USA',
            'quantity' => 1,
            'total_price' => $product->price,
            'status' => 'Delivered',
            'created_at' => now()->subDay(),
        ]);

        Order::create([
            'product_id' => $product->id,
            'seller_id' => 1,
            'customer_name' => 'David Wilson',
            'customer_email' => 'david@example.com',
            'customer_phone' => '+1555666777',
            'customer_address' => '321 Elm Blvd, Houston, USA',
            'quantity' => 3,
            'total_price' => $product->price * 3,
            'status' => 'Cancelled',
            'created_at' => now()->subDays(4),
        ]);

        $this->command->info('✅ Created 4 test orders (duplicates) for seller ID 1');
    }
}