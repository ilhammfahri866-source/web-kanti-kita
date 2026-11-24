<?php

namespace Tests\Feature;

use App\Models\Order;
use App\Models\Product;
use App\Models\Tenant;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Support\Carbon;
use Illuminate\Support\Facades\Auth;
use Tests\TestCase;

class StudentAuthAndCheckoutTest extends TestCase
{
    use RefreshDatabase;

    public function test_student_can_register_and_is_automatically_logged_in(): void
    {
        $response = $this->post('/daftar', [
            'name'                  => 'Rian Pratama',
            'classroom'             => '10 TO 1',
            'email'                 => 'rian@ekantin.test',
            'password'              => 'password123',
            'password_confirmation' => 'password123',
        ]);

        $response->assertRedirect('/');
        $response->assertSessionHas('success');

        $this->assertDatabaseHas('users', [
            'email'     => 'rian@ekantin.test',
            'name'      => 'Rian Pratama',
            'classroom' => '10 TO 1',
            'role'      => 'customer',
        ]);

        $this->assertTrue(Auth::check());
        $this->assertEquals('rian@ekantin.test', Auth::user()->email);
    }

    public function test_student_registration_requires_mandatory_fields(): void
    {
        $response = $this->post('/daftar', [
            'name'                  => '',
            'classroom'             => '',
            'email'                 => 'not-an-email',
            'password'              => 'short',
            'password_confirmation' => 'mismatch',
        ]);

        $response->assertSessionHasErrors(['name', 'classroom', 'email', 'password']);
    }

    public function test_student_can_login_with_email_or_student_name(): void
    {
        $student = User::factory()->create([
            'name'      => 'Budi Siswa',
            'email'     => 'budisiswa@ekantin.test',
            'password'  => bcrypt('password123'),
            'role'      => 'customer',
            'classroom' => '12 TPFL 1',
        ]);

        // Login using email
        $resEmail = $this->post('/masuk', [
            'email'    => 'budisiswa@ekantin.test',
            'password' => 'password123',
        ]);
        $resEmail->assertRedirect('/');
        $this->assertTrue(Auth::check());
        $this->assertEquals($student->id, Auth::id());

        Auth::logout();

        // Login using student name
        $resName = $this->post('/masuk', [
            'email'    => 'Budi Siswa',
            'password' => 'password123',
        ]);
        $resName->assertRedirect('/');
        $this->assertTrue(Auth::check());
        $this->assertEquals($student->id, Auth::id());
    }

    public function test_guest_cannot_checkout_and_receives_require_login_response(): void
    {
        $tenantUser = User::factory()->create(['role' => 'tenant']);
        $tenant = Tenant::create([
            'user_id' => $tenantUser->id,
            'name'    => 'Kantin Sekolah',
            'slug'    => 'kantin-sekolah',
            'status'  => 'active',
        ]);
        $product = Product::create([
            'tenant_id'    => $tenant->id,
            'name'         => 'Nasi Goreng Spesial',
            'slug'         => 'nasi-goreng-spesial',
            'price'        => 15000,
            'stock'        => 20,
            'category'     => 'makanan_berat',
            'is_available' => true,
        ]);

        Carbon::setTestNow('2026-10-23 09:45:00'); // Sesi istirahat 1 kantin buka

        $response = $this->postJson(route('checkout.store'), [
            'items'          => [
                ['id' => $product->id, 'quantity' => 1],
            ],
            'customer_name'  => 'Guest Siswa',
            'customer_class' => '10 TO 1',
            'payment_method' => 'cash',
        ]);

        $response->assertStatus(401);
        $response->assertJson([
            'success'       => false,
            'require_login' => true,
        ]);

        $this->assertDatabaseCount('orders', 0);

        Carbon::setTestNow();
    }

    public function test_authenticated_student_can_checkout_successfully(): void
    {
        $student = User::factory()->create([
            'name'      => 'Citra Siswi',
            'email'     => 'citra@ekantin.test',
            'role'      => 'customer',
            'classroom' => '11 Animasi 2',
        ]);

        $tenantUser = User::factory()->create(['role' => 'tenant']);
        $tenant = Tenant::create([
            'user_id' => $tenantUser->id,
            'name'    => 'Kantin Bu Joko',
            'slug'    => 'kantin-bu-joko',
            'status'  => 'active',
        ]);
        $product = Product::create([
            'tenant_id'    => $tenant->id,
            'name'         => 'Mie Goreng Ayam',
            'slug'         => 'mie-goreng-ayam',
            'price'        => 12000,
            'stock'        => 15,
            'category'     => 'makanan_berat',
            'is_available' => true,
        ]);

        Carbon::setTestNow('2026-10-23 09:45:00'); // Istirahat buka

        $response = $this->actingAs($student)->postJson(route('checkout.store'), [
            'items'          => [
                ['id' => $product->id, 'quantity' => 2],
            ],
            'customer_name'  => 'Citra Siswi',
            'customer_class' => '11 Animasi 2',
            'payment_method' => 'cash',
        ]);

        $response->assertStatus(200);
        $response->assertJson([
            'success' => true,
        ]);

        $order = Order::where('user_id', $student->id)->first();
        $this->assertNotNull($order);
        $this->assertEquals('Citra Siswi', $order->customer_name);
        $this->assertEquals('11 Animasi 2', $order->customer_class);
        $this->assertEquals(24000, (int)$order->total);
        $this->assertEquals('pending', $order->status);

        // Stok produk berkurang 2 (15 - 2 = 13)
        $this->assertEquals(13, $product->fresh()->stock);

        Carbon::setTestNow();
    }
}
