<?php

namespace Database\Seeders;

use App\Models\Order;
use App\Models\OrderItem;
use App\Models\Payment;
use App\Models\PaymentMethod;
use App\Models\Product;
use App\Models\Review;
use App\Models\User;
use Carbon\Carbon;
use Illuminate\Database\Seeder;

class OrderDemoSeeder extends Seeder
{
    public function run(): void
    {
        $customer = User::where('role', 'pelanggan')->first();
        if (! $customer) {
            return;
        }

        $bca = PaymentMethod::where('code', 'bca')->first();
        $cash = PaymentMethod::where('code', 'cash')->first();
        $kopiSusu = Product::where('slug', 'kopi-susu-gula-aren')->first();
        $cafeLatte = Product::where('slug', 'cafe-latte')->first();
        $americano = Product::where('slug', 'americano')->first();

        if (! $kopiSusu || ! $cafeLatte || ! $americano) {
            return;
        }

        // 1. Order Selesai 1
        if (! Order::where('order_number', 'RSK-20261120-DEM01')->exists()) {
            $order1 = Order::create([
                'order_number' => 'RSK-20261120-DEM01',
                'user_id' => $customer->id,
                'customer_name' => $customer->name,
                'customer_email' => $customer->email,
                'customer_phone' => $customer->phone ?? '081298765432',
                'shipping_type' => 'pickup',
                'pickup_time' => '15 Menit Lagi',
                'subtotal' => 40000,
                'discount_amount' => 10000,
                'coupon_code' => 'NGOPIHEMAT',
                'shipping_cost' => 0,
                'total_amount' => 30000,
                'payment_method_id' => $bca->id,
                'payment_method_name' => $bca->name,
                'status' => 'selesai',
                'created_at' => Carbon::now()->subDays(3),
            ]);

            OrderItem::create([
                'order_id' => $order1->id,
                'product_id' => $kopiSusu->id,
                'product_name' => $kopiSusu->name,
                'price' => $kopiSusu->price,
                'quantity' => 2,
                'subtotal' => 40000,
            ]);

            Payment::create([
                'order_id' => $order1->id,
                'amount' => 30000,
                'payment_method' => $bca->name,
                'sender_name' => 'Budi Santoso',
                'sender_bank' => 'BCA',
                'status' => 'verified',
                'verified_at' => Carbon::now()->subDays(3),
            ]);

            Review::updateOrCreate(
                ['order_id' => $order1->id, 'product_id' => $kopiSusu->id],
                [
                    'user_id' => $customer->id,
                    'rating' => 5,
                    'comment' => 'Kopi susu gula aren terenak! Manisnya pas, rasa kopinya tetap kuat dan creamy.',
                    'is_approved' => true,
                ]
            );
        }

        // 2. Order Selesai 2 (Delivery)
        if (! Order::where('order_number', 'RSK-20261122-DEM02')->exists()) {
            $order2 = Order::create([
                'order_number' => 'RSK-20261122-DEM02',
                'user_id' => $customer->id,
                'customer_name' => $customer->name,
                'customer_email' => $customer->email,
                'customer_phone' => $customer->phone ?? '081298765432',
                'shipping_type' => 'delivery',
                'shipping_address' => 'Jl. Senopati Raya No. 12',
                'shipping_city' => 'Jakarta Selatan',
                'subtotal' => 44000,
                'shipping_cost' => 10000,
                'total_amount' => 54000,
                'payment_method_id' => $bca->id,
                'payment_method_name' => $bca->name,
                'status' => 'selesai',
                'created_at' => Carbon::now()->subDays(2),
            ]);

            OrderItem::create([
                'order_id' => $order2->id,
                'product_id' => $cafeLatte->id,
                'product_name' => $cafeLatte->name,
                'price' => $cafeLatte->price,
                'quantity' => 2,
                'subtotal' => 44000,
            ]);

            Payment::create([
                'order_id' => $order2->id,
                'amount' => 54000,
                'payment_method' => $bca->name,
                'status' => 'verified',
                'verified_at' => Carbon::now()->subDays(2),
            ]);

            Review::updateOrCreate(
                ['order_id' => $order2->id, 'product_id' => $cafeLatte->id],
                [
                    'user_id' => $customer->id,
                    'rating' => 5,
                    'comment' => 'Latte-nya lembut dan foam susunya pas banget. Pengiriman kurir juga sangat cepat.',
                    'is_approved' => true,
                ]
            );
        }

        // 3. Order Menunggu Pembayaran
        if (! Order::where('order_number', 'RSK-20261125-DEM03')->exists()) {
            $order3 = Order::create([
                'order_number' => 'RSK-20261125-DEM03',
                'user_id' => $customer->id,
                'customer_name' => $customer->name,
                'customer_email' => $customer->email,
                'customer_phone' => $customer->phone ?? '081298765432',
                'shipping_type' => 'pickup',
                'pickup_time' => '30 Menit Lagi',
                'subtotal' => 18000,
                'shipping_cost' => 0,
                'total_amount' => 18000,
                'payment_method_id' => $bca->id,
                'payment_method_name' => $bca->name,
                'status' => 'menunggu_pembayaran',
                'created_at' => Carbon::now()->subHours(5),
            ]);

            OrderItem::create([
                'order_id' => $order3->id,
                'product_id' => $americano->id,
                'product_name' => $americano->name,
                'price' => $americano->price,
                'quantity' => 1,
                'subtotal' => 18000,
            ]);

            Payment::create([
                'order_id' => $order3->id,
                'amount' => 18000,
                'payment_method' => $bca->name,
                'status' => 'pending',
            ]);
        }
    }
}
