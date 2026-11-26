<?php

namespace Tests\Feature;

use App\Models\Category;
use App\Models\Product;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class CatalogTest extends TestCase
{
    use RefreshDatabase;

    private Category $category;
    private Product $product1;
    private Product $product2;

    protected function setUp(): void
    {
        parent::setUp();

        $this->category = Category::create([
            'name' => 'Signature Coffee',
            'slug' => 'signature-coffee',
            'description' => 'Kopi khas Rasa Kopi',
        ]);

        $this->product1 = Product::create([
            'category_id' => $this->category->id,
            'name' => 'Kopi Susu Aren Spesial',
            'slug' => 'kopi-susu-aren-spesial',
            'description' => 'Kopi susu dengan gula aren organik',
            'price' => 20000,
            'stock' => 15,
        ]);

        $this->product2 = Product::create([
            'category_id' => $this->category->id,
            'name' => 'Americano Dingin',
            'slug' => 'americano-dingin',
            'description' => 'Double espresso dengan es batu',
            'price' => 18000,
            'stock' => 5,
        ]);
    }

    public function test_catalog_displays_products(): void
    {
        $response = $this->get(route('catalog.index'));

        $response->assertStatus(200);
        $response->assertSee('Kopi Susu Aren Spesial');
        $response->assertSee('Americano Dingin');
        $response->assertSee('Rp 20.000');
    }

    public function test_catalog_search_filters_correctly(): void
    {
        $response = $this->get(route('catalog.index', ['q' => 'Aren']));

        $response->assertStatus(200);
        $response->assertSee('Kopi Susu Aren Spesial');
        $response->assertDontSee('Americano Dingin');
    }

    public function test_product_detail_page_works(): void
    {
        $response = $this->get(route('catalog.show', $this->product1->slug));

        $response->assertStatus(200);
        $response->assertSee('Kopi Susu Aren Spesial');
        $response->assertSee('Tambah ke Keranjang');
        $response->assertSee('Beli Sekarang');
    }

    public function test_wishlist_toggle_works(): void
    {
        $customer = User::create([
            'name' => 'Pelanggan Wishlist',
            'email' => 'wishlist@test.com',
            'password' => bcrypt('password'),
            'role' => 'pelanggan',
        ]);

        // 1. Add to wishlist
        $response = $this->actingAs($customer)->post(route('wishlist.toggle', $this->product1->id));
        $response->assertSessionHas('success');
        $this->assertDatabaseHas('wishlists', [
            'user_id' => $customer->id,
            'product_id' => $this->product1->id,
        ]);

        // 2. Remove from wishlist (toggle again)
        $response2 = $this->actingAs($customer)->post(route('wishlist.toggle', $this->product1->id));
        $response2->assertSessionHas('success');
        $this->assertDatabaseMissing('wishlists', [
            'user_id' => $customer->id,
            'product_id' => $this->product1->id,
        ]);
    }
}
