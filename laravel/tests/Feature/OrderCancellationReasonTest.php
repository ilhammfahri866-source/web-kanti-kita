<?php

namespace Tests\Feature;

use App\Models\Order;
use App\Models\OrderItem;
use App\Models\Product;
use App\Models\Tenant;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class OrderCancellationReasonTest extends TestCase
{
    use RefreshDatabase;

    public function test_tenant_can_reject_order_with_cancellation_reason_and_restock(): void
    {
        $tenantUser = User::factory()->create(['role' => 'tenant']);
        $tenant = Tenant::create([
            'user_id' => $tenantUser->id,
            'name' => 'Kantin Enak',
            'slug' => 'kantin-enak',
            'status' => 'active',
        ]);

        $product = Product::create([
            'tenant_id' => $tenant->id,
            'name' => 'Nasi Goreng Spesial',
            'slug' => 'nasi-goreng-spesial',
            'price' => 15000,
            'stock' => 5,
            'is_available' => true,
            'category' => 'makanan_berat',
        ]);

        $customer = User::factory()->create(['role' => 'customer']);

        $order = Order::create([
            'user_id' => $customer->id,
            'tenant_id' => $tenant->id,
            'order_number' => 'ORD-TEST-001',
            'status' => 'pending',
            'total' => 30000,
            'pickup_time' => '10:00:00',
        ]);

        OrderItem::create([
            'order_id' => $order->id,
            'product_id' => $product->id,
            'product_name' => $product->name,
            'price' => $product->price,
            'quantity' => 2,
            'subtotal' => 30000,
        ]);

        // Tenant rejects order with reason
        $reasonText = 'Bahan habis untuk menu Nasi Goreng Spesial.';
        $response = $this->actingAs($tenantUser)
            ->patch(route('dashboard.orders.status', $order), [
                'status' => 'cancelled',
                'cancel_reason' => $reasonText,
            ]);

        $response->assertRedirect();
        $response->assertSessionHas('success');
        $response->assertSessionHas('order_cancelled_success');
        $this->assertStringContainsString('Berhasil tolak pesanan', session('success'));

        // Order updated in database
        $order->refresh();
        $this->assertEquals('cancelled', $order->status);
        $this->assertEquals($reasonText, $order->cancel_reason);

        // Product stock restored from 5 to 7
        $product->refresh();
        $this->assertEquals(7, $product->stock);
    }

    public function test_customer_can_see_cancellation_reason_on_order_tracking(): void
    {
        $tenantUser = User::factory()->create(['role' => 'tenant']);
        $tenant = Tenant::create([
            'user_id' => $tenantUser->id,
            'name' => 'Kantin Sejahtera',
            'slug' => 'kantin-sejahtera',
            'status' => 'active',
        ]);

        $customer = User::factory()->create(['role' => 'customer']);

        $order = Order::create([
            'user_id' => $customer->id,
            'tenant_id' => $tenant->id,
            'order_number' => 'ORD-TEST-002',
            'status' => 'cancelled',
            'cancel_reason' => 'Porsi sudah habis untuk jam istirahat ini.',
            'total' => 15000,
            'pickup_time' => '12:00:00',
        ]);

        $response = $this->actingAs($customer)->get(route('orders.index'));

        $response->assertStatus(200);
        $response->assertSee('ORD-TEST-002');
        $response->assertSee('Porsi sudah habis untuk jam istirahat ini.');
        $response->assertSee('Pesanan Ditolak / Dibatalkan');
    }

    public function test_tenant_can_confirm_order_and_receives_confirmation_text(): void
    {
        $tenantUser = User::factory()->create(['role' => 'tenant']);
        $tenant = Tenant::create([
            'user_id' => $tenantUser->id,
            'name' => 'Kantin Sejahtera',
            'slug' => 'kantin-sejahtera-2',
            'status' => 'active',
        ]);

        $customer = User::factory()->create(['role' => 'customer']);

        $order = Order::create([
            'user_id' => $customer->id,
            'tenant_id' => $tenant->id,
            'order_number' => 'ORD-TEST-CONFIRM',
            'status' => 'pending',
            'total' => 20000,
            'pickup_time' => '10:00:00',
        ]);

        $response = $this->actingAs($tenantUser)
            ->patch(route('dashboard.orders.status', $order), [
                'status' => 'confirmed',
            ]);

        $response->assertRedirect();
        $response->assertSessionHas('order_confirmed_success');
        $this->assertStringContainsString('telah dikonfirmasi', session('success'));

        $order->refresh();
        $this->assertEquals('confirmed', $order->status);

        // Customer sees confirmation banner on /pesanan-saya
        $trackingResponse = $this->actingAs($customer)->get(route('orders.index'));
        $trackingResponse->assertStatus(200);
        $trackingResponse->assertSee('Pesanan Telah Dikonfirmasi');
    }
}
