<?php

namespace Tests\Feature;

use App\Models\Category;
use App\Models\Coupon;
use App\Models\Order;
use App\Models\PaymentMethod;
use App\Models\Product;
use App\Models\User;
use Database\Seeders\CategorySeeder;
use Database\Seeders\StoreDataSeeder;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Http\UploadedFile;
use Illuminate\Support\Facades\Storage;
use Tests\TestCase;

class OrderWorkflowTest extends TestCase
{
    use RefreshDatabase;

    private User $customer;
    private User $admin;
    private Category $category;
    private Product $product;
    private PaymentMethod $paymentMethod;

    protected function setUp(): void
    {
        parent::setUp();
        $this->seed(CategorySeeder::class);
        $this->seed(StoreDataSeeder::class);

        $this->customer = User::where('role', 'pelanggan')->first();
        $this->admin = User::where('role', 'admin')->first();

        $this->category = Category::first();
        $this->product = Product::create([
            'category_id' => $this->category->id,
            'name' => 'Latte Test Espresso',
            'slug' => 'latte-test-espresso',
            'description' => 'Latte testing',
            'price' => 25000,
            'stock' => 10,
        ]);

        $this->paymentMethod = PaymentMethod::where('code', 'bca')->first();
    }

    public function test_complete_order_and_transaction_lifecycle(): void
    {
        Storage::fake('public');

        // 1. Add product to cart
        $cartAddResponse = $this->actingAs($this->customer)
            ->post(route('cart.add', $this->product->id), [
                'quantity' => 2,
            ]);
        $cartAddResponse->assertSessionHas('success');

        // 2. View cart
        $cartViewResponse = $this->actingAs($this->customer)->get(route('cart.index'));
        $cartViewResponse->assertStatus(200);
        $cartViewResponse->assertSee('Latte Test Espresso');
        $cartViewResponse->assertSee('50.000'); // 2 * 25,000

        // 3. Apply coupon
        $couponResponse = $this->actingAs($this->customer)
            ->post(route('cart.coupon.apply'), [
                'coupon_code' => 'NGOPIHEMAT',
            ]);
        $couponResponse->assertSessionHas('success');

        // 4. Checkout Process
        $initialStock = $this->product->stock; // 10
        $checkoutResponse = $this->actingAs($this->customer)
            ->post(route('checkout.process'), [
                'customer_name' => $this->customer->name,
                'customer_email' => $this->customer->email,
                'customer_phone' => '081234567890',
                'shipping_type' => 'delivery',
                'shipping_address' => 'Jl. Sudirman Kav 20',
                'shipping_city' => 'Jakarta Pusat',
                'payment_method_id' => $this->paymentMethod->id,
                'customer_notes' => 'Tolong sediakan gula terpisah',
            ]);

        // Assert order created
        $order = Order::latest()->first();
        $this->assertNotNull($order);
        $checkoutResponse->assertRedirect(route('orders.confirmation', $order->order_number));

        // 5. Stock reduction verification
        $this->product->refresh();
        $this->assertEquals($initialStock - 2, $this->product->stock); // 10 - 2 = 8

        // 6. Verify calculations: Subtotal 50k, Diskon 10k, Ongkir 10k, Total 50k
        $this->assertEquals(50000, (float) $order->subtotal);
        $this->assertEquals(10000, (float) $order->discount_amount);
        $this->assertEquals(10000, (float) $order->shipping_cost);
        $this->assertEquals(50000, (float) $order->total_amount);

        // 7. Customer uploads payment proof
        $file = UploadedFile::fake()->image('bukti_transfer.jpg');
        $uploadResponse = $this->actingAs($this->customer)
            ->post(route('orders.payment-proof', $order->order_number), [
                'proof_image' => $file,
                'sender_name' => 'Budi Santoso',
                'sender_bank' => 'BCA',
            ]);
        $uploadResponse->assertSessionHas('success');

        $order->refresh();
        $this->assertEquals('dibayar', $order->status);
        $this->assertNotNull($order->payment->proof_image);

        // 8. Admin verifies payment
        $verifyResponse = $this->actingAs($this->admin)
            ->post(route('admin.orders.verify-payment', $order->id), [
                'action' => 'approve',
            ]);
        $verifyResponse->assertSessionHas('success');

        $order->refresh();
        $this->assertEquals('diproses', $order->status);
        $this->assertEquals('verified', $order->payment->status);

        // 9. Admin updates status to dikirim, then selesai
        $this->actingAs($this->admin)->patch(route('admin.orders.status', $order->id), [
            'status' => 'dikirim',
        ]);
        $order->refresh();
        $this->assertEquals('dikirim', $order->status);

        $this->actingAs($this->admin)->patch(route('admin.orders.status', $order->id), [
            'status' => 'selesai',
        ]);
        $order->refresh();
        $this->assertEquals('selesai', $order->status);

        // 10. Customer printable invoice
        $invoiceResponse = $this->actingAs($this->customer)
            ->get(route('orders.invoice', $order->order_number));
        $invoiceResponse->assertStatus(200);
        $invoiceResponse->assertSee($order->order_number);
        $invoiceResponse->assertSee('INVOICE');
    }

    public function test_cancelled_order_restores_product_stock(): void
    {
        // 1. Order 3 items
        $this->actingAs($this->customer)->post(route('cart.add', $this->product->id), [
            'quantity' => 3,
        ]);

        $this->actingAs($this->customer)->post(route('checkout.process'), [
            'customer_name' => $this->customer->name,
            'customer_email' => $this->customer->email,
            'customer_phone' => '081234567890',
            'shipping_type' => 'pickup',
            'pickup_time' => '15 Menit Lagi',
            'payment_method_id' => $this->paymentMethod->id,
        ]);

        $order = Order::latest()->first();
        $this->product->refresh();
        $this->assertEquals(7, $this->product->stock); // 10 - 3 = 7

        // 2. Customer cancels order
        $cancelResponse = $this->actingAs($this->customer)
            ->post(route('orders.cancel', $order->order_number));
        $cancelResponse->assertSessionHas('success');

        $order->refresh();
        $this->assertEquals('dibatalkan', $order->status);

        // 3. Stock restored to 10
        $this->product->refresh();
        $this->assertEquals(10, $this->product->stock);
    }
}
