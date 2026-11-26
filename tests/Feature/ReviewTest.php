<?php

namespace Tests\Feature;

use App\Models\Category;
use App\Models\Order;
use App\Models\OrderItem;
use App\Models\PaymentMethod;
use App\Models\Product;
use App\Models\Review;
use App\Models\User;
use Database\Seeders\CategorySeeder;
use Database\Seeders\StoreDataSeeder;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class ReviewTest extends TestCase
{
    use RefreshDatabase;

    private User $customer;
    private User $admin;
    private Product $product;

    protected function setUp(): void
    {
        parent::setUp();
        $this->seed(CategorySeeder::class);
        $this->seed(StoreDataSeeder::class);

        $this->customer = User::where('role', 'pelanggan')->first();
        $this->admin = User::where('role', 'admin')->first();

        $category = Category::first();
        $this->product = Product::create([
            'category_id' => $category->id,
            'name' => 'Caramel Latte Special',
            'slug' => 'caramel-latte-special',
            'price' => 28000,
            'stock' => 20,
        ]);
    }

    public function test_customer_cannot_review_if_not_purchased_or_not_completed(): void
    {
        $response = $this->actingAs($this->customer)->post(route('reviews.store', $this->product->id), [
            'rating' => 5,
            'comment' => 'Kopi sangat enak!',
        ]);

        $response->assertSessionHas('error');
        $this->assertDatabaseMissing('reviews', ['product_id' => $this->product->id]);
    }

    public function test_customer_can_review_after_order_is_completed(): void
    {
        $pm = PaymentMethod::first();

        // Create completed order
        $order = Order::create([
            'order_number' => 'RSK-TEST-REV-1',
            'user_id' => $this->customer->id,
            'customer_name' => $this->customer->name,
            'customer_email' => $this->customer->email,
            'customer_phone' => '081234567890',
            'shipping_type' => 'pickup',
            'subtotal' => 28000,
            'total_amount' => 28000,
            'payment_method_id' => $pm->id,
            'payment_method_name' => $pm->name,
            'status' => 'selesai',
        ]);

        OrderItem::create([
            'order_id' => $order->id,
            'product_id' => $this->product->id,
            'product_name' => $this->product->name,
            'price' => 28000,
            'quantity' => 1,
            'subtotal' => 28000,
        ]);

        $response = $this->actingAs($this->customer)->post(route('reviews.store', $this->product->id), [
            'rating' => 5,
            'comment' => 'Kopi caramel terenak yang pernah saya coba!',
        ]);

        $response->assertSessionHas('success');
        $this->assertDatabaseHas('reviews', [
            'user_id' => $this->customer->id,
            'product_id' => $this->product->id,
            'order_id' => $order->id,
            'rating' => 5,
            'comment' => 'Kopi caramel terenak yang pernah saya coba!',
        ]);

        // Verify product average rating is now 5.0
        $this->product->refresh();
        $this->assertEquals(5.0, $this->product->average_rating);
        $this->assertEquals(1, $this->product->reviews_count);

        // Verify admin can toggle review approval
        $review = Review::where('product_id', $this->product->id)->first();
        $this->actingAs($this->admin)->post(route('admin.reviews.toggle', $review->id));
        $review->refresh();
        $this->assertFalse($review->is_approved);
    }
}
