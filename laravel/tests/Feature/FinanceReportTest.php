<?php

namespace Tests\Feature;

use App\Models\Order;
use App\Models\OrderItem;
use App\Models\Product;
use App\Models\Tenant;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Support\Carbon;
use Tests\TestCase;

class FinanceReportTest extends TestCase
{
    use RefreshDatabase;

    public function test_guest_is_redirected_from_finance_report_page(): void
    {
        $response = $this->get(route('dashboard.reports.index'));
        $response->assertRedirect(route('login'));
    }

    public function test_customer_cannot_access_finance_report_page(): void
    {
        $customer = User::factory()->create([
            'role' => 'customer',
        ]);

        $response = $this->actingAs($customer)->get(route('dashboard.reports.index'));
        $response->assertRedirect(route('home'));
    }

    public function test_admin_can_access_finance_report_and_view_revenue(): void
    {
        $admin = User::factory()->create([
            'role' => 'admin',
        ]);

        $tenantUser = User::factory()->create(['role' => 'tenant']);
        $tenant = Tenant::create([
            'user_id'     => $tenantUser->id,
            'name'        => 'Warung Mantap',
            'slug'        => 'warung-mantap',
            'location'    => 'Blok A1',
            'status'      => 'active',
            'open_at'     => '07:00:00',
            'close_at'    => '15:00:00',
        ]);

        // Create completed order
        $order = Order::create([
            'tenant_id'       => $tenant->id,
            'order_number'    => 'ORD-20261107-001',
            'customer_name'   => 'Siswa Budi',
            'customer_class'  => '10 PPLG 1',
            'status'          => 'completed',
            'payment_status'  => 'paid',
            'payment_method'  => 'cash',
            'subtotal'        => 20000,
            'discount'        => 0,
            'total'           => 20000,
        ]);

        $response = $this->actingAs($admin)->get(route('dashboard.reports.index', ['period' => 'today']));
        $response->assertStatus(200);
        $response->assertSee('Laporan &amp; Keuangan Kantin', false);
        $response->assertSee('20.000');
        $response->assertSee('ORD-20261107-001');
    }

    public function test_date_range_filter_calculates_correct_revenue(): void
    {
        $admin = User::factory()->create(['role' => 'admin']);
        $tenantUser = User::factory()->create(['role' => 'tenant']);
        $tenant = Tenant::create([
            'user_id'     => $tenantUser->id,
            'name'        => 'Kedai Kopi',
            'slug'        => 'kedai-kopi',
            'status'      => 'active',
            'open_at'     => '07:00:00',
            'close_at'    => '15:00:00',
        ]);

        // Order inside range
        $order1 = Order::create([
            'tenant_id'       => $tenant->id,
            'order_number'    => 'ORD-NOV-01',
            'customer_name'   => 'Guru Ahmad',
            'customer_class'  => 'Guru (Ruang Guru)',
            'status'          => 'completed',
            'payment_status'  => 'paid',
            'payment_method'  => 'qris',
            'subtotal'        => 35000,
            'discount'        => 5000,
            'total'           => 30000,
        ]);
        $order1->created_at = Carbon::create(2026, 11, 5, 10, 0, 0);
        $order1->saveQuietly();

        // Order outside range (in October)
        $order2 = Order::create([
            'tenant_id'       => $tenant->id,
            'order_number'    => 'ORD-OCT-01',
            'customer_name'   => 'Siswa Lain',
            'customer_class'  => '11 TKJ',
            'status'          => 'completed',
            'payment_status'  => 'paid',
            'payment_method'  => 'cash',
            'subtotal'        => 50000,
            'discount'        => 0,
            'total'           => 50000,
        ]);
        $order2->created_at = Carbon::create(2026, 10, 10, 10, 0, 0);
        $order2->saveQuietly();

        $response = $this->actingAs($admin)->get(route('dashboard.reports.index', [
            'period'     => 'custom',
            'start_date' => '2026-11-01',
            'end_date'   => '2026-11-07',
        ]));

        $response->assertStatus(200);
        $response->assertSee('ORD-NOV-01');
        $response->assertDontSee('ORD-OCT-01');
        $response->assertSee('30.000');
    }

    public function test_admin_can_access_report_with_order_items_and_view_top_products(): void
    {
        $admin = User::factory()->create(['role' => 'admin']);
        $tenantUser = User::factory()->create(['role' => 'tenant']);
        $tenant = Tenant::create([
            'user_id'     => $tenantUser->id,
            'name'        => 'Warung Berkah',
            'slug'        => 'warung-berkah',
            'status'      => 'active',
            'open_at'     => '07:00:00',
            'close_at'    => '15:00:00',
        ]);

        $product = Product::create([
            'tenant_id'    => $tenant->id,
            'name'         => 'Nasi Ayam Penyet Spesial',
            'slug'         => 'nasi-ayam-penyet-spesial',
            'price'        => 15000,
            'stock'        => 10,
            'is_available' => true,
            'category'     => 'makanan_berat',
        ]);

        $order = Order::create([
            'tenant_id'       => $tenant->id,
            'order_number'    => 'ORD-ITEMS-01',
            'customer_name'   => 'Siti Rahma',
            'customer_class'  => '10 PPLG 2',
            'status'          => 'completed',
            'payment_status'  => 'paid',
            'payment_method'  => 'cash',
            'subtotal'        => 30000,
            'discount'        => 0,
            'total'           => 30000,
        ]);

        OrderItem::create([
            'order_id'     => $order->id,
            'product_id'   => $product->id,
            'product_name' => 'Nasi Ayam Penyet Spesial',
            'price'        => 15000,
            'quantity'     => 2,
            'subtotal'     => 30000,
        ]);

        $response = $this->actingAs($admin)->get(route('dashboard.reports.index', ['period' => 'today']));
        $response->assertStatus(200);
        $response->assertSee('Nasi Ayam Penyet Spesial');
        $response->assertSee('30.000');
    }

    public function test_admin_can_access_print_pdf_view(): void
    {
        $admin = User::factory()->create(['role' => 'admin']);

        $response = $this->actingAs($admin)->get(route('dashboard.reports.print', ['period' => 'today']));
        $response->assertStatus(200);
        $response->assertSee('Cetak / Simpan PDF Sekarang', false);
        $response->assertSee('KANTINKITA — LAPORAN REKAPITULASI KEUANGAN', false);
    }

    public function test_dashboard_date_calculator_calculates_income(): void
    {
        $admin = User::factory()->create(['role' => 'admin']);

        $response = $this->actingAs($admin)->get(route('dashboard.index', [
            'calc_date_from' => '2026-11-01',
            'calc_date_to'   => '2026-11-07',
        ]));

        $response->assertStatus(200);
        $response->assertSee('Hitung Penghasilan Berdasarkan Tanggal');
        $response->assertSee('Laporan Keuangan');
    }
}
