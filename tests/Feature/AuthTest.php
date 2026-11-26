<?php

namespace Tests\Feature;

use App\Models\User;
use Database\Seeders\StoreDataSeeder;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Support\Facades\Hash;
use Tests\TestCase;

class AuthTest extends TestCase
{
    use RefreshDatabase;

    public function test_customer_can_register_successfully(): void
    {
        $response = $this->post(route('register'), [
            'name' => 'Siti Nurhaliza',
            'email' => 'siti@example.com',
            'phone' => '081234567899',
            'password' => 'secret123',
            'password_confirmation' => 'secret123',
        ]);

        $response->assertRedirect(route('home'));
        $this->assertAuthenticated();

        $user = User::where('email', 'siti@example.com')->first();
        $this->assertNotNull($user);
        $this->assertEquals('pelanggan', $user->role);
        $this->assertTrue(Hash::check('secret123', $user->password));
    }

    public function test_customer_can_login_and_logout(): void
    {
        $user = User::create([
            'name' => 'Doni Customer',
            'email' => 'doni@example.com',
            'phone' => '081299998888',
            'role' => 'pelanggan',
            'password' => Hash::make('password123'),
        ]);

        $response = $this->post(route('login'), [
            'email' => 'doni@example.com',
            'password' => 'password123',
        ]);

        $response->assertRedirect(route('home'));
        $this->assertAuthenticatedAs($user);

        $logoutResponse = $this->post(route('logout'));
        $logoutResponse->assertRedirect(route('home'));
        $this->assertGuest();
    }

    public function test_customer_cannot_access_admin_dashboard(): void
    {
        $customer = User::create([
            'name' => 'Biasa Customer',
            'email' => 'biasa@example.com',
            'role' => 'pelanggan',
            'password' => Hash::make('password'),
        ]);

        $response = $this->actingAs($customer)->get(route('admin.dashboard'));
        $response->assertStatus(403);
    }

    public function test_admin_can_access_admin_dashboard(): void
    {
        $admin = User::create([
            'name' => 'Super Admin',
            'email' => 'admin@test.com',
            'role' => 'admin',
            'password' => Hash::make('password'),
        ]);

        $response = $this->actingAs($admin)->get(route('admin.dashboard'));
        $response->assertStatus(200);
        $response->assertSee('Ringkasan Bisnis');
    }
}
