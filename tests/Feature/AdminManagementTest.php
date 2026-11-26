<?php

namespace Tests\Feature;

use App\Models\Category;
use App\Models\Coupon;
use App\Models\Product;
use App\Models\User;
use Database\Seeders\CategorySeeder;
use Database\Seeders\StoreDataSeeder;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Http\UploadedFile;
use Illuminate\Support\Facades\Storage;
use Tests\TestCase;

class AdminManagementTest extends TestCase
{
    use RefreshDatabase;

    private User $admin;
    private Category $category;

    protected function setUp(): void
    {
        parent::setUp();
        $this->seed(CategorySeeder::class);
        $this->seed(StoreDataSeeder::class);

        $this->admin = User::where('role', 'admin')->first();
        $this->category = Category::first();
    }

    public function test_admin_can_create_edit_and_delete_product(): void
    {
        Storage::fake('public');

        // Create
        $response = $this->actingAs($this->admin)->post(route('admin.products.store'), [
            'name' => 'Kopi Tubruk Bali Kintamani',
            'category_id' => $this->category->id,
            'price' => 22000,
            'stock' => 50,
            'description' => 'Kopi tubruk khas Bali dengan aroma jeruk',
            'image' => UploadedFile::fake()->image('kintamani.jpg'),
        ]);

        $response->assertRedirect(route('admin.products.index'));
        $this->assertDatabaseHas('products', [
            'name' => 'Kopi Tubruk Bali Kintamani',
            'price' => 22000,
            'stock' => 50,
        ]);

        $product = Product::where('name', 'Kopi Tubruk Bali Kintamani')->first();

        // Edit
        $updateResponse = $this->actingAs($this->admin)->put(route('admin.products.update', $product->id), [
            'name' => 'Kopi Tubruk Bali Kintamani Premium',
            'category_id' => $this->category->id,
            'price' => 25000,
            'stock' => 45,
            'description' => 'Updated description',
        ]);

        $updateResponse->assertRedirect(route('admin.products.index'));
        $this->assertDatabaseHas('products', [
            'id' => $product->id,
            'name' => 'Kopi Tubruk Bali Kintamani Premium',
            'price' => 25000,
        ]);

        // Delete
        $deleteResponse = $this->actingAs($this->admin)->delete(route('admin.products.destroy', $product->id));
        $deleteResponse->assertRedirect(route('admin.products.index'));
        $this->assertDatabaseMissing('products', ['id' => $product->id]);
    }

    public function test_admin_can_manage_categories(): void
    {
        // Create category
        $response = $this->actingAs($this->admin)->post(route('admin.categories.store'), [
            'name' => 'Dessert & Snack',
            'description' => 'Camilan pendamping kopi',
        ]);

        $response->assertRedirect(route('admin.categories.index'));
        $this->assertDatabaseHas('categories', ['name' => 'Dessert & Snack']);

        $category = Category::where('name', 'Dessert & Snack')->first();

        // Edit category
        $this->actingAs($this->admin)->put(route('admin.categories.update', $category->id), [
            'name' => 'Artisan Pastry & Cakes',
            'description' => 'Pastry lezat',
        ]);
        $this->assertDatabaseHas('categories', ['name' => 'Artisan Pastry & Cakes']);

        // Delete category
        $this->actingAs($this->admin)->delete(route('admin.categories.destroy', $category->id));
        $this->assertDatabaseMissing('categories', ['id' => $category->id]);
    }

    public function test_admin_can_adjust_stock_rapidly(): void
    {
        $product = Product::create([
            'category_id' => $this->category->id,
            'name' => 'Robusta Lampung',
            'slug' => 'robusta-lampung',
            'price' => 15000,
            'stock' => 5,
        ]);

        // Add 20 units
        $response = $this->actingAs($this->admin)->post(route('admin.stock.update', $product->id), [
            'action_type' => 'add',
            'stock_value' => 20,
        ]);
        $response->assertSessionHas('success');
        $product->refresh();
        $this->assertEquals(25, $product->stock);

        // Set to exactly 80 units
        $this->actingAs($this->admin)->post(route('admin.stock.update', $product->id), [
            'action_type' => 'set',
            'stock_value' => 80,
        ]);
        $product->refresh();
        $this->assertEquals(80, $product->stock);
    }

    public function test_admin_can_export_sales_report_csv(): void
    {
        $response = $this->actingAs($this->admin)->get(route('admin.reports.export', [
            'start_date' => now()->subDays(7)->toDateString(),
            'end_date' => now()->toDateString(),
            'status' => 'all',
        ]));

        $response->assertStatus(200);
        $response->assertHeader('content-type', 'text/csv; charset=UTF-8');
    }
}
